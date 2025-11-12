<?php
    namespace QCubed\Jqui;

    use QCubed\Project\Control\RadioButton;
    use QCubed\Type;
    use QCubed\Project\Application;
    use QCubed\ApplicationBase;
    use QCubed\Exception\InvalidCast;
    use QCubed\Exception\Caller;
    use QCubed\ModelConnector\Param as QModelConnectorParam;
    use Throwable;

    /**
     * Class RadioButtonGen
     *
     * This is the RadioButtonGen class that is automatically generated
     * by scraping the JQuery UI documentation website. As such, it includes all the options
     * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
     * the RadioButtonBase class for any glue code to make this class more
     * usable in QCubed.
     *
     * @see RadioButtonBase
     * @package QCubed\Jqui
     * @property mixed $Classes
     * Specify additional classes to add to the widget elements. Any of
     * the classes specified in the Theming section can be used as keys to
     * override their value. To learn more about this option, check out the
     * learned article about the classes option.

     *
     * @property boolean $Disabled
     * Disables the checkboxradio if set to true.
     *
     * @property boolean $Icon
     * Whether to show the checkbox or radio icon, depending on the input
     * type.
     *
     * @property string $Label
     * Text to show in the button. When not specified (null), the HTML
     * content of the associated <label> element is used.
     *
     */

    class RadioButtonGen extends RadioButton
    {
        protected string $strJavaScripts = QCUBED_JQUI_JS;
        protected string $strStyleSheets = QCUBED_JQUI_CSS;
        /** @var mixed */
        protected mixed $mixClasses = null;
        /** @var boolean */
        protected ?bool $blnDisabled = null;
        /** @var boolean */
        protected ?bool $blnIcon = null;
        /** @var string|null */
        protected ?string $strLabel = null;

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
            if (!is_null($val = $this->Icon)) {$jqOptions['icon'] = $val;}
            if (!is_null($val = $this->Label)) {$jqOptions['label'] = $val;}
            return $jqOptions;
        }

        /**
         * Return the JavaScript function to call to associate the widget with the control.
         *
         * @return string
         */
        public function getJqSetupFunction(): string
        {
            return 'checkboxradio';
        }

        /**
         * Removes the checkboxradio functionality completely. This will return
         * the element back to its pre-init state.
         *
         * 	* This method does not accept any arguments.
         */
        public function destroy(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Disables the checkboxradio.
         *
         * 	* This method does not accept any arguments.
         */
        public function disable(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Enables the checkboxradio.
         *
         * 	* This method does not accept any arguments.
         */
        public function enable(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Retrieves the checkboxradios instance object. If the element does not
         * have an associated instance, undefined is returned.
         *
         * Unlike other widget methods, instance() is safe to call on any element
         * after the checkboxradio plugin has loaded.
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
         * checkboxradio options hash.
         *
         * 	* This signature does not accept any arguments.
         */
        public function option1(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Sets the value of the checkboxradio option associated with the
         * specified optionName.
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
         * Sets one or more options for the checkboxradio.
         *
         *    * options Type: Object A map of option-value pairs to set.
         * @param array $options
         */
        public function option3(array $options): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Refreshes the visual state of the widget. Useful for updating after
         * the native elements checked or disabled state are changed
         * programmatically.
         *
         * 	* This method does not accept any arguments.
         */
        public function refresh(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "refresh", ApplicationBase::PRIORITY_LOW);
        }


        /**
         * PHP __get magic method implementation for the QRadioButton class
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
                case 'Icon': return $this->blnIcon;
                case 'Label': return $this->strLabel;
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
         * PHP __set magic method implementation
         *
         * @param string $strName Name of the property
         * @param mixed $mixValue Value of the property
         *
         * @return void
         * @throws Caller|InvalidCast|Throwable
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

                case 'Icon':
                    try {
                        $this->blnIcon = Type::Cast($mixValue, Type::BOOLEAN);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'icon', $this->blnIcon);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'Label':
                    try {
                        $this->strLabel = Type::Cast($mixValue, Type::STRING);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'label', $this->strLabel);
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
                new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the checkboxradio if set to true.', Type::BOOLEAN),
                new QModelConnectorParam (get_called_class(), 'Icon', 'Whether to show the checkbox or radio icon, depending on the input type.', Type::BOOLEAN),
                new QModelConnectorParam (get_called_class(), 'Label', 'Text to show in the button. When not specified (null), the HTMLcontent of the associated <label> element is used.', Type::STRING),
            ));
        }
    }
