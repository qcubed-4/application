<?php
namespace QCubed\Jqui;

use QCubed;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class SelectableGen
 *
 * This is the SelectableGen class which is automatically generated
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
 * Specify additional classes to add to the widgets elements. Any of
 * classes specified in the Theming section can be used as keys to
 * override their value. To learn more about this option, check out the
 * learn article about the classes option.

 *
 * @property integer $Delay
 * Time in milliseconds to define when the selecting should start. This
 * helps prevent unwanted selections when clicking on an element.(version
 * deprecated: 1.12)
 *
 * @property boolean $Disabled
 * Disables the selectable if set to true.
 *
 * @property integer $Distance
 * Tolerance, in pixels, for when selecting should start. If specified,
 * selecting will not start until the mouse has been dragged beyond the
 * specified distance.(version deprecated: 1.12)
 *
 * @property mixed $Filter
 * The matching child elements will be made selectees (able to be
 * selected).
 *
 * @property string $Tolerance
 * Specifies which mode to use for testing whether the lasso should
 * select an item. Possible values: 
 * 
 * 	* "fit": Lasso overlaps the item entirely.
 * 	* "touch": Lasso overlaps the item by any amount.
 * 

 *
 * @was QSelectableGen

 */

class SelectableGen extends QCubed\Control\Panel
{
    protected $strJavaScripts = QCUBED_JQUI_JS;
    protected $strStyleSheets = QCUBED_JQUI_CSS;
    /** @var mixed */
    protected $mixAppendTo = null;
    /** @var boolean */
    protected $blnAutoRefresh = null;
    /** @var mixed */
    protected $mixCancel = null;
    /** @var mixed */
    protected $mixClasses = null;
    /** @var integer */
    protected $intDelay;
    /** @var boolean */
    protected $blnDisabled = null;
    /** @var integer */
    protected $intDistance;
    /** @var mixed */
    protected $mixFilter = null;
    /** @var string */
    protected $strTolerance = null;

    /**
     * Builds the option array to be sent to the widget constructor.
     *
     * @return array key=>value array of options
     */
    protected function makeJqOptions() {
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
    public function getJqSetupFunction()
    {
        return 'selectable';
    }


    /**
     * Removes the selectable functionality completely. This will return the
     * element back to its pre-init state.
     * 
     * 	* This method does not accept any arguments.
     */
    public function destroy()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", Application::PRIORITY_LOW);
    }
    /**
     * Disables the selectable.
     * 
     * 	* This method does not accept any arguments.
     */
    public function disable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", Application::PRIORITY_LOW);
    }
    /**
     * Enables the selectable.
     * 
     * 	* This method does not accept any arguments.
     */
    public function enable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", Application::PRIORITY_LOW);
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
     * selectable options hash.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function option1()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", Application::PRIORITY_LOW);
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
     * Sets one or more options for the selectable.
     * 
     * 	* options Type: Object A map of option-value pairs to set.
     * @param $options
     */
    public function option3($options)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, Application::PRIORITY_LOW);
    }
    /**
     * Refresh the position and size of each selectee element. This method
     * can be used to manually recalculate the position and size of each
     * selectee when the autoRefresh option is set to false.
     * 
     * 	* This method does not accept any arguments.
     */
    public function refresh()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "refresh", Application::PRIORITY_LOW);
    }


    public function __get($strName)
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

    public function __set($strName, $mixValue)
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
    **/
    public static function getModelConnectorParams()
    {
        return array_merge(parent::GetModelConnectorParams(), array(
            new QModelConnectorParam (get_called_class(), 'AutoRefresh', 'This determines whether to refresh (recalculate) the position and sizeof each selectee at the beginning of each select operation. If youhave many items, you may want to set this to false and call therefresh() method manually.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'Delay', 'Time in milliseconds to define when the selecting should start. Thishelps prevent unwanted selections when clicking on an element.(versiondeprecated: 1.12)', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the selectable if set to true.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'Distance', 'Tolerance, in pixels, for when selecting should start. If specified,selecting will not start until the mouse has been dragged beyond thespecified distance.(version deprecated: 1.12)', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Tolerance', 'Specifies which mode to use for testing whether the lasso shouldselect an item. Possible values: 	* \"fit\": Lasso overlaps the item entirely.	* \"touch\": Lasso overlaps the item by any amount.', Type::STRING),
        ));
    }
}
