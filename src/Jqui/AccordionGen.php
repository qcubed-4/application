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
     * Class AccordionGen
     *
     * This is the AccordionGen class that is automatically generated
     * by scraping the JQuery UI documentation website. As such, it includes all the options
     * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
     * the AccordionBase class for any glue code to make this class more
     * usable in QCubed.
     *
     * @see AccordionBase
     * @package QCubed\Jqui
     * @property mixed $Active
     * Which panel is currently open.Multiple types are supported:
     *
     * 	* Boolean: Setting active to false will collapse all panels. This
     * requires the collapsible option to be true.
     * 	* Integer: The zero-based index of the panel that is active (open).
     * A negative value selects panels going backward from the last panel.
     *

     *
     * @property mixed $Animate
     * If and how to animate changing panels. Multiple types are supported:
     *
     * 	* Boolean: A value of false will disable animations.
     * 	* Number: Duration in milliseconds with default easing.
     * 	* String: Name of easing to use with default duration.
     *
     * 	* Object: An object containing easing and duration properties to
     * configure animations.
     *
     * 	* Can also contain a down property with any of the above options.
     * 	* "Down" animations occur when the panel being activated has a lower
     * index than the currently active panel.
     *

     *
     * @property mixed $Classes
     * Specify additional classes to add to the widget elements. Any of
     * the classes specified in the Theming section can be used as keys to
     * override their value. To learn more about this option, check out the
     * learned article about the classes option.

     *
     * @property boolean $Collapsible
     * Whether all the sections can be closed at once. Allows collapsing the
     * active section.
     *
     * @property boolean $Disabled
     * Disables the accordion if set to true.
     *
     * @property string $Event
     * The event that accordion headers will react to in order to activate
     * the associated panel. Multiple events can be specified, separated by a
     * space.
     *
     * @property mixed $Header
     * Selector for the header element, applied via .find() on the main
     * accordion element. Content panels must be the sibling immediately
     * after their associated headers.

     *
     * @property string $HeightStyle
     * Controls the height of the accordion and each panel. Possible values:
     *
     * 	* "auto": All panels will be set to the height of the tallest panel.
     * 	* "fill": Expand to the available height based on the accordion
     * parent height.
     * 	* "content": Each panel will be only as tall as its content.
     *

     *
     * @property mixed $Icons
     * Icons to use for headers, matching an icon provided by the jQuery UI
     * CSS Framework. Set too false to have no icons displayed.
     *
     * 	* header (string, default: "ui-icon-triangle-1-e")
     * 	* activeHeader (string, default: "ui-icon-triangle-1-s")
     *
     */

    class AccordionGen extends Panel
    {
        protected string $strJavaScripts = QCUBED_JQUI_JS;
        protected string $strStyleSheets = QCUBED_JQUI_CSS;
        /** @var mixed */
        protected mixed $mixActive = null;
        /** @var mixed */
        protected mixed $mixAnimate = null;
        /** @var mixed */
        protected mixed $mixClasses = null;
        /** @var boolean */
        protected ?bool $blnCollapsible = null;
        /** @var boolean */
        protected ?bool $blnDisabled = null;
        /** @var string|null */
        protected ?string $strEvent = null;
        /** @var mixed */
        protected mixed $mixHeader = null;
        /** @var string|null */
        protected ?string $strHeightStyle = null;
        /** @var mixed */
        protected mixed $mixIcons = null;

        /**
         * Builds the option array to be sent to the widget constructor.
         *
         * @return array key=>value array of options
         */
        protected function makeJqOptions(): array
        {
            $jqOptions = parent::MakeJqOptions();
            if (!is_null($val = $this->Active)) {$jqOptions['active'] = $val;}
            if (!is_null($val = $this->Animate)) {$jqOptions['animate'] = $val;}
            if (!is_null($val = $this->Classes)) {$jqOptions['classes'] = $val;}
            if (!is_null($val = $this->Collapsible)) {$jqOptions['collapsible'] = $val;}
            if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
            if (!is_null($val = $this->Event)) {$jqOptions['event'] = $val;}
            if (!is_null($val = $this->Header)) {$jqOptions['header'] = $val;}
            if (!is_null($val = $this->HeightStyle)) {$jqOptions['heightStyle'] = $val;}
            if (!is_null($val = $this->Icons)) {$jqOptions['icons'] = $val;}
            return $jqOptions;
        }

        /**
         * Return the JavaScript function to call to associate the widget with the control.
         *
         * @return string
         */
        public function getJqSetupFunction(): string
        {
            return 'accordion';
        }

        /**
         * Removes the accordion functionality completely. This will return the
         * element back to its pre-init state.
         *
         * 	* This method does not accept any arguments.
         */
        public function destroy(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Disables the accordion.
         *
         * 	* This method does not accept any arguments.
         */
        public function disable(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Enables the accordion.
         *
         * 	* This method does not accept any arguments.
         */
        public function enable(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Retrieves the accordions instance object. If the element does not have
         * an associated instance, undefined is returned.
         *
         * Unlike other widget methods, instance() is safe to call on any element
         * after the accordion plugin has loaded.
         *
         * 	* This method does not accept any arguments.
         */
        public function instance(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", ApplicationBase::PRIORITY_LOW);
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
         * accordion options hash.
         *
         * 	* This signature does not accept any arguments.
         */
        public function option1(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Sets the value of the accordion option associated with the specified
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
         * Sets one or more options for the accordion.
         *
         *    * options Type: Object A map of option-value pairs to set.
         * @param array $options
         */
        public function option3(array $options): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Process any headers and panels that were added or removed directly in
         * the DOM and recompute the height of the accordion panels. Results
         * depend on the content and the heightStyle option.
         *
         * 	* This method does not accept any arguments.
         */
        public function refresh(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "refresh", ApplicationBase::PRIORITY_LOW);
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
                case 'Active': return $this->mixActive;
                case 'Animate': return $this->mixAnimate;
                case 'Classes': return $this->mixClasses;
                case 'Collapsible': return $this->blnCollapsible;
                case 'Disabled': return $this->blnDisabled;
                case 'Event': return $this->strEvent;
                case 'Header': return $this->mixHeader;
                case 'HeightStyle': return $this->strHeightStyle;
                case 'Icons': return $this->mixIcons;
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
                case 'Active':
                    $this->mixActive = $mixValue;
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'active', $mixValue);
                    break;

                case 'Animate':
                    $this->mixAnimate = $mixValue;
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'animate', $mixValue);
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
                    try {
                        $this->blnDisabled = Type::Cast($mixValue, Type::BOOLEAN);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'disabled', $this->blnDisabled);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'Event':
                    try {
                        $this->strEvent = Type::Cast($mixValue, Type::STRING);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'event', $this->strEvent);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'Header':
                    $this->mixHeader = $mixValue;
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'header', $mixValue);
                    break;

                case 'HeightStyle':
                    try {
                        $this->strHeightStyle = Type::Cast($mixValue, Type::STRING);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'heightStyle', $this->strHeightStyle);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'Icons':
                    $this->mixIcons = $mixValue;
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'icons', $mixValue);
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
                new QModelConnectorParam (get_called_class(), 'Collapsible', 'Whether all the sections can be closed at once. Allows collapsing the active section.', Type::BOOLEAN),
                new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the accordion if set to true.', Type::BOOLEAN),
                new QModelConnectorParam (get_called_class(), 'Event', 'The event that accordion headers will react to in order to activate the associated panel. Multiple events can be specified, separated by space.', Type::STRING),
                new QModelConnectorParam (get_called_class(), 'HeightStyle', 'Controls the height of the accordion and each panel. Possible values:	* \"auto\": All panels will be set to the height of the tallest panel.	* \"fill\": Expand to the available height based on the accordion parent height.	* \"content\": Each panel will be only as tall as its content.', Type::STRING),
            ));
        }
    }
