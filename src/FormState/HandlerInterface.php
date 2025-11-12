<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\FormState;

    /**
     * Interface HandlerInterface
     *
     * Implements the interface for formstates. This is currently a static object.
     *
     * TODO: Change this to be a service of the singleton application object.
     *
     * @package QCubed\FormState
     */
    interface HandlerInterface {

        /**
         * Serializes and saves the given form state and returns a token representing it.
         * @param string $strFormState The state of the form to be serialized and saved.
         * @param bool $blnBackButtonFlag A flag indicating whether back button support should be enabled.
         * @return mixed A token that represents the serialized form state.
         */
        public static function save(string $strFormState, bool $blnBackButtonFlag): mixed;

        /**
         * Loads the provided post-data state for processing.
         *
         * @param mixed $strPostDataState The state of the post-data to be loaded.
         */
        public static function load(mixed $strPostDataState): string;
    }