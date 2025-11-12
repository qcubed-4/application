<?php
    namespace QCubed\Jqui;

    use QCubed\Control\Panel;
    use QCubed\Type;
    use QCubed\Project\Application;
    use QCubed\ApplicationBase;
    use QCubed\Exception\InvalidCast;
    use QCubed\Exception\Caller;
    use QCubed\ModelConnector\Param as QModelConnectorParam;

    /**
     * Class MenuGen
     *
     * This is the MenuGen class that is automatically generated
     * by scraping the JQuery UI documentation website. As such, it includes all the options
     * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
     * the MenuBase class for any glue code to make this class more
     * usable in QCubed.
     *
     * @see MenuBase
     * @package QCubed\Jqui
     * @property mixed $Classes
     * Specify additional classes to add to the widget elements. Any of
     * the classes specified in the Theming section can be used as keys to
     * override their value. To learn more about this option, check out the
     * learned article about the classes option.

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
     * Note: The item option should not be changed after initialization.
     * (version added: 1.11.0)
     *
     * @property string $Menus
     * Selector for the elements that serve as the menu container, including
     * submenus.
     * Note: The menu option should not be changed after initialization.
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

    class MenuGen extends Panel
    {
        protected string $strJavaScripts = QCUBED_JQUI_JS;
        protected string $strStyleSheets = QCUBED_JQUI_CSS;
        /** @var mixed */
        protected mixed $mixClasses = null;
        /** @var boolean */
        protected ?bool $blnDisabled = null;
        /** @var mixed */
        protected mixed $mixIcons = null;
        /** @var string|null */
        protected ?string $strItems = null;
        /** @var string|null */
        protected ?string $strMenus = null;
        /** @var mixed */
        protected mixed $mixPosition = null;
        /** @var string|null */
        protected ?string $strRole = null;

        /**
         * Builds the option array to be sent to the widget constructor.
         *
         * @return array key=>value array of options
         */
        protected function makeJqOptions(): array
        {
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
        public function getJqSetupFunction(): string
        {
            return 'menu';
        }

        /**
         * Removes focus from a menu, resets any active element styles and
         * triggers the menus blur event.
         * Event Type: Event What triggered the menu to blur.
         *
         * @return void
         */
        public function blur(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "blur", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Closes the currently active submenu.
         *
         * 	* event Type: Event What triggered the menu to collapse.
         *
         * @return void
         */
        public function collapse(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "collapse", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Closes all open submenus.
         *
         * 	* event Type: Event What triggered the menu to collapse.
         * 	* all Types: Boolean Indicates whether all submenus should be closed
         * or only submenus below and including the menu that is or contains the
         * target of the triggering event.
         *
         * @return void
         */
        public function collapseAll(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "collapseAll", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Removes the menu functionality completely. This will return the
         * element back to its pre-init state.
         *
         * 	* This method does not accept any arguments.
         *
         * @return void
         */
        public function destroy(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Disables the menu.
         *
         * 	* This method does not accept any arguments.
         *
         * @return void
         */
        public function disable(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Enables the menu.
         *
         * 	* This method does not accept any arguments.
         *
         * @return void
         */
        public function enable(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Opens the submenu below the currently active item if one exists.
         *
         * 	* event Type: Event What triggered the menu to expand.
         *
         * @return void
         */
        public function expand(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "expand", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Activates the given menu item and triggers the menu focus event.
         * Opens the menu items submenu if one exists.
         *
         * 	* event Type: Event What triggered the menu item to gain focus.
         * 	* item Type: jQuery The menu item to focus/activate.
         * @param string $item
         *
         * @return void
         */
        public function focus1(string $item): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "focus", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Retrieves the menus instance object. If the element does not have an
         * associated instance, undefined is returned.
         *
         * Unlike other widget methods, instance() is safe to call on any element
         * after the menu plugin has loaded.
         *
         * 	* This method does not accept any arguments.
         *
         * @return void
         */
        public function instance(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Returns a boolean value stating whether or not the currently active
         * item is the first item in the menu.
         *
         * 	* This method does not accept any arguments.
         *
         * @return void
         */
        public function isFirstItem(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "isFirstItem", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Returns a boolean value stating whether or not the currently active
         * item is the last item in the menu.
         *
         * 	* This method does not accept any arguments.
         *
         * @return void
         */
        public function isLastItem(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "isLastItem", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Moves active state to the next menu item.
         *
         * 	* event Type: Event What triggered the focus to move.
         *
         * @return void
         */
        public function next(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "next", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Moves active state to the first menu item below the bottom of a scrollable
         * menu or the last item if not scrollable.
         *
         * 	* event Type: Event What triggered the focus to move.
         *
         * @return void
         */
        public function nextPage(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "nextPage", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Gets the value currently associated with the specified optionName.
         *
         * Note: For options that have objects as their value, you can get the
         * value of a specific key by using dot notation. For example, "foo.bar"
         * would get the value of the bar property on the foo option.
         *
         * 	* optionName Type: String The name of the option to get.
         * @param string $optionName
         *
         * @return void
         */
        public function option(string $optionName): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Gets an object containing key/value pairs representing the current
         * menu options hash.
         *
         * 	* This signature does not accept any arguments.
         *
         * @return void
         */
        public function option1(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", ApplicationBase::PRIORITY_LOW);
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
         * Sets one or more options for the menu.
         *
         *    * options Type: Object A map of option-value pairs to set.
         * @param array $options
         *
         * @return void
         */
        public function option3( array $options): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Moves active state to the previous menu item.
         *
         *    * event Type: Event What triggered the focus to move.
         *
         * @return void
         */
        public function previous(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "previous", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Moves active state to the first menu item above the top of a scrollable
         * menu or the first item if not scrollable.
         *
         * 	* event Type: Event What triggered the focus to move.
         *
         * @return void
         */
        public function previousPage(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "previousPage", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Initializes submenus and menu items that have not already been
         * initialized. New menu items, including submenus, can be added to the
         * menu, or all of the contents of the menu can be replaced and then
         * initialized with the refresh() method.
         *
         * 	* This method does not accept any arguments.
         *
         * @return void
         */
        public function refresh(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "refresh", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Selects the currently active menu item, collapses all submenus and
         * triggers the menus select event.
         *
         * 	* event Type: Event What triggered the selection.
         *
         * @return void
         */
        public function select(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "select", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * PHP __get magic method implementation
         *
         * @param string $strName Name of the property
         *
         * @return mixed
         * @throws Caller
         * @throws \Exception
         */
        public function __get(string $strName): mixed
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

        /**
         * Sets a property value based on the given property name.
         *
         * This method is a magic PHP method used to dynamically set the value of a
         * property using the specified name and value. The property names are case-sensitive
         * and can include attributes like Text, Format, Template, TagName, among others.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the specified property.
         *
         * @return void
         *
         * @throws InvalidCast If the type of the value does not match the property's expected type.
         * @throws Caller If the property name is invalid or the template file does not exist.
         * @throws \Exception
         */
        public function __set(string $strName, mixed $mixValue): void
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
         *
         * @throws Caller
         */
        public static function getModelConnectorParams(): array
        {
            return array_merge(parent::GetModelConnectorParams(), array(
                new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the menu if set to true.', Type::BOOLEAN),
                new QModelConnectorParam (get_called_class(), 'Items', 'Selector for the elements that serve as the menu items.Note: The item option should not be changed after initialization. (version added: 1.11.0)', Type::STRING),
                new QModelConnectorParam (get_called_class(), 'Menus', 'Selector for the elements that serve as the menu container, including submenus. Note: The menu option should not be changed after initialization. Existing submenus will not be updated.', Type::STRING),
                new QModelConnectorParam (get_called_class(), 'Role', 'Customize the ARIA roles used for the menu and menu items. The default uses \"menuitem\" for items. Setting the role option to \"listbox\" will use \"option\" for items. If set to null, no roles will be set, which is useful if the menu is being controlled by another element that is maintaining focus.Note: The role option should not be changed after initialization.Existing (sub)menus and menu items will not be updated.', Type::STRING),
            ));
        }
    }
