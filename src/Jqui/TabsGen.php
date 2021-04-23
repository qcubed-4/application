<?php
namespace QCubed\Jqui;

use QCubed;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class TabsGen
 *
 * This is the TabsGen class which is automatically generated
 * by scraping the JQuery UI documentation website. As such, it includes all the options
 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
 * the TabsBase class for any glue code to make this class more
 * usable in QCubed.
 *
 * @see TabsBase
 * @package QCubed\Jqui
 * @property mixed $Active
 * Which panel is currently open.Multiple types supported:
 * 
 * 	* Boolean: Setting active to false will collapse all panels. This
 * requires the collapsible option to be true.
 * 	* Integer: The zero-based index of the panel that is active (open).
 * A negative value selects panels going backward from the last panel.
 * 

 *
 * @property mixed $Classes
 * Specify additional classes to add to the widgets elements. Any of
 * classes specified in the Theming section can be used as keys to
 * override their value. To learn more about this option, check out the
 * learn article about the classes option.

 *
 * @property boolean $Collapsible
 * When set to true, the active panel can be closed.
 *
 * @property mixed $Disabled
 * Which tabs are disabled.Multiple types supported:
 * 
 * 	* Boolean: Enable or disable all tabs.
 * 	* Array: An array containing the zero-based indexes of the tabs that
 * should be disabled, e.g., [ 0, 2 ] would disable the first and third
 * tab.
 * 

 *
 * @property string $Event
 * The type of event that the tabs should react to in order to activate
 * the tab. To activate on hover, use "mouseover".
 *
 * @property string $HeightStyle
 * Controls the height of the tabs widget and each panel. Possible
 * values: 
 * 
 * 	* "auto": All panels will be set to the height of the tallest panel.
 * 	* "fill": Expand to the available height based on the tabs parent
 * height.
 * 	* "content": Each panel will be only as tall as its content.
 * 

 *
 * @property mixed $Hide
 * If and how to animate the hiding of the panel.Multiple types
 * supported:
 * 
 * 	* Boolean: When set to false, no animation will be used and the panel
 * will be hidden immediately. When set to true, the panel will fade out
 * with the default duration and the default easing.
 * 	* Number: The panel will fade out with the specified duration and
 * the default easing.
 * 	* String: The panel will be hidden using the specified effect. The
 * value can either be the name of a built-in jQuery animation method,
 * such as "slideUp", or the name of a jQuery UI effect, such as "fold".
 * In either case the effect will be used with the default duration and
 * the default easing.
 * 	* Object: If the value is an object, then effect, delay, duration,
 * and easing properties may be provided. If the effect property contains
 * the name of a jQuery method, then that method will be used; otherwise
 * it is assumed to be the name of a jQuery UI effect. When using a
 * jQuery UI effect that supports additional settings, you may include
 * those settings in the object and they will be passed to the effect. If
 * duration or easing is omitted, then the default values will be used.
 * If effect is omitted, then "fadeOut" will be used. If delay is
 * omitted, then no delay is used.
 * 

 *
 * @property mixed $Show
 * If and how to animate the showing of the panel.Multiple types
 * supported:
 * 
 * 	* Boolean: When set to false, no animation will be used and the panel
 * will be shown immediately. When set to true, the panel will fade in
 * with the default duration and the default easing.
 * 	* Number: The panel will fade in with the specified duration and the
 * default easing.
 * 	* String: The panel will be shown using the specified effect. The
 * value can either be the name of a built-in jQuery animation method,
 * such as "slideDown", or the name of a jQuery UI effect, such as
 * "fold". In either case the effect will be used with the default
 * duration and the default easing.
 * 	* Object: If the value is an object, then effect, delay, duration,
 * and easing properties may be provided. If the effect property contains
 * the name of a jQuery method, then that method will be used; otherwise
 * it is assumed to be the name of a jQuery UI effect. When using a
 * jQuery UI effect that supports additional settings, you may include
 * those settings in the object and they will be passed to the effect. If
 * duration or easing is omitted, then the default values will be used.
 * If effect is omitted, then "fadeIn" will be used. If delay is omitted,
 * then no delay is used.
 * 

 *
 * @was QTabsGen

 */

class TabsGen extends QCubed\Control\Panel
{
    protected $strJavaScripts = QCUBED_JQUI_JS;
    protected $strStyleSheets = QCUBED_JQUI_CSS;
    /** @var mixed */
    protected $mixActive;
    /** @var mixed */
    protected $mixClasses = null;
    /** @var boolean */
    protected $blnCollapsible = null;
    /** @var mixed */
    protected $mixDisabled = null;
    /** @var string */
    protected $strEvent = null;
    /** @var string */
    protected $strHeightStyle = null;
    /** @var mixed */
    protected $mixHide = null;
    /** @var mixed */
    protected $mixShow = null;

