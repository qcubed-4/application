<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\QString;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed as Q;

/**
 * Class BlockControl
 *
 * This abstract class is designed to be a base for a class for a span and div control. It adds an additional drag and
 * drop support to these objects, as well as templating.
 *
 * @property string $Text is the HTML that you want rendered?
 * @property string $Format is a sprintf string that the Text property will be sent to for further formatting.
 * @property string $Template Path to the HTML template (.tpl.php) file (applicable in case a template is being used for Render)
 * @property boolean $AutoRenderChildren Render the child controls of this control automatically
 * @property string $TagName HTML tag to be used by the control (such as div or span)
 * @property boolean $HtmlEntities hold htmlentities be used on the contents of this control
 */
abstract class BlockControl extends Q\Project\Control\ControlBase
{
    ///////////////////////////
    // Private Member Variables
    ///////////////////////////

    // APPEARANCE
    /** @var string|null The text on the control */
    protected ?string $strText = null;
    /** @var string|null The format specifier for rendering the control */
    protected ?string $strFormat = null;
    /** @var string Path to the HTML template (.tpl.php) file (applicable in case a template is being used for Render) */
    protected string $strTemplate = '';
    /** @var bool Render the child controls of this control automatically? */
    protected ?bool $blnAutoRenderChildren = false;
    /** @var string HTML tag to be used by the control (such as div or span) */
    protected string $strTagName = '';
    /** @var bool Should htmlentities be used on the contents of this control? */
    protected bool $blnHtmlEntities = true;

    // BEHAVIOR
    /** @var bool Is it a drop target? */
 //   protected $blnDropTarget = false;

    // Move Targets and Drop Zones

    protected array|bool $objMovesControlsArray = array();
    protected array $objDropsControlsArray = array();
    protected array $objDropsGroupingsArray = array();
    protected array $objIsDropZoneFor = array();

    /**
     * Adds functionality to move a target control in response to this control's drag events.
     *
     * @param ControlBase|null $objTargetControl The control to move when this control is dragged. If null, no control is targeted.
     * @return void
     * @throws Caller
     */
    public function addControlToMove(?ControlBase $objTargetControl = null): void
    {
        $this->strJavaScripts = QCUBED_JQUI_JS;
        if ($objTargetControl && $objTargetControl->ControlId != $this->ControlId) {
            Application::executeJavascript(sprintf('var pos_%s = $j("#%s").offset()', $objTargetControl->ControlId,
                $objTargetControl->ControlId));
            Application::executeJavascript(sprintf('$j("#%s").on("drag",  function (ev, ui) { p = $j("#%s").offset(); p.left = pos_%s.left + ui.position.left; p.top = pos_%s.top + ui.position.top; $j("#%s").offset(p); } );',
                $this->strControlId, $objTargetControl->ControlId, $objTargetControl->ControlId,
                $objTargetControl->ControlId, $objTargetControl->ControlId));
            $this->objMovesControlsArray[$objTargetControl->ControlId] = true;

            // Replace ExecuteJavascript with this:
            //$this->addAttributeScript('qcubed', 'ctrlToMove', $objTargetControl->ControlId);
        }
        //return;
    }

    /**
     * Removes a control from the list of controls to be moved.
     *
     * @param ControlBase $objTargetControl The control to remove from the list.
     * @return void
     */
    public function removeControlToMove(ControlBase $objTargetControl): void
    {
        unset($this->objMovesControlsArray[$objTargetControl->ControlId]);
    }

    /**
     * Removes all controls designated to be moved and clears all drop zones.
     *
     * @return void
     */
    public function removeAllControlsToMove(): void
    {
        $this->objMovesControlsArray = array();
        $this->removeAllDropZones();
    }

    /**
     * Adds a drop zone to the specified parent object.
     *
     * @param object $objParentObject The parent object to which the drop zone will be added.
     * @return void
     */
    public function addDropZone(object $objParentObject): void
    {
        $this->strJavaScripts = QCUBED_JQUI_JS;
        $this->objDropsControlsArray[$objParentObject->ControlId] = true;
        $objParentObject->DropTarget = true;
        $objParentObject->objIsDropZoneFor[$this->ControlId] = true;
    }

