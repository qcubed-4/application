<?php
namespace QCubed\Jqui;

use QCubed;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\ApplicationBase;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class SpinnerGen
 *
 * This is the SpinnerGen class that is automatically generated
 * by scraping the JQuery UI documentation website. As such, it includes all the options
 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
 * the SpinnerBase class for any glue code to make this class more
 * usable in QCubed.
 *
 * @see SpinnerBase
 * @package QCubed\Jqui
 * @property mixed $Classes
 * Specify additional classes to add to the widget elements. Any of
 * the classes specified in the Theming section can be used as keys to
 * override their value. To learn more about this option, check out the
 * learned article about the classes option.

 *
 * @property string $Culture
 * Sets the culture to use for parsing and formatting the value. If null,
 * the currently set culture in Globalize is used, see Globalize docs for
 * available cultures. Only relevant if the numberFormat option is set.
 * Requires Globalize to be included.
 *
 * @property boolean $Disabled
 * Disables the spinner if set to true.
 *
 * @property mixed $Icons
 * Icons to use for buttons, matching an icon provided by the jQuery UI
 * CSS Framework. 
 * 
 * 	* up (string, default: "ui-icon-triangle-1-n")
 * 	* down (string, default: "ui-icon-triangle-1-s")
 * 

 *
 * @property mixed $Incremental
 * Controls the number of steps taken when holding down a spin
 * button.Multiple types supported:
 * 
 * 	* Boolean: When set to true, the stepping delta will increase when
 * spun incessantly. When set to false, all steps are equal (as defined
 * by the step option).
 * 	* Function: Receives one parameter: the number of spins that have
 * occurred. We Must return the number of steps that should occur for the
 * current spin.
 * 

 *
 * @property mixed $Max
 * The maximum allowed value. The element max attribute is used if it
 *  exists, and the option is not explicitly set. If null, there is no
 * maximum enforced.Multiple types are supported:
 * 
 * 	* Number: The maximum value.
 * 	* String: If Globalize is included, the max option can be passed as
 * a string which will be parsed based on the numberFormat and culture
 * options; otherwise it will fall back to the native parseFloat()
 * method.
 * 

 *
 * @property mixed $Min
 * The minimum allowed value. The element min attribute is used if it
 *  exists, and the option is not explicitly set. If null, there is no
 * minimum enforced.Multiple types are supported:
 * 
 * 	* Number: The minimum value.
 * 	* String: If Globalize is included, the min option can be passed as
 * a string which will be parsed based on the numberFormat and culture
 * options; otherwise it will fall back to the native parseFloat()
 * method.
 * 

 *
 * @property string $NumberFormat
 * Format of numbers passed to Globalize, if available. The most common are
 * "n" for a decimal number and "C" for a currency value. Also see the
 * culture option.
 *
 * @property integer $Page
 * The number of steps to take when paging via the pageUp/pageDown
 * methods.
 *
 * @property mixed $Step
 * The size of the step to take when spinning via buttons or via the
 * stepUp()/stepDown() methods. The element step attribute is used if it
 *  exists, and the option is not explicitly set.Multiple types supported:
 * 
 * 	* Number: The size of the step.
 * 	* String: If Globalize is included, the step option can be passed as
 * a string which will be parsed based on the numberFormat and culture
 * options, otherwise it will fall back to the native parseFloat.
 * 

 *
 * @was QSpinnerGen

 */

class SpinnerGen extends QCubed\Project\Control\TextBox
{
    protected string $strJavaScripts = QCUBED_JQUI_JS;
    protected string $strStyleSheets = QCUBED_JQUI_CSS;
    /** @var mixed */
    protected mixed $mixClasses = null;
    /** @var string|null */
    protected ?string $strCulture = null;
    /** @var boolean */
    protected ?bool $blnDisabled = null;
    /** @var mixed */
    protected mixed $mixIcons = null;
    /** @var mixed */
    protected mixed $mixIncremental = null;
    /** @var mixed */
    protected mixed $mixMax = null;
    /** @var mixed */
    protected mixed $mixMin = null;
    /** @var string|null */
    protected ?string $strNumberFormat = null;
    /** @var integer|null */
    protected ?int $intPage = null;
    /** @var mixed */
    protected mixed $mixStep = null;