    /**
     * Builds the option array to be sent to the widget constructor.
     *
     * @return array key=>value array of options
     */
    protected function makeJqOptions() {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->Active)) {$jqOptions['active'] = $val;}
        if (!is_null($val = $this->Classes)) {$jqOptions['classes'] = $val;}
        if (!is_null($val = $this->Collapsible)) {$jqOptions['collapsible'] = $val;}
        if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
        if (!is_null($val = $this->Event)) {$jqOptions['event'] = $val;}
        if (!is_null($val = $this->HeightStyle)) {$jqOptions['heightStyle'] = $val;}
        if (!is_null($val = $this->Hide)) {$jqOptions['hide'] = $val;}
        if (!is_null($val = $this->Show)) {$jqOptions['show'] = $val;}
        return $jqOptions;
    }

    /**
     * Return the JavaScript function to call to associate the widget with the control.
     *
     * @return string
     */
    public function getJqSetupFunction()
    {
        return 'tabs';
    }


    /**
     * Removes the tabs functionality completely. This will return the
     * element back to its pre-init state.
     * 
     * 	* This method does not accept any arguments.
     */
    public function destroy()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", Application::PRIORITY_LOW);
    }
    /**
     * Disables all tabs.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function disable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", Application::PRIORITY_LOW);
    }
    /**
     * Disables a tab. The selected tab cannot be disabled. To disable more
     * than one tab at once, set the disabled option: $( "#tabs" ).tabs(
     * "option", "disabled", [ 1, 2, 3 ] ).
     * 
     * 	* index Type: Number The zero-based index of the tab to disable.
     * @param $index
     */
    public function disable1($index)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", $index, Application::PRIORITY_LOW);
    }
    /**
     * Disables a tab. The selected tab cannot be disabled.
     * 
     * 	* href Type: String The href of the tab to disable.
     * @param $href
     */
    public function disable2($href)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", $href, Application::PRIORITY_LOW);
    }
    /**
     * Enables all tabs.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function enable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", Application::PRIORITY_LOW);
    }
    /**
     * Enables a tab. To enable more than one tab at once reset the disabled
     * property like: $( "#example" ).tabs( "option", "disabled", [] );.
     * 
     * 	* index Type: Number The zero-based index of the tab to enable.
     * @param $index
     */
    public function enable1($index)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", $index, Application::PRIORITY_LOW);
    }
    /**
     * Enables a tab.
     * 
     * 	* href Type: String The href of the tab to enable.
     * @param $href
     */
    public function enable2($href)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", $href, Application::PRIORITY_LOW);
    }
    /**
     * Retrieves the tabss instance object. If the element does not have an
     * associated instance, undefined is returned.
     * 
     * Unlike other widget methods, instance() is safe to call on any element
     * after the tabs plugin has loaded.
     * 
     * 	* This method does not accept any arguments.
     */
    public function instance()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", Application::PRIORITY_LOW);
    }
    /**
     * Loads the panel content of a remote tab.
     * 
     * 	* index Type: Number The zero-based index of the tab to load.
     * @param $index
     */
    public function load($index)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "load", $index, Application::PRIORITY_LOW);
    }
    /**
     * Loads the panel content of a remote tab.
     * 
     * 	* href Type: String The href of the tab to load.
     * @param $href
     */
    public function load1($href)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "load", $href, Application::PRIORITY_LOW);
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
     * tabs options hash.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function option1()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", Application::PRIORITY_LOW);
    }
    /**
     * Sets the value of the tabs option associated with the specified
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
     * Sets one or more options for the tabs.
     * 
     * 	* options Type: Object A map of option-value pairs to set.
     * @param $options
     */
    public function option3($options)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, Application::PRIORITY_LOW);
    }
    /**
     * Process any tabs that were added or removed directly in the DOM and
     * recompute the height of the tab panels. Results depend on the content
     * and the heightStyle option.
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
            case 'Active': return $this->mixActive;
            case 'Classes': return $this->mixClasses;
            case 'Collapsible': return $this->blnCollapsible;
            case 'Disabled': return $this->mixDisabled;
            case 'Event': return $this->strEvent;
            case 'HeightStyle': return $this->strHeightStyle;
            case 'Hide': return $this->mixHide;
            case 'Show': return $this->mixShow;
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
            case 'Active':
                $this->mixActive = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'active', $mixValue);
                break;

            case 'Classes':
                $this->mixClasses = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'classes', $mixValue);
                break;

            case 'Collapsible':
                try {
                    $this->blnCollapsible = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'collapsible', $this->blnCollapsible);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Disabled':
                $this->mixDisabled = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'disabled', $mixValue);
                break;

            case 'Event':
                try {
                    $this->strEvent = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'event', $this->strEvent);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'HeightStyle':
                try {
                    $this->strHeightStyle = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'heightStyle', $this->strHeightStyle);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Hide':
                $this->mixHide = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'hide', $mixValue);
                break;

            case 'Show':
                $this->mixShow = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'show', $mixValue);
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
            new QModelConnectorParam (get_called_class(), 'Collapsible', 'When set to true, the active panel can be closed.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'Event', 'The type of event that the tabs should react to in order to activatethe tab. To activate on hover, use \"mouseover\".', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'HeightStyle', 'Controls the height of the tabs widget and each panel. Possiblevalues: 	* \"auto\": All panels will be set to the height of the tallest panel.	* \"fill\": Expand to the available height based on the tabs parentheight.	* \"content\": Each panel will be only as tall as its content.', Type::STRING),
        ));
    }
}
