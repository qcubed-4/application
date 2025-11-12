<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Control;


    use QCubed\Project\Control\ControlBase;

    /**
     * Class ActionControl
     *
     * It basically pre-sets CausesValidation to be true (b/c most of the time,
     * when a button is clicked, we'd assume that we want the validation to kick off),
     * And it pre-defines ParsePostData and Validates.
     *
     * @package QCubed\Control
     */
    abstract class ActionControl extends ControlBase
    {
        protected mixed $mixCausesValidation = self::CAUSES_VALIDATION_ALL;

        /**
         * This function should contain the POST data parsing mechanism
         */
        public function parsePostData(): void
        {
        }

        /**
         * Checks whether the value submitted via POST for the control was valid or not
         * The code to test the validity will have to reside in this function
         * @return bool Whether or not the validation succeeded
         */
        public function validate(): bool
        {
            return true;
        }
    }