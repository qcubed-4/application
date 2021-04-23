<?php
namespace QCubed\Jqui;

use QCubed;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class MenuGen
 *
 * This is the MenuGen class which is automatically generated
 * by scraping the JQuery UI documentation website. As such, it includes all the options
 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
 * the MenuBase class for any glue code to make this class more
 * usable in QCubed.
 *
 * @see MenuBase
 * @package QCubed\Jqui
 * @property mixed $Classes
 * Specify additional classes to add to the widgets elements. Any of
 * classes specified in the Theming section can be used as keys to
 * override their value. To learn more about this option, check out the
 * learn article about the classes option.

 *
 * @property boolean $Disabled
 * Disables the menu if set to true.
 *
 * @property mixed $Icons
 * Icons to use for submenus, matching an icon provided by the jQuery UI
 * CSS Framework.
 *
 * @property string $Items
 * Selector for the elements that serve as the menu items.
 * Note: The items option should not be changed after initialization.
 * (version added: 1.11.0)
 *
 * @property string $Menus
 * Selector for the elements that serve as the menu container, including
 * sub-menus.
 * Note: The menus option should not be changed after initialization.
 * Existing submenus will not be updated.
 *
 * @property mixed $Position
 * Identifies the position of submenus in relation to the associated
 * parent menu item. The of option defaults to the parent menu item, but
 * you can specify another element to position against. You can refer to
 * the jQuery UI Position utility for more details about the various
 * options.
 *
 * @property string $Role
 * Customize the ARIA roles used for the menu and menu items. The default
 * uses "menuitem" for items. Setting the role option to "listbox" will
 * use "option" for items. If set to null, no roles will be set, which is
 * useful if the menu is being controlled by another element that is
 * maintaining focus.
 * Note: The role option should not be changed after initialization.
 * Existing (sub)menus and menu items will not be updated.
 *
 * @was QMenuGen

 */

class MenuGen extends QCubed\Control\Panel
{
    protected $strJavaScripts = QCUBED_JQUI_JS;
    protected $strStyleSheets = QCUBED_JQUI_CSS;
    /** @var mixed */
    protected $mixClasses = null;
    /** @var boolean */
    protected $blnDisabled = null;
    /** @var mixed */
    protected $mixIcons = null;
    /** @var string */
    protected $strItems = null;
    /** @var string */
    protected $strMenus = null;
    /** @var mixed */
    protected $mixPosition = null;
    /** @var string */
    protected $strRole = null;

