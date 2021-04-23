<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Jqui;

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed as Q;
use QCubed\Control\ControlBase;
use QCubed\Type;

/**
 * Class TabsBase
 *
 * The QTabsBase class defined here provides an interface between the generated
 * QTabsGen class, and QCubed. This file is part of the core and will be overwritten
 * when you update QCubed. To override, make your changes to the QTabs.class.php file instead.
 *
 * Tabs are similar to an Accordion, but tabs along the top are used to switch between panels. The top
 * level html items in the panel will become the items that are switched.
 *
 * Specify the names of the tabs either in the TabHeadersArray, or assign a Name attribute to the top
 * level child controls and those names will be used as the tab names.
 *
 * @property-write array $Headers    Array of names for the tabs. You can also specify by assigning the Name attribute of each pane.
 * @property-read array $SelectedId    Control Id of the selected pane. Use ->Active to get the zero-based index of the selected pane.
 *
 * @link http://jqueryui.com/tabs/
 * @was QTabsBase
 * @package QCubed\Jqui
 */
class TabsBase extends TabsGen
{
    /** @var array Names of tabs. Can also specify with Name attribute of child controls. */
    protected $objTabHeadersArray = array();
    /** @var bool Automatically render the children by default, since these are the tabs. */
    protected $blnAutoRenderChildren = true;
    /** @var string ControlId of currently selected child item. Use ->Active to get the index of the current selection. */
    protected $strSelectedId = null;

    /**
     * Return the javascript associated with the control.
     */
    protected function makeJqWidget()
    {
        parent::makeJqWidget();
        Application::executeJsFunction('qcubed.tabs', $this->getJqControlId(), Application::PRIORITY_HIGH);
    }

    /**
     * Renders child controls as divs so that they become tabs.
     * @param bool $blnDisplayOutput
     * @return null|string
     */
    protected function renderChildren($blnDisplayOutput = true)
    {
        $strToReturn = $this->getTabHeaderHtml();

        foreach ($this->getChildControls() as $objControl) {
            if (!$objControl->Rendered) {
                $renderMethod = $objControl->strPreferredRenderMethod;
                $strToReturn .= Q\Html::renderTag('div', null, $objControl->$renderMethod($blnDisplayOutput));
            }
        }

        if ($blnDisplayOutput) {
            print($strToReturn);
            return null;
        } else {
            return $strToReturn;
        }
    }

    /**
     * Returns the HTML for the tab header. This includes the names and the control logic to record what the
     * user clicked.
     *
     * @return string
     */
    protected function getTabHeaderHtml()
    {
        $strHtml = '';
        $childControls = $this->getChildControls();
        for ($i = 0, $cnt = count($childControls); $i < $cnt; ++$i) {
            $strControlId = $childControls[$i]->ControlId;
            if (array_key_exists($key = $strControlId, $this->objTabHeadersArray) ||
                array_key_exists($key = $i, $this->objTabHeadersArray)
            ) {
                $objHeader = $this->objTabHeadersArray[$key];
                if ($objHeader instanceof Q\Control\ControlBase) {
                    $strText = $objHeader->getControlHtml();
                } else {
                    $strText = (string)$objHeader;
                }
            } elseif ($strName = $childControls[$i]->Name) {
                $strText = $strName;
            } else {
                $strText = 'Tab ' . ($i + 1);
            }
            $strAnchor = Q\Html::renderTag('a', ['href' => '#' . $strControlId], $strText, false, true);
            $strHtml .= Q\Html::renderTag('li', null, $strAnchor);
        }
        return Q\Html::renderTag('ul', null, $strHtml);
    }

    /**
     * Set the tab header for a tab
     *
     * Give it a control and a name to set the header
     *
     * TBD: impelment ajax fetch of tab content
     *
     * @param integer|ControlBase|string $mixHeaderIndicator either the 0-based index of the header, or the section control or that control's id
     * @param string|ControlBase $mixHeader string or control to render as the tab header
     * @return void
     */
    public function setHeader($mixHeaderIndicator, $mixHeader)
    {
        $key = ($mixHeaderIndicator instanceof ControlBase) ? $mixHeaderIndicator->ControlId : $mixHeaderIndicator;
        $this->objTabHeadersArray[$key] = $mixHeader;
    }

    /**
     * Generated method overrides the built-in Control method, causing it to not redraw completely. We restore
     * its functionality here.
     */
    public function refresh()
    {
        parent::refresh();
        ControlBase::refresh();
    }


