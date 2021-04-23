<?php
use QCubed\Action\Server;
use QCubed\Control\CsvTextBox;
use QCubed\Control\EmailTextBox;
use QCubed\Control\FloatTextBox;
use QCubed\Control\IntegerTextBox;
use QCubed\Control\UrlTextBox;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\TextBox;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Qcontrols
class ExamplesForm extends \QCubed\Project\Control\FormBase
{

    // Local declarations of our Qcontrols
    protected $txtBasic;
    protected $txtInt;
    protected $txtFlt;
    protected $txtList;
    protected $txtEmail;
    protected $txtUrl;
    protected $txtCustom;
    protected $btnValidate;

    // Initialize our Controls during the Form Creation process
    protected function formCreate()
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
    }

}

// Run the Form we have defined
// The \QCubed\Project\Control\FormBase engine will look to intro.tpl.php to use as its HTML template include file
ExamplesForm::run('ExamplesForm');
