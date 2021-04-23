<?php
namespace QCubed\Jqui;

use QCubed;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class SelectMenuGen
 *
 * This is the SelectMenuGen class which is automatically generated
 * by scraping the JQuery UI documentation website. As such, it includes all the options
 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
 * the SelectMenuBase class for any glue code to make this class more
 * usable in QCubed.
 *
 * @see SelectMenuBase
 * @package QCubed\Jqui
 * @property mixed $AppendTo
 * Which element to append the menu to. When the value is null, the
 * parents of the <select> are checked for a class name of ui-front. If
 * an element with the ui-front class name is found, the menu is appended
 * to that element. Regardless of the value, if no element is found, the
 * menu is appended to the body.
 *
 * @property mixed $Classes
 * Specify additional classes to add to the widgets elements. Any of
 * classes specified in the Theming section can be used as keys to
 * override their value. To learn more about this option, check out the
 * learn article about the classes option.

 *
 * @property boolean $Disabled
 * Disables the selectmenu if set to true.
 *
 * @property mixed $Icons
 * Icons to use for the button, matching an icon defined by the jQuery UI
 * CSS Framework. 
 * 
 * 	* button (string, default: "ui-icon-triangle-1-s")
 * 

 *
 * @property mixed $Position
 * Identifies the position of the menu in relation to the associated
 * button element. You can refer to the jQuery UI Position utility for
 * more details about the various options.
 *
 * @property mixed $Width
 * The width of the menu, in pixels. When the value is null, the width of
 * the native select is used. When the value is false, no inline style
 * will be set for the width, allowing the width to be set in a
 * stylesheet.
 *
 * @was QSelectMenuGen

 */

class SelectMenuGen extends QCubed\Project\Control\ListBox
{
    protected $strJavaScripts = QCUBED_JQUI_JS;
    protected $strStyleSheets = QCUBED_JQUI_CSS;
    /** @var mixed */
    protected $mixAppendTo = null;
    /** @var mixed */
    protected $mixClasses = null;
    /** @var boolean */
    protected $blnDisabled = null;
    /** @var mixed */
    protected $mixIcons = null;
    /** @var mixed */
    protected $mixPosition = null;
    /** @var mixed */
    protected $mixWidth = null;

    /**
     * Builds the option array to be sent to the widget constructor.
     *
     * @return array key=>value array of options
     */
    protected function makeJqOptions() {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->AppendTo)) {$jqOptions['appendTo'] = $val;}
        if (!is_null($val = $this->Classes)) {$jqOptions['classes'] = $val;}
        if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
        if (!is_null($val = $this->Icons)) {$jqOptions['icons'] = $val;}
        if (!is_null($val = $this->Position)) {$jqOptions['position'] = $val;}
        if (!is_null($val = $this->Width)) {$jqOptions['width'] = $val;}
        return $jqOptions;
    }

    /**
     * Return the JavaScript function to call to associate the widget with the control.
     *
     * @return string
     */
    public function getJqSetupFunction()
    {
        return 'selectmenu';
    }


    /**
     * Closes the menu.
     * 
     * 	* This method does not accept any arguments.
     */
    public function close()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "close", Application::PRIORITY_LOW);
    }
    /**
     * Removes the selectmenu functionality completely. This will return the
     * element back to its pre-init state.
     * 
     * 	* This method does not accept any arguments.
     */
    public function destroy()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", Application::PRIORITY_LOW);
    }
    /**
     * Disables the selectmenu.
     * 
     * 	* This method does not accept any arguments.
     */
    public function disable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", Application::PRIORITY_LOW);
    }
    /**
     * Enables the selectmenu.
     * 
     * 	* This method does not accept any arguments.
     */
    public function enable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", Application::PRIORITY_LOW);
    }
    /**
     * Retrieves the selectmenus instance object. If the element does not
     * have an associated instance, undefined is returned.
     * 
     * Unlike other widget methods, instance() is safe to call on any element
     * after the selectmenu plugin has loaded.
     * 
     * 	* This method does not accept any arguments.
     */
    public function instance()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", Application::PRIORITY_LOW);
    }
    /**
     * Returns a jQuery object containing the menu element.
     * 
     * 	* This method does not accept any arguments.
     */
    public function menuWidget()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "menuWidget", Application::PRIORITY_LOW);
    }
    /**
     * Opens the menu.
     * 
     * 	* This method does not accept any arguments.
     */
    public function open()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "open", Application::PRIORITY_LOW);
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
     * selectmenu options hash.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function option1()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", Application::PRIORITY_LOW);
    }
    /**
     * Sets the value of the selectmenu option associated with the specified
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
     * Sets one or more options for the selectmenu.
     * 
     * 	* options Type: Object A map of option-value pairs to set.
     * @param $options
     */
    public function option3($options)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, Application::PRIORITY_LOW);
    }
    /**
     * Parses the original element and re-renders the menu. Processes any
     * <option> or <optgroup> elements that were added, removed or disabled.
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
            case 'Classes': return $this->mixClasses;
            case 'Disabled': return $this->blnDisabled;
            case 'Icons': return $this->mixIcons;
            case 'Position': return $this->mixPosition;
            case 'Width': return $this->mixWidth;
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

            case 'Icons':
                $this->mixIcons = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'icons', $mixValue);
                break;

            case 'Position':
                $this->mixPosition = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'position', $mixValue);
                break;

            case 'Width':
                $this->mixWidth = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'width', $mixValue);
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
    **/
    public static function getModelConnectorParams()
    {
        return array_merge(parent::GetModelConnectorParams(), array(
            new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the selectmenu if set to true.', Type::BOOLEAN),
        ));
    }
}
