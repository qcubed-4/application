<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Control;

    use QCubed as Q;

    /**
     * Class Label
     *
     * QLabel class is used to create text on the client side.
     * By default, it will not accept raw HTML for text.
     * To enable this behavior, set Htmlentities to false.
     *
     * @package QCubed\Control
     */
    class Label extends BlockControl
    {
        ///////////////////////////
        // Protected Member Variables
        ///////////////////////////
        /** @var string HTML tag to be used when rendering this control */
        protected string $strTagName = 'span';
        /** @var bool Should htmlentities be run on the contents of this control? */
        protected bool $blnHtmlEntities = true;

        /**
         * Returns the generator corresponding to this control.
         *
         * @return Q\Codegen\Generator\Label
         */
        public static function getCodeGenerator(): Q\Codegen\Generator\Label
        {
            return new Q\Codegen\Generator\Label(__CLASS__);
        }
    }
