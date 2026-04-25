<?php

    require_once('../qcubed.inc.php');

    use QCubed\Action\ActionParams;
    use QCubed\Action\Server;
    use QCubed\Event\Click;
    use QCubed\Exception\Caller;
    use QCubed\I18n\SimpleCacheTranslator;
    use QCubed\I18n\TranslationService;
    use QCubed\Project\Control\Button;
    use QCubed\Project\Control\FormBase;
    use Psr\SimpleCache\InvalidArgumentException;

    /**
     * Class ExamplesForm
     *
     * Represents a form that provides functionality for language switching in the application.
     * It allows users to dynamically change the application's language using buttons and ensures
     * all translations are handled and refreshed appropriately.
     *
     * NOTE: IF YOU ARE RUNNING THIS EXAMPLE FROM YOUR OWN DEVELOPMENT ENVIRONMENT,
     * you **MUST** remember to copy the custom es.po file from this directory and
     * place it into /project/i18n
     *
     */
    class ExamplesForm extends FormBase
    {
        protected Button $btnEs;
        protected Button $btnEn;

        /**
         * Configures the translation service for the application.
         *
         * This method sets up a SimpleCacheTranslator for handling translations.
         * It binds the application's language domain, specifies the i18n directory,
         * sets a default domain, and defines a temporary directory for caching.
         * The configured translator is then assigned to the global TranslationService instance.
         *
         * @return void
         * @throws InvalidArgumentException
         */
        protected function formRun(): void
        {
            // You will typically do these steps in the Application::initTranslator method
            $translator = new SimpleCacheTranslator();
            $translator->bindDomain('app', QCUBED_PROJECT_DIR . "/i18n")  // set to application's i18n directory
            ->setDefaultDomain('app')
                ->setTempDir(QCUBED_TMP_DIR . '/cache');
            TranslationService::instance()->setTranslator($translator);
        }

        /**
         * Initializes the form components and actions for language switching.
         *
         * This method sets up two buttons for language selection ('es' and 'en').
         * It defines their action parameters and binds click actions to each button.
         * Additionally, the method triggers a translation refresh to ensure the form
         * reflects the selected language dynamically.
         *
         * @return void
         * @throws Caller
         */
        protected function formCreate(): void
        {
            // Note how we do not define any TEXT properties here -- we define them
            // in the template, so that translation and language switches can occur
            // even after this form is created
            $this->btnEs = new Button($this);
            //$this->btnEs->Text = t('Switch to') . ' es';
            $this->btnEs->ActionParameter = 'es';
            $this->btnEs->addAction(new Click(), new Server('button_Click'));

            $this->btnEn = new Button($this);
            //$this->btnEn->Text = t('Switch to') . ' en';
            $this->btnEn->ActionParameter = 'en';
            $this->btnEn->addAction(new Click(), new Server('button_Click'));

            $this->refreshTranslations();
        }

        /**
         * Handles the click event for a button, updating the application's language
         * settings based on the provided parameters and refreshing translations.
         *
         * @param ActionParams $params Contains parameters for the button click event,
         * including the selected language.
         *
         * @return void
         */
        protected function button_Click(ActionParams $params): void
        {
            // NORMALLY -- these settings are set up in prepend.inc,
            // But it is pulled out here to illustrate

            $language = $params->ActionParameter;

            TranslationService::instance()->translator()->setLanguage($language, null);

            $this->refreshTranslations();
        }

        /**
         * Refreshes the text for language switch buttons based on the current translations.
         *
         * @return void
         */
        protected function refreshTranslations(): void
        {
            $this->btnEs->Text = t('Switch to') . ' es';
            $this->btnEn->Text = t('Switch to') . ' en';
        }
    }

    // Run the Form we have defined
    // The \QCubed\Project\Control\FormBase engine will look to intro.tpl.php to use as its HTML template include a file
    ExamplesForm::run('ExamplesForm');

