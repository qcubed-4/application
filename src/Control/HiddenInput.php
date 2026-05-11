<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Control;

    /**
     * Represents a hidden input field in a form, inheriting functionality
     * from the TextBoxBase class. The text mode is configured to "hidden".
     */
    class HiddenInput extends TextBoxBase
    {
        protected string $strTextMode = self::HIDDEN;
    }