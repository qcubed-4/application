<?php
namespace QCubed\Jqui;

use QCubed;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class ControlgroupGen
 *
 * This is the ControlgroupGen class which is automatically generated
 * by scraping the JQuery UI documentation website. As such, it includes all the options
 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
 * the ControlgroupBase class for any glue code to make this class more
 * usable in QCubed.
 *
 * @see ControlgroupBase
 * @package QCubed\Jqui
 * @property mixed $Classes
 * Specify additional classes to add to the widgets elements. Any of
 * classes specified in the Theming section can be used as keys to
 * override their value. To learn more about this option, check out the
 * learn article about the classes option.

 *
 * @property string $Direction
 * By default, controlgroup displays its controls in a horizontal layout.
 * Use this option to use a vertical layout instead.

 *
 * @property boolean $Disabled
 * Disables the controlgroup if set to true.
 *
 * @property mixed $Items
 * Which descendant elements to initialize as their respective widgets.
 * Two elements have special behavior: 
 * 
 * 	* controlgroupLabel: Any elements matching the selector for this will
 * be wrapped in a span with the ui-controlgroup-label-contents class.
 * 	* spinner: This uses a class selector as the value. Requires either
 * adding the class manually or initializing the spinner manually. Can be
 * overridden to use input[type=number], but that also requires custom
 * CSS to remove the native number controls.
 * 

 *
 * @property boolean $OnlyVisible
 * Sets whether to exclude invisible children in the assignment of
 * rounded corners. When set to false, all children of a controlgroup are
 * taken into account when assigning rounded corners, including hidden
 * children. Thus, if, for example, the controlgroups first child is
 * hidden and the default horizontal layout is applied, the controlgroup
 * will, in effect, not have rounded corners on the left edge. Likewise,
 * if the controlgroup has a vertical layout and its first child is
 * hidden, the controlgroup will not have rounded corners on the top
 * edge.
 *
 * @was QControlgroupGen

 */

class ControlgroupGen extends QCubed\Control\Panel
{
    protected $strJavaScripts = QCUBED_JQUI_JS;
    protected $strStyleSheets = QCUBED_JQUI_CSS;
    /** @var mixed */
    protected $mixClasses = null;
    /** @var string */
    protected $strDirection = null;
    /** @var boolean */
    protected $blnDisabled = null;
    /** @var mixed */
    protected $mixItems = null;
    /** @var boolean */
    protected $blnOnlyVisible = null;

    /**
     * Builds the option array to be sent to the widget constructor.
     *
     * @return array key=>value array of options
     */
    protected function makeJqOptions() {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->Classes)) {$jqOptions['classes'] = $val;}
        if (!is_null($val = $this->Direction)) {$jqOptions['direction'] = $val;}
        if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
        if (!is_null($val = $this->Items)) {$jqOptions['items'] = $val;}
        if (!is_null($val = $this->OnlyVisible)) {$jqOptions['onlyVisible'] = $val;}
        return $jqOptions;
    }

    /**
     * Return the JavaScript function to call to associate the widget with the control.
     *
     * @return string
     */
    public function getJqSetupFunction()
    {
        return 'controlgroup';
    }


    /**
     * Removes the controlgroup functionality completely. This will return
     * the element back to its pre-init state.
     * 
     * 	* This method does not accept any arguments.
     */
    public function destroy()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", Application::PRIORITY_LOW);
    }
    /**
     * Disables the controlgroup.
     * 
     * 	* This method does not accept any arguments.
     */
    public function disable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", Application::PRIORITY_LOW);
    }
    /**
     * Enables the controlgroup.
     * 
     * 	* This method does not accept any arguments.
     */
    public function enable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", Application::PRIORITY_LOW);
    }
    /**
     * Retrieves the controlgroups instance object. If the element does not
     * have an associated instance, undefined is returned.
     * 
     * Unlike other widget methods, instance() is safe to call on any element
     * after the controlgroup plugin has loaded.
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
     * controlgroup options hash.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function option1()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", Application::PRIORITY_LOW);
    }
    /**
     * Sets the value of the controlgroup option associated with the
     * specified optionName.
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
     * Sets one or more options for the controlgroup.
     * 
     * 	* options Type: Object A map of option-value pairs to set.
     * @param $options
     */
    public function option3($options)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, Application::PRIORITY_LOW);
    }
    /**
     * Process any widgets that were added or removed directly in the DOM.
     * Results depend on the items option.
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
            case 'Classes': return $this->mixClasses;
            case 'Direction': return $this->strDirection;
            case 'Disabled': return $this->blnDisabled;
            case 'Items': return $this->mixItems;
            case 'OnlyVisible': return $this->blnOnlyVisible;
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
            case 'Classes':
                $this->mixClasses = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'classes', $mixValue);
                break;

            case 'Direction':
                try {
                    $this->strDirection = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'direction', $this->strDirection);
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

            case 'Items':
                $this->mixItems = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'items', $mixValue);
                break;

            case 'OnlyVisible':
                try {
                    $this->blnOnlyVisible = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'onlyVisible', $this->blnOnlyVisible);
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
            new QModelConnectorParam (get_called_class(), 'Direction', 'By default, controlgroup displays its controls in a horizontal layout.Use this option to use a vertical layout instead.', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the controlgroup if set to true.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'OnlyVisible', 'Sets whether to exclude invisible children in the assignment ofrounded corners. When set to false, all children of a controlgroup aretaken into account when assigning rounded corners, including hiddenchildren. Thus, if, for example, the controlgroups first child ishidden and the default horizontal layout is applied, the controlgroupwill, in effect, not have rounded corners on the left edge. Likewise,if the controlgroup has a vertical layout and its first child ishidden, the controlgroup will not have rounded corners on the topedge.', Type::BOOLEAN),
        ));
    }
}