    /**
     * Builds the option array to be sent to the widget constructor.
     *
     * @return array key=>value array of options
     */
    protected function makeJqOptions() {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->Classes)) {$jqOptions['classes'] = $val;}
        if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
        if (!is_null($val = $this->Icons)) {$jqOptions['icons'] = $val;}
        if (!is_null($val = $this->Items)) {$jqOptions['items'] = $val;}
        if (!is_null($val = $this->Menus)) {$jqOptions['menus'] = $val;}
        if (!is_null($val = $this->Position)) {$jqOptions['position'] = $val;}
        if (!is_null($val = $this->Role)) {$jqOptions['role'] = $val;}
        return $jqOptions;
    }

    /**
     * Return the JavaScript function to call to associate the widget with the control.
     *
     * @return string
     */
    public function getJqSetupFunction()
    {
        return 'menu';
    }


    /**
     * Removes focus from a menu, resets any active element styles and
     * triggers the menus blur event.
     * 
     * 	* event Type: Event What triggered the menu to blur.
     * @param $event
     */
    public function blur($event = null)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "blur", $event, Application::PRIORITY_LOW);
    }
    /**
     * Closes the currently active sub-menu.
     * 
     * 	* event Type: Event What triggered the menu to collapse.
     * @param $event
     */
    public function collapse($event = null)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "collapse", $event, Application::PRIORITY_LOW);
    }
    /**
     * Closes all open sub-menus.
     * 
     * 	* event Type: Event What triggered the menu to collapse.
     * 	* all Type: Boolean Indicates whether all sub-menus should be closed
     * or only sub-menus below and including the menu that is or contains the
     * target of the triggering event.
     * @param $event
     * @param $all
     */
    public function collapseAll($event = null, $all = null)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "collapseAll", $event, $all, Application::PRIORITY_LOW);
    }
    /**
     * Removes the menu functionality completely. This will return the
     * element back to its pre-init state.
     * 
     * 	* This method does not accept any arguments.
     */
    public function destroy()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", Application::PRIORITY_LOW);
    }
    /**
     * Disables the menu.
     * 
     * 	* This method does not accept any arguments.
     */
    public function disable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", Application::PRIORITY_LOW);
    }
    /**
     * Enables the menu.
     * 
     * 	* This method does not accept any arguments.
     */
    public function enable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", Application::PRIORITY_LOW);
    }
    /**
     * Opens the sub-menu below the currently active item, if one exists.
     * 
     * 	* event Type: Event What triggered the menu to expand.
     * @param $event
     */
    public function expand($event = null)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "expand", $event, Application::PRIORITY_LOW);
    }
    /**
     * Activates the given menu item and triggers the menus focus event.
     * Opens the menu items sub-menu, if one exists.
     * 
     * 	* event Type: Event What triggered the menu item to gain focus.
     * 	* item Type: jQuery The menu item to focus/activate.
     * @param $item
     * @param $event
     */
    public function focus1($event = null, $item)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "focus", $item, $event, Application::PRIORITY_LOW);
    }
    /**
     * Retrieves the menus instance object. If the element does not have an
     * associated instance, undefined is returned.
     * 
     * Unlike other widget methods, instance() is safe to call on any element
     * after the menu plugin has loaded.
     * 
     * 	* This method does not accept any arguments.
     */
    public function instance()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", Application::PRIORITY_LOW);
    }
    /**
     * Returns a boolean value stating whether or not the currently active
     * item is the first item in the menu.
     * 
     * 	* This method does not accept any arguments.
     */
    public function isFirstItem()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "isFirstItem", Application::PRIORITY_LOW);
    }
    /**
     * Returns a boolean value stating whether or not the currently active
     * item is the last item in the menu.
     * 
     * 	* This method does not accept any arguments.
     */
    public function isLastItem()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "isLastItem", Application::PRIORITY_LOW);
    }
    /**
     * Moves active state to next menu item.
     * 
     * 	* event Type: Event What triggered the focus to move.
     * @param $event
     */
    public function next($event = null)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "next", $event, Application::PRIORITY_LOW);
    }
    /**
     * Moves active state to first menu item below the bottom of a scrollable
     * menu or the last item if not scrollable.
     * 
     * 	* event Type: Event What triggered the focus to move.
     * @param $event
     */
    public function nextPage($event = null)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "nextPage", $event, Application::PRIORITY_LOW);
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
     * menu options hash.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function option1()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", Application::PRIORITY_LOW);
    }
    /**
     * Sets the value of the menu option associated with the specified
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
     * Sets one or more options for the menu.
     * 
     * 	* options Type: Object A map of option-value pairs to set.
     * @param $options
     */
    public function option3($options)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, Application::PRIORITY_LOW);
    }
    /**
     * Moves active state to previous menu item.
     * 
     * 	* event Type: Event What triggered the focus to move.
     * @param $event
     */
    public function previous($event = null)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "previous", $event, Application::PRIORITY_LOW);
    }
    /**
     * Moves active state to first menu item above the top of a scrollable
     * menu or the first item if not scrollable.
     * 
     * 	* event Type: Event What triggered the focus to move.
     * @param $event
     */
    public function previousPage($event = null)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "previousPage", $event, Application::PRIORITY_LOW);
    }
    /**
     * Initializes sub-menus and menu items that have not already been
     * initialized. New menu items, including sub-menus can be added to the
     * menu or all of the contents of the menu can be replaced and then
     * initialized with the refresh() method.
     * 
     * 	* This method does not accept any arguments.
     */
    public function refresh()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "refresh", Application::PRIORITY_LOW);
    }
    /**
     * Selects the currently active menu item, collapses all sub-menus and
     * triggers the menus select event.
     * 
     * 	* event Type: Event What triggered the selection.
     * @param $event
     */
    public function select($event = null)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "select", $event, Application::PRIORITY_LOW);
    }


    public function __get($strName)
    {
        switch ($strName) {
            case 'Classes': return $this->mixClasses;
            case 'Disabled': return $this->blnDisabled;
            case 'Icons': return $this->mixIcons;
            case 'Items': return $this->strItems;
            case 'Menus': return $this->strMenus;
            case 'Position': return $this->mixPosition;
            case 'Role': return $this->strRole;
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

            case 'Items':
                try {
                    $this->strItems = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'items', $this->strItems);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Menus':
                try {
                    $this->strMenus = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'menus', $this->strMenus);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Position':
                $this->mixPosition = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'position', $mixValue);
                break;

            case 'Role':
                try {
                    $this->strRole = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'role', $this->strRole);
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
            new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the menu if set to true.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'Items', 'Selector for the elements that serve as the menu items.Note: The items option should not be changed after initialization.(version added: 1.11.0)', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'Menus', 'Selector for the elements that serve as the menu container, includingsub-menus.Note: The menus option should not be changed after initialization.Existing submenus will not be updated.', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'Role', 'Customize the ARIA roles used for the menu and menu items. The defaultuses \"menuitem\" for items. Setting the role option to \"listbox\" willuse \"option\" for items. If set to null, no roles will be set, which isuseful if the menu is being controlled by another element that ismaintaining focus.Note: The role option should not be changed after initialization.Existing (sub)menus and menu items will not be updated.', Type::STRING),
        ));
    }
}