    /**
     * Removes the drop zone associated with the specified parent object.
     *
     * @param mixed $objParentObject The parent object which can be either an instance of FormBase or BlockControl.
     *                               If it is a BlockControl, it will also remove the corresponding relationship.
     * @return void
     * @throws Caller If the parent object is not an instance of FormBase or BlockControl.
     */
    public function removeDropZone(mixed $objParentObject): void
    {
        if ($objParentObject instanceof FormBase) {
            $this->objDropsControlsArray[$objParentObject->FormId] = false;
        } else {
            if ($objParentObject instanceof BlockControl) {
                $this->objDropsControlsArray[$objParentObject->ControlId] = false;
                $objParentObject->objIsDropZoneFor[$this->ControlId] = false;
            } else {
                throw new Caller('ParentObject must be either a Form or BlockControl object');
            }
        }
    }

    /**
     * Removes all drop zones associated with the control, resetting configurations
     * and updating the relevant associations.
     *
     * @return void
     */
    public function removeAllDropZones(): void
    {
        Application::executeControlCommand($this->strControlId, 'draggable', "option", "revert", "invalid");

        foreach ($this->objDropsControlsArray as $strControlId => $blnValue) {
            if ($blnValue) {
                $objControl = $this->objForm->getControl($strControlId);
                if ($objControl && property_exists($objControl, 'objIsDropZoneFor')) {
                    $objControl->objIsDropZoneFor[$this->ControlId] = false;
                }
            }
        }

        $this->objDropsControlsArray = array();
    }

    /**
     * Returns the end script for the control, including handling for drop zones.
     *
     * @return string
     */

    public function getEndScript(): string
    {
        $strToReturn = parent::getEndScript();

        // DROP ZONES
        foreach ($this->objDropsControlsArray as $strKey => $blnIsDropZone) {
            if ($blnIsDropZone) {
                Application::executeControlCommand($strKey, 'droppable');
            }
        }

        foreach ($this->objIsDropZoneFor as $strKey => $blnIsDropZone) {
            if ($blnIsDropZone) {
                $objControl = $this->objForm->getControl($strKey);
                if ($objControl && ($objControl->strRenderMethod)) {
                    Application::executeControlCommand($this->strControlId, 'droppable', 'option', 'accept',
                        '#' . $strKey);
                }
            }
        }

        return $strToReturn;
    }

    //////////
    // Methods
    //////////
    /**
     * Parse the POST data and process it accordingly.
     *
     * @return void
     */
    public function parsePostData(): void
    {
    }


    /**
     * Returns the HTML of the Control
     * @return string The HTML string
     */
    protected function getControlHtml(): string
    {

        return $this->renderTag($this->strTagName,
            null,
            null,
            $this->getInnerHtml());

       /* $strToReturn = $this->renderTag($this->strTagName,
            null,
            null,
            $this->getInnerHtml());

//			if ($this->blnDropTarget)
//				$strToReturn .= sprintf('<span id="%s_ctldzmask" style="position:absolute;"><span style="font-size: 1px">&nbsp;</span></span>', $this->strControlId);

        return $strToReturn;*/
    }

    /**
     * Return the inner HTML between the tags.
     *
     * @return string
     */
    protected function getInnerHtml(): string
    {
        if ($this->strFormat) {
            $strText = sprintf($this->strFormat, $this->strText);
        } else {
            $strText = $this->strText;
        }

        if ($this->blnHtmlEntities) {
            $strText = QString::htmlEntities($strText);
        }

        $strTemplateEvaluated = '';
        if ($this->strTemplate) {
            global $_CONTROL;
            $objCurrentControl = $_CONTROL;
            $_CONTROL = $this;
            $strTemplateEvaluated = $this->evaluateTemplate($this->strTemplate);
            $_CONTROL = $objCurrentControl;
        }

        $strText .= $strTemplateEvaluated;

        if ($this->blnAutoRenderChildren) {
            $strText .= $this->renderChildren(false);
        }
        return $strText;
    }

