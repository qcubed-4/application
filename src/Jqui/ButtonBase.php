<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Jqui;

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;


    /**
     * Class ButtonBase
     *
     * Implements a JQuery UI Button
     *
     * The JqButtonBase class defined here provides an interface between the generated
     * QJqButtonGen class and QCubed. This file is part of the core and will be overwritten
     * when you update QCubed. To override, make your changes to the QJqButton.class.php file instead.
     *
     * Create a button exactly as if you were creating a QButton.
     *
     * @property boolean $ShowText Causes text to be shown when icons are also defined.
     *
     * One of the JqButtonGen properties uses the same names as standard QCubed properties.
     * The Text property is a boolean in the JqUi object that specifies whether
     * to show text or just icons (provided icons are defined), and the Label property overrides
     * the standard HTML of the button. Because of the name conflict, the JQ UI property is called
     * ->JqText. You can also use ShowText as an alias to this as well so that your code is more readable.
     *  Text = standard HTML text of button
     *  Label = override of a standard HTML text if you want a button to say something different when JS is on or off
     *  ShowText = whether or not to hide the text of the button when icons are set
     *
     * @link http://jqueryui.com/button/
     *
     * @package QCubed\Jqui
     */
    class ButtonBase extends ButtonGen
    {
        /**
         * PHP Magic __get method implementation
         *
         * @param string $strName Name of the property to be fetched
         *
         * @return mixed
         * @throws Caller
         * @throws \Exception
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case 'ShowText':
                    return $this->ShowLabel;    // from Gen superclass
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
         * PHP Magic method __set implementation for this class (QButtonBase)
         *
         * @param string $strName Name of the property
         * @param mixed $mixValue Value of the property
         *
         * @return void
         * @throws InvalidCast
         * @throws Caller
         * @throws \Exception
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case 'ShowText':    // true if the text should be shown when icons are defined
                    $this->ShowLabel = $mixValue;
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