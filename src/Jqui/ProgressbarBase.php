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
    use QCubed\Exception\Caller;
    use QCubed\Project\Application;
    use QCubed\Type;

    /**
     * Class ProgressbarBase
     *
     * The ProgressbarBase class defined here provides an interface between the generated
     * ProgressbarGen class and QCubed. This file is part of the core and will be overwritten
     * when you update QCubed. To override, see theQProgressbar.php file in the controls'
     * folder.
     *
     * Use the inherited interface to control the progress bar.
     *
     * @link http://jqueryui.com/progressbar/
     * @package QCubed\Jqui
     */
    class ProgressbarBase extends ProgressbarGen
    {
        /**
         * The JavaScript for the control to be sent to the client.
         */
        protected function makeJqWidget(): void
        {
            parent::makeJqWidget();

            Application::executeJsFunction('qcubed.progressbar', $this->getJqControlId(), ApplicationBase::PRIORITY_HIGH);
        }

        /**
         * Returns the state data to restore later.
         * @return array|null
         */
        protected function getState(): ?array
        {
            return ['value' => $this->Value];
        }

        /**
         * Restore the state of the control.
         * @param mixed $state
         */
        protected function putState(mixed $state): void
        {
            if (isset($state['value'])) {
                $this->Value = $state['value'];
            }
        }

        /**
         * PHP __set magic method
         *
         * @param string $strName Name of the property
         * @param mixed $mixValue Value of the property
         *
         * @throws Caller
         * *@throws \Exception
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case '_Value':    // Internal Only. Used by JS above. Do Not Call.
                    try {
                        $this->Value = Type::cast($mixValue, Type::INTEGER);
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
