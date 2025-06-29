<?php
namespace QCubed\Jqui;

use QCubed;
use QCubed\Control\Panel;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class SelectableGen
 *
 * This is the SelectableGen class that is automatically generated
 * by scraping the JQuery UI documentation website. As such, it includes all the options
 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
 * the SelectableBase class for any glue code to make this class more
 * usable in QCubed.
 *
 * @see SelectableBase
 * @package QCubed\Jqui
 * @property mixed $AppendTo
 * Which element the selection helper (the lasso) should be appended to.
 *
 * @property boolean $AutoRefresh
 * This determines whether to refresh (recalculate) the position and size
 * of each selectee at the beginning of each select operation. If you
 * have many items, you may want to set this to false and call the
 * refresh() method manually.
 *
 * @property mixed $Cancel
 * Prevents selecting if you start on elements matching the selector.
 *
 * @property mixed $Classes
 * Specify additional classes to add to the widget elements. Any of
 * the classes specified in the Theming section can be used as keys to
 * override their value. To learn more about this option, check out the
 * learned article about the classes option.

 *
 * @property integer $Delay
 * Time in milliseconds to define when the selecting should start. This
 * helps prevent unwanted selections when clicking on an element. (version
 * deprecated: 1.12)
 *
 * @property boolean $Disabled
 * Disables the selectable if set to true.
 *
 * @property integer $Distance
 * Tolerance, in pixels, for when selecting should start. If specified,
 * selecting will not start until the mouse has been dragged beyond the
 * specified distance. (version deprecated: 1.12)
 *
 * @property mixed $Filter
 * The matching child elements will be made selectees (able to be
 * selected).
 *
 * @property string $Tolerance
 * Specify which mode to use for testing whether the lasso should
 * select an item. Possible values: 
 * 
 * 	* "fit": Lasso overlaps the item entirely.
 * 	* "touch": Lasso overlaps the item by any amount.
 *
 *
 */

class SelectableGen extends Panel
{
    protected string $strJavaScripts = QCUBED_JQUI_JS;
    protected string $strStyleSheets = QCUBED_JQUI_CSS;
    /** @var mixed */
    protected mixed $mixAppendTo = null;
    /** @var boolean */
    protected ?bool $blnAutoRefresh = null;
    /** @var mixed */
    protected mixed $mixCancel = null;
    /** @var mixed */
    protected mixed $mixClasses = null;
    /** @var integer|null */
    protected ?int $intDelay = null;
    /** @var boolean */
    protected ?bool $blnDisabled = null;
    /** @var integer|null */
    protected ?int $intDistance = null;
    /** @var mixed */
    protected mixed $mixFilter = null;
    /** @var string|null */
    protected ?string $strTolerance = null;

    /**
     * Builds the option array to be sent to the widget constructor.
     *
     * @return array key=>value array of options
     */
    protected function makeJqOptions(): array
    {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->AppendTo)) {$jqOptions['appendTo'] = $val;}
        if (!is_null($val = $this->AutoRefresh)) {$jqOptions['autoRefresh'] = $val;}
        if (!is_null($val = $this->Cancel)) {$jqOptions['cancel'] = $val;}
        if (!is_null($val = $this->Classes)) {$jqOptions['classes'] = $val;}
        if (!is_null($val = $this->Delay)) {$jqOptions['delay'] = $val;}
        if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
        if (!is_null($val = $this->Distance)) {$jqOptions['distance'] = $val;}
        if (!is_null($val = $this->Filter)) {$jqOptions['filter'] = $val;}
        if (!is_null($val = $this->Tolerance)) {$jqOptions['tolerance'] = $val;}
        return $jqOptions;
    }

    /**
     * Return the JavaScript function to call to associate the widget with the control.
     *
     * @return string
     */
    public function getJqSetupFunction(): string
    {
        return 'selectable';
    }


    /**
     * Removes the selectable functionality completely. This will return the
     * element back to its pre-init state.
     * 
     * 	* This method does not accept any arguments.
     */
    public function destroy(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Disables the selectable.
     * 
     * 	* This method does not accept any arguments.
     */
    public function disable(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Enables the selectable.
     * 
     * 	* This method does not accept any arguments.
     */
    public function enable(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Retrieves the selectables instance object. If the element does not
     * have an associated instance, undefined is returned.
     * 
     * Unlike other widget methods, instance() is safe to call on any element
     * after the selectable plugin has loaded.
     * 
     * 	* This method does not accept any arguments.
     */
    public function instance(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", QCubed\ApplicationBase::PRIORITY_LOW);
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
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Gets an object containing key/value pairs representing the current
     * selectable options hash.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function option1(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Sets the value of the selectable option associated with the specified
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
     * @param object $value
     */
    public function option2(string $optionName, object $value): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, $value, QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Sets one or more options for the selectable.
     *
     *    * options Type: Object A map of option-value pairs to set.
     * @param object $options
     */
    public function option3(object $options): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Refresh the position and size of each selectee element. This method
     * can be used to manually recalculate the position and size of each
     * selectee when the autoRefresh option is set to false.
     * 
     * 	* This method does not accept any arguments.
     */
    public function refresh(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "refresh", QCubed\ApplicationBase::PRIORITY_LOW);
    }


    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'AppendTo': return $this->mixAppendTo;
            case 'AutoRefresh': return $this->blnAutoRefresh;
            case 'Cancel': return $this->mixCancel;
            case 'Classes': return $this->mixClasses;
            case 'Delay': return $this->intDelay;
            case 'Disabled': return $this->blnDisabled;
            case 'Distance': return $this->intDistance;
            case 'Filter': return $this->mixFilter;
            case 'Tolerance': return $this->strTolerance;
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
            case 'AppendTo':
                $this->mixAppendTo = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'appendTo', $mixValue);
                break;

            case 'AutoRefresh':
                try {
                    $this->blnAutoRefresh = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'autoRefresh', $this->blnAutoRefresh);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Cancel':
                $this->mixCancel = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'cancel', $mixValue);
                break;

            case 'Classes':
                $this->mixClasses = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'classes', $mixValue);
                break;

            case 'Delay':
                try {
                    $this->intDelay = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'delay', $this->intDelay);
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

            case 'Distance':
                try {
                    $this->intDistance = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'distance', $this->intDistance);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Filter':
                $this->mixFilter = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'filter', $mixValue);
                break;

            case 'Tolerance':
                try {
                    $this->strTolerance = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tolerance', $this->strTolerance);
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
     *
     * @throws Caller
     */
    public static function getModelConnectorParams(): array
    {
        return array_merge(parent::GetModelConnectorParams(), array(
            new QModelConnectorParam (get_called_class(), 'AutoRefresh', 'This determines whether to refresh (recalculate) the position and sizeof each selectee at the beginning of each select operation. If you have many items, you may want to set this to false and call the refresh() method manually.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'Delay', 'Time in milliseconds to define when the selecting should start. This helps prevent unwanted selections when clicking on an element. (version deprecated: 1.12)', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the selectable if set to true.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'Distance', 'Tolerance, in pixels, for when selecting should start. If specified, selecting will not start until the mouse has been dragged beyond the specified distance. (version deprecated: 1.12)', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Tolerance', 'Specify which mode to use for testing whether the lasso should select an item. Possible values:	* \"fit\": Lasso overlaps the item entirely.	* \"touch\": Lasso overlaps the item by any amount.', Type::STRING),
        ));
    }
}