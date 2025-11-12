<?php

    namespace QCubed\Project\Control;

    use QCubed as Q;
    use QCubed\Control\TextBoxBase;
    use QCubed\Exception\Caller;

    /**
     * Class TextBox
     * @package QCubed\Project\Control
     */
    class TextBox extends TextBoxBase
    {
        // Feel free to specify global display preferences/defaults for all QTextBox controls
        /** @var string Default CSS class for the textbox */
        protected string $strCssClass = 'textbox';

        /**
         * Constructor method for initializing the object.
         *
         * @param mixed $objParentObject The parent object of this control.
         * @param string|null $strControlId An optional control ID for the object.
         * @return void
         * @throws Caller
         */
        public function __construct(mixed $objParentObject, ?string $strControlId = null)
        {
            parent::__construct($objParentObject, $strControlId);

            /**
             * This is the default purifier, used to purify text coming from users to make sure it doesn't contain
             * malicious cross-site scripts. If you install HTML Purifier, it will use that one. Otherwise, it will do its
             * best by just putting HTML entities on what is coming through, which is not very good. You really should install
             * HTML purifier.
             */

            if (class_exists('HTMLPurifier')) {
                $this->strCrossScripting = self::XSS_HTML_PURIFIER;
            } else {
                $this->strCrossScripting = self::XSS_HTML_ENTITIES;
            }

        }

        /**
         * Returns the generator corresponding to this control.
         *
         * @return Q\Codegen\Generator\TextBox
         */
        public static function getCodeGenerator(): Q\Codegen\Generator\TextBox
        {
            return new Q\Codegen\Generator\TextBox(__CLASS__);
        }
    }