    /**
     * Overrides default so that if a tab does not pass validation, it will be visible.
     * @return bool
     */
    public function validateControlAndChildren()
    {
        // Initially Assume Validation is True
        $blnToReturn = true;

        // Check the Control Itself
        if (!$this->validate()) {
            $blnToReturn = false;
        }

        // Recursive call on Child Controls
        $intControlNum = 0;

        foreach ($this->getChildControls() as $objChildControl) {
            // Only Enabled and Visible and Rendered controls should be validated
            if (($objChildControl->Visible) && ($objChildControl->Enabled) && ($objChildControl->RenderMethod) && ($objChildControl->OnPage)) {
                if (!$objChildControl->validateControlAndChildren()) {
                    $this->activateTab($intControlNum);
                    $blnToReturn = false;
                }
            }
            $intControlNum++;
        }

        return $blnToReturn;
    }

    /**
     * Given a tab name, index or control ID, returns its index. If invalid, returns false;
     * @param string|integer $mixTab
     * @return bool|int
     */
    protected function findTabIndex($mixTab)
    {
        if ($mixTab === null) {
            return false;
        }

        if ($this->objTabHeadersArray) {
            $count = count($this->objTabHeadersArray);
        } else {
            $childControls = $this->getChildControls();
            $count = count($childControls);
        }

        if (is_numeric($mixTab)) {
            if ($mixTab < $count) {
                return $mixTab; // assume numbers less than the index are index numbers
            }
        }

        // If there is a headers array, check for a name in there
        if ($this->objTabHeadersArray) {
            for ($i = 0, $cnt = $count; $i < $cnt; ++$i) {
                if ($this->objTabHeadersArray[$i] == $mixTab) {
                    return $i;
                }
            }
        }

        if (isset($childControls)) {
            for ($i = 0, $cnt = $count; $i < $cnt; ++$i) {
                $objControl = $childControls[$i];
                if ($mixTab == $objControl->Name) {
                    return $i;
                } elseif ($mixTab == $objControl->ControlId) {
                    return $i;
                }
            }
        }
        return false;
    }

    /**
     * Activate the tab with the given name, number or controlId.
     *
     * @param string|integer $mixTab The tab name, tab index number or control ID
     */
    public function activateTab($mixTab)
    {
        if (false !== ($i = $this->findTabIndex($mixTab))) {
            parent::option2('active', $i);
        }
    }

    /**
     * Enable or disable a tab, or all tabs.
     *
     * @param null|string|integer $mixTab If null, enables or disables all tabs. Otherwise, the name or index of a tab.
     * @param bool $blnEnable True to enable tabs. False to disable.
     */
    public function enableTab($mixTab = null, $blnEnable = true)
    {
        if (is_null($mixTab)) {
            if ($blnEnable) {
                parent::enable();
            } else {
                parent::disable();
            }
            return;
        }
        if (false !== ($i = $this->findTabIndex($mixTab))) {
            if ($blnEnable) {
                parent::enable1($i);
            } else {
                parent::disable1($i);
            }
        }
    }

    /**
     * Overriding to keep info in sync.
     * @param ControlBase $objControl
     */
    public function addChildControl(ControlBase $objControl)
    {
        parent::addChildControl($objControl);
        if (count($this->objChildControlArray) == 1) {
            $this->strSelectedId = $objControl->strControlId;    // default to first item added being selected
            $this->mixActive = 0;
        }
    }

    /**
     * Returns the state data to restore later.
     * @return mixed
     */
    protected function getState()
    {
        return ['active' => $this->Active, 'selectedId' => $this->strSelectedId];
    }

    /**
     * Restore the state of the control.
     * @param mixed $state
     */
    protected function putState($state)
    {
        if (isset($state['active'])) {
            $this->Active = $state['active'];
            $this->strSelectedId = $state['selectedId'];
        }
    }


    public function __get($strName)
    {
        switch ($strName) {
            case "SelectedId":
                return $this->strSelectedId;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case 'Headers':
                try {
                    $this->objTabHeadersArray = Type::cast($mixValue, Type::ARRAY_TYPE);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case '_active': // private method to synchronize with jQuery UI
                $this->mixActive = $mixValue[0];
                $this->strSelectedId = $mixValue[1];
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                    break;
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }
}
