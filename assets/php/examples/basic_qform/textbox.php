<?php

use QCubed\Action\ActionParams;
use QCubed\Action\Ajax;
use QCubed\Action\Server;
use QCubed\Control\CsvTextBox;
use QCubed\Control\EmailTextBox;
use QCubed\Control\FloatTextBox;
use QCubed\Control\IntegerTextBox;
use QCubed\Control\UrlTextBox;
use QCubed\Event\Click;
use QCubed\Project\Application;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\TextBox;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Controls
class ExamplesForm extends FormBase
{

    // Local declarations of our Controls
    protected TextBox $txtBasic;
    protected IntegerTextBox $txtInt;
    protected FloatTextBox $txtFlt;
    protected CsvTextBox $txtList;
    protected EmailTextBox $txtEmail;
    protected UrlTextBox $txtUrl;
    protected TextBox $txtCustom;
    protected Button $btnValidate;
    protected EmailTextBox $txtMultipleEmails;
    protected Button $btnEmailValidate;

    // Initialize our Controls during the Form Creation process
    protected function formCreate(): void
    {
        // Define our Label
        $this->txtBasic = new TextBox($this);
        $this->txtBasic->Name = t("Basic");

        $this->txtBasic = new TextBox($this);
        $this->txtBasic->MaxLength = 5;

        $this->txtInt = new IntegerTextBox($this);
        $this->txtInt->Maximum = 10;

        $this->txtFlt = new FloatTextBox($this);

        $this->txtList = new CsvTextBox($this);
        $this->txtList->MinItemCount = 2;
        $this->txtList->MaxItemCount = 5;

        $this->txtEmail = new EmailTextBox($this);
        $this->txtUrl = new UrlTextBox($this);


        $this->txtCustom = new TextBox($this);
        // These parameters are fed into filter_var. See PHP doc on filter_var() for more info.
        $this->txtCustom->ValidateFilter = FILTER_VALIDATE_REGEXP;
        $this->txtCustom->ValidateFilterOptions = array('options' => array('regexp' => '/^(0x)?[0-9A-F]*$/i')); // must be a hex decimal, optional leading 0x
        $this->txtCustom->LabelForInvalid = 'Hex value required.';

        $this->btnValidate = new Button ($this);
        $this->btnValidate->Text = "Filter and Validate";
        $this->btnValidate->addAction(new Click(), new Server()); // just validates
        $this->btnValidate->CausesValidation = true;

        $this->txtMultipleEmails = new EmailTextBox($this);
        $this->txtMultipleEmails->AllowMultipleEmails = true;

        $this->btnEmailValidate = new Button ($this);
        $this->btnEmailValidate->Text = "Validate multiple emails";
        $this->btnEmailValidate->addAction(new Click(), new Ajax("btnClick_Validate")); // just validates
    }

    protected function btnClick_Validate(ActionParams $params): void
    {
        Application::displayAlert(print_r($this->txtMultipleEmails->getGroupedEmails(), true));
    }
}

// Run the Form we have defined
// The \QCubed\Project\Control\FormBase engine will look to intro.tpl.php to use as its HTML template include a file
ExamplesForm::run('ExamplesForm');
