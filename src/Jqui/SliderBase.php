<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Jqui;

    use QCubed\ApplicationBase;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Application;
    use QCubed\Exception\Caller;
    use QCubed\Type;
    use Throwable;

    /**
     * Class SliderBase
     *
     * The SliderBase class defined here provides an interface between the generated
     * SliderGen class and QCubed. This file is part of the core and will be overwritten
     * when you update QCubed. To override, make your changes to the Slider.php file in
     * the controls folder instead.
     *
     * A slider can have one or two handles to represent a range of things, similar to a scroll bar.
     *
     * Use the inherited properties to manipulate it. Call Value or Values to get the values.
     *
     * @link http://jqueryui.com/slider/
     * @package QCubed\Jqui
     */
    class SliderBase extends SliderGen
    {

        /** Constants to use for setting Orientation */
        const string VERTICAL = 'vertical';
        const string HORIZONTAL = 'horizontal';

        /**
         * Attaches the JQueryUI widget to the HTML object if a widget is specified.
         */
        protected function makeJqWidget(): void
        {
            parent::makeJqWidget();

            Application::executeJsFunction('qcubed.slider', $this->getJqControlId(), ApplicationBase::PRIORITY_HIGH);
        }

        /**
         * Returns the state data to restore later.
         * @return array|null
         */
        protected function getState(): ?array
        {
            if ($this->mixRange === true) {
                return ['values' => $this->Values];
            } else {
                return ['value' => $this->Value];
            }
        }

        /**
         * Restore the state of the control.
         * @param mixed $state
         */
        protected function putState(mixed $state): void
        {
            if (isset($state['values'])) {
                $this->Values = $state['values'];
            } elseif (isset($state['value'])) {
                $this->Value = $state['value'];
            }
        }


        /**
         * @throws InvalidCast
         * @throws Caller
         * @throws Throwable Exception
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case '_Value':    // Internal Only. Used by JS above. Do Not Call.
                    try {
                        $this->intValue = Type::cast($mixValue, Type::INTEGER);
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;

                case '_Values': // Internal Only. Used by JS above. Do Not Call.
                    try {
                        $aValues = explode(',', $mixValue);
                        $aValues[0] = Type::cast($aValues[0],
                            Type::INTEGER); // important to make sure JS sends values as into instead of strings
                        $aValues[1] = Type::cast($aValues[1],
                            Type::INTEGER); // important to make sure JS sends values as into instead of strings
                        $this->arrValues = $aValues;
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;

                default:
                    try {
                        parent::__set($strName, $mixValue);
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;
            }
        }
    }