    /**
     * Builds the option array to be sent to the widget constructor.
     *
     * @return array key=>value array of options
     */
    protected function makeJqOptions(): array
    {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->Classes)) {$jqOptions['classes'] = $val;}
        if (!is_null($val = $this->Culture)) {$jqOptions['culture'] = $val;}
        if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
        if (!is_null($val = $this->Icons)) {$jqOptions['icons'] = $val;}
        if (!is_null($val = $this->Incremental)) {$jqOptions['incremental'] = $val;}
        if (!is_null($val = $this->Max)) {$jqOptions['max'] = $val;}
        if (!is_null($val = $this->Min)) {$jqOptions['min'] = $val;}
        if (!is_null($val = $this->NumberFormat)) {$jqOptions['numberFormat'] = $val;}
        if (!is_null($val = $this->Page)) {$jqOptions['page'] = $val;}
        if (!is_null($val = $this->Step)) {$jqOptions['step'] = $val;}
        return $jqOptions;
    }

    /**
     * Return the JavaScript function to call to associate the widget with the control.
     *
     * @return string
     */
    public function getJqSetupFunction(): string
    {
        return 'spinner';
    }

    /**
     * Removes the spinner functionality completely. This will return the
     * element back to its pre-init state.
     * 
     * 	* This method does not accept any arguments.
     */
    public function destroy(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", ApplicationBase::PRIORITY_LOW);
    }
    
    /**
     * Disables the spinner.
     * 
     * 	* This method does not accept any arguments.
     */
    public function disable(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Enables the spinner.
     * 
     * 	* This method does not accept any arguments.
     */
    public function enable(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", ApplicationBase::PRIORITY_LOW);
    }
    
    /**
     * Retrieves the spinner instance object. If the element does not have
     * an associated instance, undefined is returned.
     * 
     * Unlike other widget methods, instance() is safe to call on any element
     * after the spinner plugin has loaded.
     * 
     * 	* This method does not accept any arguments.
     */
    public function instance(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", ApplicationBase::PRIORITY_LOW);
    }
    
    /**
     * Returns whether the Spinners value is valid given its min, max, and
     * step.
     * 
     * 	* This method does not accept any arguments.
     */
    public function isValid(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "isValid", ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Gets the value currently associated with the specified optionName.
     *
     * Note: For options that have objects as their value, you can get the
     * value of a specific key by using dot notation. For example, "foo.bar"
     * would get the value of the bar property on the foo option.
     *
     *    * optionName Type: String The name of the option to get.
     * @param string $optionName
     */
    public function option(string $optionName): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, ApplicationBase::PRIORITY_LOW);
    }
    
    /**
     * Gets an object containing key/value pairs representing the current
     * spinner options hash.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function option1(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Sets the value of the spinner option associated with the specified
     * optionName.
     *
     * Note: For options that have objects as their value, you can set the
     * value of just one property by using dot notation for optionName. For
     * example, "foo.bar" would update only the bar property of the foo
     * option.
     *
     *    * optionName Type: String The name of the option to set.
     *    * value Type: Object A value to set for the option.
     * @param string $optionName
     * @param string $value
     */
    public function option2(string $optionName, string $value): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, $value, ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Sets one or more options for the spinner.
     *
     *    * options Type: Object A map of option-value pairs to set.
     * @param array $options
     */
    public function option3(array $options): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Decrements the value by the specified number of pages, as defined by
     * the page option. Without the parameter, a single page is decremented.
     *
     * If the resulting value is above the max, below the min, or results in
     * a step mismatch, the value will be adjusted to the closest valid
     * value.
     *
     * Invoking pageDown() will cause start, spin, and stop events to be
     * triggered.
     *
     *    * pages Type: Number of pages to decrement, defaults to 1.
     * @param int|null $pages
     */
    public function pageDown(?int $pages = null): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "pageDown", $pages, ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Increments the value by the specified number of pages, as defined by
     * the page option. Without the parameter, a single page is incremented.
     *
     * If the resulting value is above the max, below the min, or results in
     * a step mismatch, the value will be adjusted to the closest valid
     * value.
     *
     * Invoking pageUp() will cause to start, spin, and stop events from being
     * triggered.
     *
     *    * pages Type: Number of pages to increment, defaults to 1.
     * @param int|null $pages
     */
    public function pageUp(?int $pages = null): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "pageUp", $pages, ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Decrements the value by the specified number of steps. Without the
     * parameter, a single step is decremented.
     *
     * If the resulting value is above the max, below the min, or results in
     * a step mismatch, the value will be adjusted to the closest valid
     * value.
     *
     * Invoking stepDown() will cause start, spin, and stop events to be
     * triggered.
     *
     *    * steps Type: Number of steps to decrement, defaults to 1.
     * @param int|null $steps
     */
    public function stepDown(?int $steps = null): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "stepDown", $steps, ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Increments the value by the specified number of steps. Without the
     * parameter, a single step is incremented.
     *
     * If the resulting value is above the max, below the min, or results in
     * a step mismatch, the value will be adjusted to the closest valid
     * value.
     *
     * Invoking stepUp() will cause to start, spin, and stop events from being
     * triggered.
     *
     *    * steps Type: Number of steps to increment, defaults to 1.
     * @param int|null $steps
     */
    public function stepUp(?int $steps = null): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "stepUp", $steps, ApplicationBase::PRIORITY_LOW);
    }
    
    /**
     * Gets the current value as a number. The value is parsed based on the
     * numberFormat and culture options.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function value(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "value", ApplicationBase::PRIORITY_LOW);
    }

    /**
     * * value Type: Number or String The value to set. If passed as a
     * string, the value is parsed based on the numberFormat and culture
     * options.
     * @param string $value
     */
    public function value1(string $value): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "value", $value, ApplicationBase::PRIORITY_LOW);
    }


    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'Classes': return $this->mixClasses;
            case 'Culture': return $this->strCulture;
            case 'Disabled': return $this->blnDisabled;
            case 'Icons': return $this->mixIcons;
            case 'Incremental': return $this->mixIncremental;
            case 'Max': return $this->mixMax;
            case 'Min': return $this->mixMin;
            case 'NumberFormat': return $this->strNumberFormat;
            case 'Page': return $this->intPage;
            case 'Step': return $this->mixStep;
            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case 'Classes':
                $this->mixClasses = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'classes', $mixValue);
                break;

            case 'Culture':
                try {
                    $this->strCulture = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'culture', $this->strCulture);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Disabled':
                try {
                    $this->blnDisabled = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'disabled', $this->blnDisabled);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Icons':
                $this->mixIcons = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'icons', $mixValue);
                break;

            case 'Incremental':
                $this->mixIncremental = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'incremental', $mixValue);
                break;

            case 'Max':
                $this->mixMax = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'max', $mixValue);
                break;

            case 'Min':
                $this->mixMin = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'min', $mixValue);
                break;

            case 'NumberFormat':
                try {
                    $this->strNumberFormat = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'numberFormat', $this->strNumberFormat);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Page':
                try {
                    $this->intPage = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'page', $this->intPage);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Step':
                $this->mixStep = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'step', $mixValue);
                break;


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
     *
     * @throws Caller
     */
    public static function getModelConnectorParams(): array
    {
        return array_merge(parent::GetModelConnectorParams(), array(
            new QModelConnectorParam (get_called_class(), 'Culture', 'Sets the culture to use for parsing and formatting the value. If null,the currently set culture in Globalize is used, see Globalize docs for available cultures. Only relevant if the numberFormat option is set.Requires Globalize to be included.', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the spinner if set to true.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'NumberFormat', 'Format of numbers passed to Globalize, if available. The most common are\"n\ "for a decimal number and \"C\" for a currency value. Also see the culture option.', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'Page', 'The number of steps to take when paging via the pageUp/pageDown methods.', Type::INTEGER),
        ));
    }
}
