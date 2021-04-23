<?php
namespace QCubed\Jqui;

use QCubed;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class SliderGen
 *
 * This is the SliderGen class which is automatically generated
 * by scraping the JQuery UI documentation website. As such, it includes all the options
 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
 * the SliderBase class for any glue code to make this class more
 * usable in QCubed.
 *
 * @see SliderBase
 * @package QCubed\Jqui
 * @property mixed $Animate
 * Whether to slide the handle smoothly when the user clicks on the
 * slider track. Also accepts any valid animation duration.Multiple types
 * supported:
 * 
 * 	* Boolean: When set to true, the handle will animate with the default
 * duration.
 * 	* String: The name of a speed, such as "fast" or "slow".
 * 	* Number: The duration of the animation, in milliseconds.
 * 

 *
 * @property mixed $Classes
 * Specify additional classes to add to the widgets elements. Any of
 * classes specified in the Theming section can be used as keys to
 * override their value. To learn more about this option, check out the
 * learn article about the classes option.

 *
 * @property boolean $Disabled
 * Disables the slider if set to true.
 *
 * @property integer $Max
 * The maximum value of the slider.
 *
 * @property integer $Min
 * The minimum value of the slider.
 *
 * @property string $Orientation
 * Determines whether the slider handles move horizontally (min on left,
 * max on right) or vertically (min on bottom, max on top). Possible
 * values: "horizontal", "vertical".
 *
 * @property mixed $Range
 * Whether the slider represents a range.Multiple types supported:
 * 
 * 	* Boolean: If set to true, the slider will detect if you have two
 * handles and create a styleable range element between these two.
 * 	* String: Either "min" or "max". A min range goes from the slider
 * min to one handle. A max range goes from one handle to the slider max.
 * 

 *
 * @property integer $Step
 * Determines the size or amount of each interval or step the slider
 * takes between the min and max. The full specified value range of the
 * slider (max - min) should be evenly divisible by the step.
 *
 * @property integer $Value
 * Determines the value of the slider, if theres only one handle. If
 * there is more than one handle, determines the value of the first
 * handle.
 *
 * @property array $Values
 * This option can be used to specify multiple handles. If the range
 * option is set to true, the length of values should be 2.
 *
 * @was QSliderGen

 */

class SliderGen extends QCubed\Control\Panel
{
    protected $strJavaScripts = QCUBED_JQUI_JS;
    protected $strStyleSheets = QCUBED_JQUI_CSS;
    /** @var mixed */
    protected $mixAnimate = null;
    /** @var mixed */
    protected $mixClasses = null;
    /** @var boolean */
    protected $blnDisabled = null;
    /** @var integer */
    protected $intMax = null;
    /** @var integer */
    protected $intMin;
    /** @var string */
    protected $strOrientation = null;
    /** @var mixed */
    protected $mixRange = null;
    /** @var integer */
    protected $intStep = null;
    /** @var integer */
    protected $intValue;
    /** @var array */
    protected $arrValues = null;

