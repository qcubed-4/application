<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

require_once(dirname(dirname(__DIR__)) . '/i18n/i18n-lib.inc.php');
use QCubed\Application\t;

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Html;
use QCubed\Project\Application;
use QCubed\QString;
use QCubed\Type;
use QCubed as Q;

/**
 * Class ListBoxBase
 *
 * This will render an HTML DropDown or MultiSelect box [SELECT] element.
 * It extends {@link ListControl}.  By default, the number of visible rows is set to 1 and
 * the selection mode is set to single, creating a dropdown select box.
 *
 * @property integer $Rows          specifies how many rows you want to have shown.
 * @property string $LabelForRequired
 * @property string $LabelForRequiredUnnamed
 * @property string $SelectionMode SELECTION_MODE_* const specifies if this is a "Single" or "Multiple" select control.
 * @was QListBoxBase
 * @package QCubed\Control
 */
abstract class ListBoxBase extends ListControl
{
    /** Can select only one item. */
    const SELECTION_MODE_SINGLE = 'Single';
    /** Can select more than one */
    const SELECTION_MODE_MULTIPLE = 'Multiple';
    /** Selection mode not specified */
    const SELECTION_MODE_NONE = 'None';

///////////////////////////
    // Private Member Variables
    ///////////////////////////

    // APPEARANCE
    /** @var string Error to be shown if the box is empty, has a name and is marked as required */
    protected $strLabelForRequired;
    /** @var string Error to be shown If the box is empty, doesn't have a name and is marked as required */
    protected $strLabelForRequiredUnnamed;

    /**
     * ListBoxBase constructor.
     * @param ControlBase|FormBase $objParentObject
     * @param string|null $strControlId
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->strLabelForRequired = t('%s is required');
        $this->strLabelForRequiredUnnamed = t('Required');
        $this->objItemStyle = new ListItemStyle();
    }

    /**
     * Parses the data received back from the client/browser
     */
    public function parsePostData()
    {
        if (array_key_exists($this->strControlId, $_POST)) {
            if (is_array($_POST[$this->strControlId])) {
                // Multi-Select, so find them all.
                $this->setSelectedItemsById($_POST[$this->strControlId], false);
            } elseif ($_POST[$this->strControlId] === '') {
                $this->unselectAllItems(false);
            } else {
                // Single-select
                $this->setSelectedItemsById(array($_POST[$this->strControlId]), false);
            }
        } else {
            // Multiselect forms with nothing passed via $_POST means that everything was DE selected
            if ($this->SelectionMode == self::SELECTION_MODE_MULTIPLE) {
                $this->unselectAllItems(false);
            }
        }
    }

    /**
     * Returns the HTML-Code for a single Item
     *
     * @param ListItem $objItem
     * @return string resulting HTML
     */
    protected function getItemHtml(ListItem $objItem)
    {
        // The Default Item Style
        if ($this->objItemStyle) {
            $objStyler = clone ($this->objItemStyle);
        } else {
            $objStyler = new ListItemStyle();
        }

        // Apply any Style Override (if applicable)
        if ($objStyle = $objItem->ItemStyle) {
            $objStyler->override($objStyle);
        }

        $objStyler->setHtmlAttribute('value', ($objItem->Empty) ? '' : $objItem->Id);
        if ($objItem->Selected) {
            $objStyler->setHtmlAttribute('selected', 'selected');
        }

        $strHtml = Html::renderTag('option', $objStyler->renderHtmlAttributes(),
                QString::htmlEntities($objItem->Name), false, true) . _nl();

        return $strHtml;
    }

    /**
     * Returns the html for the entire control.
     * @return string
     */
    protected function getControlHtml()
    {
        // If no selection is specified, we select the first item, because once we draw this, that is what the browser
        // will consider selected on the screen.
        // We need to make sure that what we draw is mirrored in our current state
        if ($this->SelectionMode == self::SELECTION_MODE_SINGLE &&
            $this->SelectedIndex == -1 &&
            $this->ItemCount > 0
        ) {
            $this->SelectedIndex = 0;
        }

        if ($this->SelectionMode == self::SELECTION_MODE_MULTIPLE) {
            $attrOverride['name'] = $this->strControlId . "[]";
        } else {
            $attrOverride['name'] = $this->strControlId;
        }

        $strToReturn = $this->renderTag('select', $attrOverride, null, $this->renderInnerHtml());

        // If MultiSelect and if NOT required, add a "Reset" button to deselect everything
        if (($this->SelectionMode == self::SELECTION_MODE_MULTIPLE) && (!$this->blnRequired) && ($this->Enabled) && ($this->blnVisible)) {
            $strToReturn .= $this->getResetButtonHtml();
        }
        return $strToReturn;
    }