    /**
     * Public function to be overridden by child classes
     *
     * It is used to determine if the input fed into the control is valid or not.
     * The rules are written in this function only. If the control is set for Validation,
     * this function is automatically called on postback.
     * @return bool Whether the input inside the control are valid
     */
    public function validate(): bool
    {
        return true;
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * PHP __get magic method implementation
     * @param string $strName Name of the property
     *
     * @return mixed
     * @throws Caller
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            // APPEARANCE
            case "Text":
                return $this->strText;
            case "Format":
                return $this->strFormat;
            case "Template":
                return $this->strTemplate;
            case "AutoRenderChildren":
                return $this->blnAutoRenderChildren;
            case "TagName":
                return $this->strTagName;
            case "HtmlEntities":
                return $this->blnHtmlEntities;

            // BEHAVIOR
            /*
            case "DropTarget":
                return $this->blnDropTarget;
*/
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
     * PHP __set magic method implementation
     * @param string $strName Property Name
     * @param mixed $mixValue Property Value
     *
     * @return void
     *@throws Caller|InvalidCast
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            // APPEARANCE
            case "Text":
                try {
                    if ($this->strText !== ($mixValue = Type::cast($mixValue, Type::STRING))) {
                        $this->blnModified = true;
                        $this->strText = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "Format":
                try {
                    if ($this->strFormat !== ($mixValue = Type::cast($mixValue, Type::STRING))) {
                        $this->blnModified = true;
                        $this->strFormat = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "Template":
                try {
                    $this->blnModified = true;
                    if ($mixValue) {
                        if (file_exists($strPath = $this->getTemplatePath($mixValue))) {
                            $this->strTemplate = Type::cast($strPath, Type::STRING);
                        } else {
                            throw new Caller('Could not find a template file: ' . $mixValue);
                        }
                    } else {
                        $this->strTemplate = null;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "AutoRenderChildren":
                try {
                    if ($this->blnAutoRenderChildren !== ($mixValue = Type::cast($mixValue,
                            Type::BOOLEAN))
                    ) {
                        $this->blnModified = true;
                        $this->blnAutoRenderChildren = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "TagName":
                try {
                    if ($this->strTagName !== ($mixValue = Type::cast($mixValue, Type::STRING))) {
                        $this->blnModified = true;
                        $this->strTagName = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "HtmlEntities":
                try {
                    if ($this->blnHtmlEntities !== ($mixValue = Type::cast($mixValue, Type::BOOLEAN))) {
                        $this->blnModified = true;
                        $this->blnHtmlEntities = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
/*
            case "DropTarget":
                try {
                    if ($this->blnDropTarget !== ($mixValue = Type::cast($mixValue, Type::BOOLEAN))) {
                        $this->blnModified = true;
                        $this->blnDropTarget = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
*/

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
     * Returns a description of the options available to modify by the designer for the code generator.
     *
     * @return Q\ModelConnector\Param[]
     */
    public static function getModelConnectorParams(): array
    {
        return array(
            new Q\ModelConnector\Param ('BlockControl', 'Text', 'Text to draw in the control', Type::STRING),
            new Q\ModelConnector\Param ('BlockControl', 'Format', 'Format string', Type::STRING),
            new Q\ModelConnector\Param ('BlockControl', 'Template', 'Path to template', Type::STRING),
            new Q\ModelConnector\Param ('BlockControl', 'AutoRenderChildren', 'Automatically render child controls?', Type::BOOLEAN),
            new Q\ModelConnector\Param ('BlockControl', 'TagName', 'The html tag', Type::STRING),
            new Q\ModelConnector\Param ('BlockControl', 'HtmlEntities', 'Should text content pass through html_entities?', Type::BOOLEAN)
        );

    }
}


// TODO: Move this somewhere else
//$_CONTROL = null;