    /**
     * Builds the option array to be sent to the widget constructor.
     *
     * @return array key=>value array of options
     */
    protected function makeJqOptions() {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->Animate)) {$jqOptions['animate'] = $val;}
        if (!is_null($val = $this->Classes)) {$jqOptions['classes'] = $val;}
        if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
        if (!is_null($val = $this->Max)) {$jqOptions['max'] = $val;}
        if (!is_null($val = $this->Min)) {$jqOptions['min'] = $val;}
        if (!is_null($val = $this->Orientation)) {$jqOptions['orientation'] = $val;}
        if (!is_null($val = $this->Range)) {$jqOptions['range'] = $val;}
        if (!is_null($val = $this->Step)) {$jqOptions['step'] = $val;}
        if (!is_null($val = $this->Value)) {$jqOptions['value'] = $val;}
        if (!is_null($val = $this->Values)) {$jqOptions['values'] = $val;}
        return $jqOptions;
    }

    /**
     * Return the JavaScript function to call to associate the widget with the control.
     *
     * @return string
     */
    public function getJqSetupFunction()
    {
        return 'slider';
    }


    /**
     * Removes the slider functionality completely. This will return the
     * element back to its pre-init state.
     * 
     * 	* This method does not accept any arguments.
     */
    public function destroy()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", Application::PRIORITY_LOW);
    }
    /**
     * Disables the slider.
     * 
     * 	* This method does not accept any arguments.
     */
    public function disable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", Application::PRIORITY_LOW);
    }
    /**
     * Enables the slider.
     * 
     * 	* This method does not accept any arguments.
     */
    public function enable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", Application::PRIORITY_LOW);
    }
    /**
     * Retrieves the sliders instance object. If the element does not have an
     * associated instance, undefined is returned.
     * 
     * Unlike other widget methods, instance() is safe to call on any element
     * after the slider plugin has loaded.
     * 
     * 	* This method does not accept any arguments.
     */
    public function instance()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", Application::PRIORITY_LOW);
    }
    /**
     * Gets the value currently associated with the specified optionName.
     * 
     * Note: For options that have objects as their value, you can get the
     * value of a specific key by using dot notation. For example, "foo.bar"
     * would get the value of the bar property on the foo option.
     * 
     * 	* optionName Type: String The name of the option to get.
     * @param $optionName
     */
    public function option($optionName)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, Application::PRIORITY_LOW);
    }
    /**
     * Gets an object containing key/value pairs representing the current
     * slider options hash.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function option1()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", Application::PRIORITY_LOW);
    }
    /**
     * Sets the value of the slider option associated with the specified
     * optionName.
     * 
     * Note: For options that have objects as their value, you can set the
     * value of just one property by using dot notation for optionName. For
     * example, "foo.bar" would update only the bar property of the foo
     * option.
     * 
     * 	* optionName Type: String The name of the option to set.
     * 	* value Type: Object A value to set for the option.
     * @param $optionName
     * @param $value
     */
    public function option2($optionName, $value)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, $value, Application::PRIORITY_LOW);
    }
    /**
     * Sets one or more options for the slider.
     * 
     * 	* options Type: Object A map of option-value pairs to set.
     * @param $options
     */
    public function option3($options)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, Application::PRIORITY_LOW);
    }
    /**
     * Get the value of the slider.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function value()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "value", Application::PRIORITY_LOW);
    }
    /**
     * Set the value of the slider.
     * 
     * 	* value Type: Number The value to set.
     * @param $value
     */
    public function value1($value)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "value", $value, Application::PRIORITY_LOW);
    }
    /**
     * Get the value for all handles.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function values()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "values", Application::PRIORITY_LOW);
    }
    /**
     * Get the value for the specified handle.
     * 
     * 	* index Type: Integer The zero-based index of the handle.
     * @param $index
     */
    public function values1($index)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "values", $index, Application::PRIORITY_LOW);
    }
    /**
     * Set the value for the specified handle.
     * 
     * 	* index Type: Integer The zero-based index of the handle.
     * 	* value Type: Number The value to set.
     * @param $index
     * @param $value
     */
    public function values2($index, $value)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "values", $index, $value, Application::PRIORITY_LOW);
    }
    /**
     * Set the value for all handles.
     * 
     * 	* values Type: Array The values to set.
     * @param $values
     */
    public function values3($values)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "values", $values, Application::PRIORITY_LOW);
    }


    public function __get($strName)
    {
        switch ($strName) {
            case 'Animate': return $this->mixAnimate;
            case 'Classes': return $this->mixClasses;
            case 'Disabled': return $this->blnDisabled;
            case 'Max': return $this->intMax;
            case 'Min': return $this->intMin;
            case 'Orientation': return $this->strOrientation;
            case 'Range': return $this->mixRange;
            case 'Step': return $this->intStep;
            case 'Value': return $this->intValue;
            case 'Values': return $this->arrValues;
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
            case 'Animate':
                $this->mixAnimate = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'animate', $mixValue);
                break;

            case 'Classes':
                $this->mixClasses = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'classes', $mixValue);
                break;

            case 'Disabled':
                try {
                    $this->blnDisabled = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'disabled', $this->blnDisabled);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Max':
                try {
                    $this->intMax = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'max', $this->intMax);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Min':
                try {
                    $this->intMin = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'min', $this->intMin);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Orientation':
                try {
                    $this->strOrientation = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'orientation', $this->strOrientation);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Range':
                $this->mixRange = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'range', $mixValue);
                break;

            case 'Step':
                try {
                    $this->intStep = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'step', $this->intStep);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Value':
                try {
                    $this->intValue = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'value', $this->intValue);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Values':
                try {
                    $this->arrValues = Type::Cast($mixValue, Type::ARRAY_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'values', $this->arrValues);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }


            case 'Enabled':
                $this->Disabled = !$mixValue;	// Tie in standard QCubed functionality
                parent::__set($strName, $mixValue);
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

    /**
    * If this control is attachable to a codegenerated control in a ModelConnector, this function will be
    * used by the ModelConnector designer dialog to display a list of options for the control.
    * @return QModelConnectorParam[]
    **/
    public static function getModelConnectorParams()
    {
        return array_merge(parent::GetModelConnectorParams(), array(
            new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the slider if set to true.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'Max', 'The maximum value of the slider.', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Min', 'The minimum value of the slider.', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Orientation', 'Determines whether the slider handles move horizontally (min on left,max on right) or vertically (min on bottom, max on top). Possiblevalues: \"horizontal\", \"vertical\".', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'Step', 'Determines the size or amount of each interval or step the slidertakes between the min and max. The full specified value range of theslider (max - min) should be evenly divisible by the step.', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Value', 'Determines the value of the slider, if theres only one handle. Ifthere is more than one handle, determines the value of the firsthandle.', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Values', 'This option can be used to specify multiple handles. If the rangeoption is set to true, the length of values should be 2.', Type::ARRAY_TYPE),
        ));
    }
}
