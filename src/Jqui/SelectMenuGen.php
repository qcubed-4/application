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
     * This is the SelectMenuGen class that is automatically generated
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
     * Specify additional classes to add to the widget elements. Any of
     * the classes specified in the Theming section can be used as keys to
     * override their value. To learn more about this option, check out the
     * learned article about the classes option.

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
     */

    class SelectMenuGen extends QCubed\Project\Control\ListBox
    {
        protected string $strJavaScripts = QCUBED_JQUI_JS;
        protected string $strStyleSheets = QCUBED_JQUI_CSS;
        /** @var mixed */
        protected mixed $mixAppendTo = null;
        /** @var mixed */
        protected mixed $mixClasses = null;
        /** @var boolean */
        protected ?bool $blnDisabled = null;
        /** @var mixed */
        protected mixed $mixIcons = null;
        /** @var mixed */
        protected mixed $mixPosition = null;
        /** @var mixed */
        protected mixed $mixWidth = null;

        /**
         * Builds the option array to be sent to the widget constructor.
         *
         * @return array key=>value array of options
         */
        protected function makeJqOptions(): array
        {
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
        public function getJqSetupFunction(): string
        {
            return 'selectmenu';
        }

        /**
         * Closes the menu.
         *
         * 	* This method does not accept any arguments.
         */
        public function close(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "close", QCubed\ApplicationBase::PRIORITY_LOW);
        }
        /**
         * Removes the selectmenu functionality completely. This will return the
         * element back to its pre-init state.
         *
         * 	* This method does not accept any arguments.
         */
        public function destroy(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", QCubed\ApplicationBase::PRIORITY_LOW);
        }
        /**
         * Disables the selectmenu.
         *
         * 	* This method does not accept any arguments.
         */
        public function disable(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", QCubed\ApplicationBase::PRIORITY_LOW);
        }
        /**
         * Enables the selectmenu.
         *
         * 	* This method does not accept any arguments.
         */
        public function enable(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", QCubed\ApplicationBase::PRIORITY_LOW);
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
        public function instance(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", QCubed\ApplicationBase::PRIORITY_LOW);
        }
        /**
         * Returns a jQuery object containing the menu element.
         *
         * 	* This method does not accept any arguments.
         */
        public function menuWidget(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "menuWidget", QCubed\ApplicationBase::PRIORITY_LOW);
        }
        /**
         * Opens the menu.
         *
         * 	* This method does not accept any arguments.
         */
        public function open(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "open", QCubed\ApplicationBase::PRIORITY_LOW);
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
         * selectmenu options hash.
         *
         * 	* This signature does not accept any arguments.
         */
        public function option1(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", QCubed\ApplicationBase::PRIORITY_LOW);
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
         *    * optionName Type: String The name of the option to set.
         *    * value Type: Object A value to set for the option.
         * @param string $optionName
         * @param string $value
         */
        public function option2(string $optionName, string $value): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, $value, QCubed\ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Sets one or more options for the selectmenu.
         *
         *    * options Type: Object A map of option-value pairs to set.
         * @param array $options
         */
        public function option3(array $options): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, QCubed\ApplicationBase::PRIORITY_LOW);
        }
        /**
         * Parses the original element and re-renders the menu. Processes any
         * <option> or <optgroup> elements that were added, removed or disabled.
         *
         * 	* This method does not accept any arguments.
         */
        public function refresh(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "refresh", QCubed\ApplicationBase::PRIORITY_LOW);
        }


        /**
         * Magic method to retrieve property values.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed Returns the value of the requested property.
         * @throws Caller If the property does not exist.
         * @throws \Exception
         */
        public function __get(string $strName): mixed
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

        /**
         * Magic method to set the value of a property.
         * Allows dynamic assignment of properties and handles typecasting or validation for certain attributes.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the property.
         *
         * @return void
         * @throws Caller Thrown when attempting to set an undefined property.
         * @throws InvalidCast Thrown when the value cannot be cast to the required type.*@throws \Exception
         * @throws \Exception
         */
        public function __set(string $strName, mixed $mixValue): void
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
         *
         * @throws Caller
         */
        public static function getModelConnectorParams(): array
        {
            return array_merge(parent::GetModelConnectorParams(), array(
                new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the selectmenu if set to true.', Type::BOOLEAN),
            ));
        }
    }