    /**
     * Return the inner html for the select box.
     * @return string
     */
    protected function renderInnerHtml()
    {
        $strHtml = '';
        $intItemCount = $this->getItemCount();
        if (!$intItemCount) {
            return '';
        }
        $groups = array();

        for ($intIndex = 0; $intIndex < $intItemCount; $intIndex++) {
            $objItem = $this->getItem($intIndex);
            // Figure Out Groups (if applicable)
            if ($strGroup = $objItem->ItemGroup) {
                $groups[$strGroup][] = $objItem;
            } else {
                $groups[''][] = $objItem;
            }
        }

        foreach ($groups as $strGroup => $items) {
            if (!$strGroup) {
                foreach ($items as $objItem) {
                    $strHtml .= $this->getItemHtml($objItem);
                }
            } else {
                $strGroupHtml = '';
                foreach ($items as $objItem) {
                    $strGroupHtml .= $this->getItemHtml($objItem);
                }
                $strHtml .= Html::renderTag('optgroup', ['label' => QString::htmlEntities($strGroup)],
                    $strGroupHtml);
            }
        }
        return $strHtml;
    }

    // For multiple-select based listboxes, you must define the way a "Reset" button should look
    abstract protected function getResetButtonHtml();

    /**
     * Determines whether the supplied input data is valid or not.
     * @return bool
     */
    public function validate()
    {
        if ($this->blnRequired) {
            if ($this->SelectedIndex == -1) {
                if ($this->strName) {
                    $this->ValidationError = sprintf($this->strLabelForRequired, $this->strName);
                } else {
                    $this->ValidationError = $this->strLabelForRequiredUnnamed;
                }
                return false;
            }

            if (($this->SelectedIndex == 0) && (strlen($this->SelectedValue) == 0)) {
                if ($this->strName) {
                    $this->ValidationError = sprintf($this->strLabelForRequired, $this->strName);
                } else {
                    $this->ValidationError = $this->strLabelForRequiredUnnamed;
                }
                return false;
            }
        }

        return true;
    }

    /**
     * Override of superclass that will update the selection using javascript so that the whole control does
     * not need to be redrawn.
     */
    protected function refreshSelection()
    {
        $items = $this->SelectedItems;
        $values = [];
        foreach ($items as $objItem) {
            $values[] = $objItem->Id;
        }
        Application::executeControlCommand($this->ControlId, 'val', $values);
    }

    /**
     * Restore the  state of the control. This override makes sure the item exists before putting it. Otherwise,
     * if the item did not exist, the default selection would be removed and nothing would be selected.
     * @param mixed $state
     */
    public function putState($state)
    {
        if (!empty($state['SelectedValues'])) {
            // assume only one selection in list
            $strValue = reset($state['SelectedValues']);
            if ($this->findItemByValue($strValue)) {
                $this->SelectedValues = [$strValue];
            }
        }
    }


    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * PHP magic function
     * @param string $strName
     *
     * @return mixed
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            // APPEARANCE
            case "Rows":
                return $this->getHtmlAttribute('size');
            case "LabelForRequired":
                return $this->strLabelForRequired;
            case "LabelForRequiredUnnamed":
                return $this->strLabelForRequiredUnnamed;

            // BEHAVIOR
            case "SelectionMode":
                return $this->hasHtmlAttribute('multiple') ? self::SELECTION_MODE_MULTIPLE : self::SELECTION_MODE_SINGLE;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /////////////////////////
    // Public Properties: SET
    /////////////////////////
    /**
     * PHP magic method
     * @param string $strName
     * @param string $mixValue
     *
     * @return void
     * @throws Caller|InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            // APPEARANCE
            case "Rows":
                try {
                    $this->setHtmlAttribute('size', Type::cast($mixValue, Type::INTEGER));
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "LabelForRequired":
                try {
                    $this->strLabelForRequired = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "LabelForRequiredUnnamed":
                try {
                    $this->strLabelForRequiredUnnamed = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            // BEHAVIOR
            case "SelectionMode":
                try {
                    if (Type::cast($mixValue, Type::STRING) == self::SELECTION_MODE_MULTIPLE) {
                        $this->setHtmlAttribute('multiple', 'multiple');
                    } else {
                        $this->removeHtmlAttribute('multiple');
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }

    /**
     * Returns an description of the options available to modify by the designer for the code generator.
     *
     * @return Q\ModelConnector\Param[]
     */
    public static function getModelConnectorParams()
    {
        return array_merge(parent::getModelConnectorParams(), array(
            new Q\ModelConnector\Param(get_called_class(), 'Rows', 'Height of field for multirow field',
                Type::INTEGER),
            new Q\ModelConnector\Param(get_called_class(), 'SelectionMode', 'Single or multiple selections',
                Q\ModelConnector\Param::SELECTION_LIST,
                array(
                    null => 'Default',
                    'self::SELECTION_MODE_SINGLE' => 'Single',
                    'self::SELECTION_MODE_MULTIPLE' => 'Multiple'
                ))
        ));
    }

    /**
     * Returns the generator corresponding to this control.
     *
     * @return Q\Codegen\Generator\GeneratorBase
     */
    public static function getCodeGenerator() {
        return new Q\Codegen\Generator\ListBox();
    }
}
