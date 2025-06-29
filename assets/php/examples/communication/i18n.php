<?php

use QCubed\Action\ActionParams;
use QCubed\Action\Server;
use QCubed\Event\Click;
use QCubed\I18n\SimpleCacheTranslator;
use QCubed\I18n\TranslationService;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

// NOTE: IF YOU ARE RUNNING THIS EXAMPLE FROM YOUR OWN DEVELOPMENT ENVIRONMENT,
// you **MUST** remember to copy the custom es.po file from this directory and
// place it into /project/i18n

class ExamplesForm extends FormBase
{
    protected Button $btnEs;
    protected Button $btnEn;

    protected function formRun(): void
    {
        // You will typically do these steps in the Application::initTranslator method
        $translator = new SimpleCacheTranslator();
        $translator->bindDomain('app', QCUBED_PROJECT_DIR . "/i18n")  // set to application's i18n directory
            ->setDefaultDomain('app')
            ->setTempDir(QCUBED_TMP_DIR . '/cache');
        TranslationService::instance()->setTranslator($translator);
    }

    // Initialize our Controls during the Form Creation process
    protected function formCreate(): void
    {
        // Note how we do not define any TEXT properties here -- we define them
        // in the template, so that translation and language switches can occur
        // even after this form is created
        $this->btnEs = new Button($this);
        $this->btnEs->Text = t('Switch to') . ' es';
        $this->btnEs->ActionParameter = 'es';
        $this->btnEs->addAction(new Click(), new Server('button_Click'));

        $this->btnEn = new Button($this);
        $this->btnEn->Text = t('Switch to') . ' en';
        $this->btnEn->ActionParameter = 'en';
        $this->btnEn->addAction(new Click(), new Server('button_Click'));
    }

    // The "btnButton_Click" Event handler
    protected function button_Click(ActionParams $params): void
    {
        // NORMALLY -- these settings are set up in prepend.inc,
        // But it is pulled out here to illustrate

        $language = $params->ActionParameter;

        TranslationService::instance()->translator()->setLanguage($language, null);
    }
}

// Run the Form we have defined
// The \QCubed\Project\Control\FormBase engine will look to intro.tpl.php to use as its HTML template include a file
ExamplesForm::run('ExamplesForm');

