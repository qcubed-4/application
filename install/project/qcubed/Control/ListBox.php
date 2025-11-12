<?php

    namespace QCubed\Project\Control;

    use QCubed as Q;
    use QCubed\Control\ListBoxBase;
    use QCubed\Exception\Caller;
    use QCubed\Project\Application;

    /**
     * Class ListBox
     *
     * The ListBox class is based upon ListBoxBase.
     *
     * The purpose of this class is entirely to provide a place for you to make modifications of the QListBox control.
     * All updates in QCubed releases will make changes to the ListBoxBase class.  By making your modifications here
     * instead of in the base class, you can ensure that your changes are not affected by core improvements.
     *
     * @package QCubed\Project\Control
     */
    class ListBox extends ListBoxBase
    {
        ///////////////////////////
        // ListBox Preferences
        ///////////////////////////

        // Feel free to specify global display preferences/defaults for all QListBox controls
        /** @var string Default CSS class for the listbox */
        protected string $strCssClass = 'listbox';
//		protected $strFontNames = QFontFamily::Verdana;
//		protected $strFontSize = '12px';
//		protected $strWidth = '250px';

        /**
         * Creates the reset button HTML for use with multiple select boxes.
         *
         * @throws Caller
         */
        protected function getResetButtonHtml(): string
        {
            $strJavaScriptOnClick = sprintf('$j("#%s").val(null);$j("#%s").trigger("change"); return false;',
                $this->strControlId, $this->strControlId);

            $strToReturn = sprintf(' <a id="reset_ctl_%s" href="#" class="listboxReset">%s</a>',
                $this->strControlId,
                t('Reset')
            );

            Application::executeJavaScript(sprintf('$j("#reset_ctl_%s").on("%s", function(){ %s });', $this->strControlId,
                "click", $strJavaScriptOnClick));

            return $strToReturn;
        }

        /**
         * Returns the generator corresponding to this control.
         *
         * @return Q\Codegen\Generator\ListBox
         */
        public static function getCodeGenerator(): Q\Codegen\Generator\ListBox
        {
            return new Q\Codegen\Generator\ListBox(__CLASS__);
        }

    }
