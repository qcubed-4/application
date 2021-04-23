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
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class RadioButton
 *
 * This class will render an HTML Radio button.
 *
 * Based on a QCheckbox, which is very similar to a radio.
 *
 * @property string $Text is used to display text that is displayed next to the radio. The text is rendered as an html "Label For" the radio
 * @property string $TextAlign specifies if "Text" should be displayed to the left or to the right of the radio.
 * @property string $GroupName assigns the radio button into a radio button group (optional) so that no more than one radio in that group may be selected at a time.
 * @property boolean $HtmlEntities
 * @property boolean $Checked specifies whether or not the radio is selected
 *
 * @package QCubed\Control
 */
class RadioButtonBase extends CheckboxBase
{
    /**
     * Group to which this radio button belongs
     * Groups determine the 'radio' behavior wherein you can select only one option out of all buttons in that group
     * @var null|string Name of the group
     */
    protected $strGroupName = null;

    /**
     * Parse the data posted
     */
    public function parsePostData()
    {
        $val = $this->objForm->checkableControlValue($this->strControlId);
        $val = Type::cast($val, Type::BOOLEAN);
        $this->blnChecked = !empty($val);
    }

    /**
     * Returns the HTML code for the control which can be sent to the client.
     *
     * Note, previous version wrapped this in a div and made the control a block level control unnecessarily. To
     * achieve a block control, set blnUseWrapper and blnIsBlockElement.
     *
     * @return string THe HTML for the control
     */
    protected function getControlHtml()
    {
        if ($this->strGroupName) {
            $strGroupName = $this->strGroupName;
        } else {
            $strGroupName = $this->strControlId;
        }

        $attrOverride = array('type' => 'radio', 'name' => $strGroupName, 'value' => $this->strControlId);
        return $this->renderButton($attrOverride);
    }

    /**
     * Returns the current state of the control to be able to restore it later.
     * @return mixed
     */
    public function getState()
    {
        return array('Checked' => $this->Checked);
    }

    /**
     * Restore the state of the control.
     * @param mixed $state Previously saved state as returned by GetState above.
     */
    public function putState($state)
    {
        if (isset($state['Checked'])) {
            $this->Checked = $state['Checked'];
        }
    }


    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * PHP __get magic method implementation for the QRadioButton class
     * @param string $strName Name of the property
     *
     * @return mixed
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            // APPEARANCE
            case "GroupName":
                return $this->strGroupName;

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
     *
     * @param string $strName Name of the property
     * @param string $mixValue Value of the property
     *
     * @return void
     * @throws Caller|InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "GroupName":
                try {
                    $strGroupName = Type::cast($mixValue, Type::STRING);
                    if ($this->strGroupName != $strGroupName) {
                        $this->strGroupName = $strGroupName;
                        $this->blnModified = true;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "Checked":
                try {
                    $val = Type::cast($mixValue, Type::BOOLEAN);
                    if ($val != $this->blnChecked) {
                        $this->blnChecked = $val;
                        if ($this->GroupName && $val == true) {
                            Application::executeJsFunction('qcubed.setRadioInGroup', $this->strControlId);
                        } else {
                            $this->addAttributeScript('prop', 'checked', $val); // just set the one radio
                        }
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
}