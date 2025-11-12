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
     * Class SliderGen
     *
     * This is the SliderGen class that is automatically generated
     * by scraping the JQuery UI documentation website. As such, it includes all the options
     * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
     * the SliderBase class for any glue code to make this class more
     * usable in QCubed.
     *
     * @see SliderBase
     * @package QCubed\Jqui
     * @property mixed $Animate
     * Whether to slide the handle smoothly when the user clicks on the
     * slider track. Also accepts any valid animation duration.Multiple types are
     * supported:
     *
     * 	* Boolean: When set to true, the handle will animate with the default
     * duration.
     * 	* String: The name of a speed, such as "fast" or "slow".
     * 	* Number: The duration of the animation, in milliseconds.
     *

     *
     * @property mixed $Classes
     * Specify additional classes to add to the widget elements. Any of
     * the classes specified in the Theming section can be used as keys to
     * override their value. To learn more about this option, check out the
     * learned article about the classes option.
     *
     * @property boolean $Disabled
     * Disables the slider if set to true.
     *
     * @property integer $Max
     * The maximum value of the slider.
     *
     * @property integer $Min
     * The minimum value of the slider.
     *
     * @property string $Orientation
     * Determines whether the slider handles move horizontally (min on the left, max on the * right) or vertically (min on the bottom, max on top). Possible
     * values: "horizontal", "vertical".
     *
     * @property mixed $Range
     * Whether the slider represents a range, Multiple types are supported:
     *
     * 	* Boolean: If set to true, the slider will detect if you have two
     * handles and create a stable range element between these two.
     * 	* String: Either "min" or "max". A min range goes from the slider
     * min to one handle. A max range goes from one handle to the slider max.
     *

     *
     * @property integer $Step
     * Determines the size or amount of each interval or step the slider
     * takes between the min and max. The full specified value range of the
     * slider (max - min) should be evenly divisible by the step.
     *
     * @property integer $Value
     * Set the slider value if there is only one handle. If
     * there is more than one handle, set the first
     * handle.
     *
     * @property array $Values
     * This option can be used to specify multiple handles. If the range
     * option is set to true, the length of values should be 2.
     *
     */

    class SliderGen extends Panel
    {
        protected string $strJavaScripts = QCUBED_JQUI_JS;
        protected string $strStyleSheets = QCUBED_JQUI_CSS;
        /** @var mixed */
        protected mixed $mixAnimate = null;
        /** @var mixed */
        protected mixed $mixClasses = null;
        /** @var boolean */
        protected ?bool $blnDisabled = null;
        /** @var integer|null */
        protected ?int $intMax = null;
        /** @var integer|null */
        protected ?int $intMin = null;
        /** @var string|null */
        protected ?string $strOrientation = null;
        /** @var mixed */
        protected mixed $mixRange = null;
        /** @var integer|null */
        protected ?int $intStep = null;
        /** @var integer|null */
        protected ?int $intValue = null;
        /** @var array|null */
        protected ?array $arrValues = null;

        /**
         * Builds the option array to be sent to the widget constructor.
         *
         * @return array key=>value array of options
         */
        protected function makeJqOptions(): array
        {
            $jqOptions = parent::MakeJqOptions();
            if (!is_null($val = $this->Animate)) {$jqOptions['animate'] = $val;}
            if (!is_null($val = $this->Classes)) {$jqOptions['classes'] = $val;}
            if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
            if (!is_null($val = $this->Max)) {$jqOptions['max'] = $val;}
            if (!is_null($val = $this->Min)) {$jqOptions['min'] = $val;}
            if (!is_null($val = $this->Orientation)) {$jqOptions['orientation'] = $val;}
            if (!is_null($val = $this->Range)) {$jqOptions['range'] = $val;}
            if (!is_null($val = $this->Step)) {$jqOptions['step'] = $val;}
            if (!is_null($val = $this->Value)) {$jqOptions['value'] = $val;}
            if (!is_null($val = $this->Values)) {$jqOptions['values'] = $val;}
            return $jqOptions;
        }

        /**
         * Return the JavaScript function to call to associate the widget with the control.
         *
         * @return string
         */
        public function getJqSetupFunction(): string
        {
            return 'slider';
        }


        /**
         * Removes the slider functionality completely. This will return the
         * element back to its pre-init state.
         *
         * 	* This method does not accept any arguments.
         */
        public function destroy(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", QCubed\ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Disables the slider.
         *
         * 	* This method does not accept any arguments.
         */
        public function disable(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", QCubed\ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Enables the slider.
         *
         * 	* This method does not accept any arguments.
         */
        public function enable(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", QCubed\ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Retrieves the slider instance object. If the element does not have an
         * associated instance, undefined is returned.
         *
         * Unlike other widget methods, instance() is safe to call on any element
         * after the slider plugin has loaded.
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
         * slider options hash.
         *
         * 	* This signature does not accept any arguments.
         */
        public function option1(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", QCubed\ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Sets the value of the slider option associated with the specified
         * optionName.
         *
         * Note: For options that have objects as their value, you can set the
         * value of just one property by using dot notation for optionName. For
         * example, "foo.bar" would update only the bar property of the foo
         * option.
         * optionName Type: String The name of the option to set.
         * Value Type: Object A value to set for the option.
         *
         *    * optionName Type: String The name of the option to set.
         *    * value Type: Mixed The value to assign to the option.
         * @param string $optionName
         * @param mixed $value
         * @return void
         */
        public function option2(string $optionName, mixed $value): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, $value, QCubed\ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Sets one or more options for the slider.
         *
         *    * options Type: Object A map of option-value pairs to set.
         * @param object $options
         */
        public function option3(object $options): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, QCubed\ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Get the value of the slider.
         *
         * 	* This signature does not accept any arguments.
         */
        public function value(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "value", QCubed\ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Set the value of the slider.
         *
         *    * value Type: Number The value to set.
         * @param int $value
         */
        public function value1(int $value): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "value", $value, QCubed\ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Get the value for all handles.
         *
         * 	* This signature does not accept any arguments.
         */
        public function values(): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "values", QCubed\ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Get the value for the specified handle.
         *
         *    * index Type: Integer The zero-based index of the handle.
         * @param int $index
         */
        public function values1(int $index): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "values", $index, QCubed\ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Set the value for the specified handle.
         *
         *    * index Type: Integer The zero-based index of the handle.
         *    * value Type: Number The value to set.
         * @param int $index
         * @param int $value
         */
        public function values2(int $index, int $value): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "values", $index, $value, QCubed\ApplicationBase::PRIORITY_LOW);
        }

        /**
         * Set the value for all handles.
         *
         *    * values Type: Array The values to set.
         * @param array $values
         */
        public function values3(array $values): void
        {
            Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "values", $values, QCubed\ApplicationBase::PRIORITY_LOW);
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
                case 'Animate': return $this->mixAnimate;
                case 'Classes': return $this->mixClasses;
                case 'Disabled': return $this->blnDisabled;
                case 'Max': return $this->intMax;
                case 'Min': return $this->intMin;
                case 'Orientation': return $this->strOrientation;
                case 'Range': return $this->mixRange;
                case 'Step': return $this->intStep;
                case 'Value': return $this->intValue;
                case 'Values': return $this->arrValues;
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
                case 'Animate':
                    $this->mixAnimate = $mixValue;
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'animate', $mixValue);
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

                case 'Max':
                    try {
                        $this->intMax = Type::Cast($mixValue, Type::INTEGER);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'max', $this->intMax);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'Min':
                    try {
                        $this->intMin = Type::Cast($mixValue, Type::INTEGER);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'min', $this->intMin);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'Orientation':
                    try {
                        $this->strOrientation = Type::Cast($mixValue, Type::STRING);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'orientation', $this->strOrientation);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'Range':
                    $this->mixRange = $mixValue;
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'range', $mixValue);
                    break;

                case 'Step':
                    try {
                        $this->intStep = Type::Cast($mixValue, Type::INTEGER);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'step', $this->intStep);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'Value':
                    try {
                        $this->intValue = Type::Cast($mixValue, Type::INTEGER);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'value', $this->intValue);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case 'Values':
                    try {
                        $this->arrValues = Type::Cast($mixValue, Type::ARRAY_TYPE);
                        $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'values', $this->arrValues);
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
                new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the slider if set to true.', Type::BOOLEAN),
                new QModelConnectorParam (get_called_class(), 'Max', 'The maximum value of the slider.', Type::INTEGER),
                new QModelConnectorParam (get_called_class(), 'Min', 'The minimum value of the slider.', Type::INTEGER),
                new QModelConnectorParam (get_called_class(), 'Orientation', 'Determines whether the slider handles move horizontally (min on the left, max on the right) or vertically (min on the bottom, max on top). Possible values: \"horizontal\", \"vertical\".', Type::STRING),
                new QModelConnectorParam (get_called_class(), 'Step', 'Determines the size or amount of each interval or step the slider takes between the min and max. The full specified value range of the slider (max - min) should be evenly divisible by the step.', Type::INTEGER),
                new QModelConnectorParam (get_called_class(), 'Value', 'Determines the value of the slider if there\'s only one handle. If there is more than one handle, determines the value of the firsthand.', Type::INTEGER),
                new QModelConnectorParam (get_called_class(), 'Values', 'This option can be used to specify multiple handles. If the range option is set to true, the length of values should be 2.', Type::ARRAY_TYPE),
            ));
        }
    }
