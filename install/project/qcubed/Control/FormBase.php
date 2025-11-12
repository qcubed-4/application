<?php

    namespace QCubed\Project\Control;

    /**
     * Class FormBase
     *
     * This form base gives you opportunities to override key functions and values for all of your forms.
     *
     * @package QCubed\Project\Control
     */
    abstract class FormBase extends \QCubed\Control\FormBase
    {
        ///////////////////////////
        // Form Preferences
        ///////////////////////////

        /**
         * If you wish to encrypt the resulting formstate data to be put on the form (via
         * QCryptography), please specify a key to use.  The default cipher and encrypt mode
         * on QCryptography will be used, and because the resulting encrypted data will be
         * sent via HTTP POST, it will be Base64 encoded.
         *
         * @var string|null EncryptionKey the key to use, or NULL if no encryption is required
         * TODO: Do this some other way, likely more specifically in the formstate handlers that use it
         */
        public static ?string $EncryptionKey = null;

        /**
         * The FormStateHandler to use to handle the actual serialized form.
         * Please refer to the configuration.inc.php file (in the includes / configuration directory) to learn more
         * about what FORM_STATE_HANDLER does. Though you can change it here,
         * try to change the FORM_STATE_HANDLER in the configuration file alone.
         *
         * It overrides the default value in the FormBase Class file
         *
         * @var string FormStateHandler the classname of the FormState handler to use
         */
        public static string $FormStateHandler = FORM_STATE_HANDLER;

        /**
         * These are the list of JavaScript files that should NOT be loaded by the framework,
         * event if a particular control asks for it.
         *
         * In particular, specify any files that you know to be already loaded by a hardcoded
         * include of the JavaScript in your HTML or template files.
         *
         * @var array
         */
        protected array $strIgnoreJavaScriptFileArray = array();

        /**
         * These are the list of style sheet files that should NOT be loaded by the framework,
         * event if a particular control asks for it.
         *
         * In particular, specify any files that you know to be already loaded by a hardcoded
         * include of the style sheet in your HTML or template files.
         *
         * @var array
         */
        protected array $strIgnoreStyleSheetFileArray = array();

        /**
         * Return any JavaScripts that should be loaded always. In particular, these would
         * be JavaScripts that you would use in your application even if no particular control
         * asked for it. For example, if you did some manual styling with Bootstrap and you
         * needed the bootstrap JavaScript file.
         *
         * @return array
         */
        protected function getFormJavaScripts(): array
        {
            return parent::getFormJavaScripts();
        }

        /**
         * Return any stylesheets that should be loaded always. These would include
         * stylesheets that are utilized globally in your application, regardless
         * of whether a specific control requires them. For example, a global
         * Bootstrap CSS can be included for consistent styling.
         *
         * @return array
         */

        protected function getFormStyles(): array
        {
            //$a[] = QCUBED_BOOTSTRAP_CSS;
            return parent::getFormStyles();
        }
    }
