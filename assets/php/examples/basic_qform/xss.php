<?php
use QCubed\Action\Ajax;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\TextBox;

require_once('../qcubed.inc.php');

class ExamplesForm extends FormBase
{

    /** @var TextBox */
    protected $txtTextbox1;

    /** @var Label */
    protected $lblLabel1;

    /** @var Button */
    protected $btnButton1;

    /** @var TextBox */
    protected $txtTextbox2;

    /** @var Label */
    protected $lblLabel2;

    /** @var Button */
    protected $btnButton2;

    /** @var TextBox */
    protected $txtTextbox3;

    /** @var Label */
    protected $lblLabel3;

    /** @var Button */
    protected $btnButton3;

    /** @var TextBox */
    protected $txtTextbox4;

    /** @var Label */
    protected $lblLabel4;

    /** @var Button */
    protected $btnButton4;

    /** @var TextBox */
    protected $txtTextbox5;

    /** @var Label */
    protected $lblLabel5;

    /** @var Button */
    protected $btnButton5;

    // Initialize our Controls during the Form Creation process
    protected function formCreate()
    {
        // default protection, will use built-in PHP String sanitizer
        $this->txtTextbox1 = new TextBox($this);
        $this->txtTextbox1->Text = 'Hello!';
        $this->txtTextbox1->Width = 500;
        $this->txtTextbox1->CrossScripting = TextBox::XSS_PHP_SANITIZE;

        $this->lblLabel1 = new Label($this);
        $this->lblLabel1->HtmlEntities = false;
        $this->lblLabel1->Text = "";

        $this->btnButton1 = new Button($this);
        $this->btnButton1->Text = "Parse and Display";
        $this->btnButton1->addAction(new Click(), new Ajax('btnButton1_Click'));

        // htmlentities mode
        $this->txtTextbox2 = new TextBox($this);
        $this->txtTextbox2->CrossScripting = TextBox::XSS_HTML_ENTITIES;
        $this->txtTextbox2->Text = 'Hello!';
        $this->txtTextbox2->Width = 500;

        $this->lblLabel2 = new Label($this);
        $this->lblLabel2->HtmlEntities = false;
        $this->lblLabel2->Text = "";

        $this->btnButton2 = new Button($this);
        $this->btnButton2->Text = "Parse and Display";
        $this->btnButton2->addAction(new Click(), new Ajax('btnButton2_Click'));

        // full protection with the HTMLPurifier defaults
        $this->txtTextbox3 = new TextBox($this);
        $this->txtTextbox3->CrossScripting = TextBox::XSS_HTML_PURIFIER;
        $this->txtTextbox3->Text = 'Hello!';
        $this->txtTextbox3->Width = 500;

        $this->lblLabel3 = new Label($this);
        $this->lblLabel3->HtmlEntities = false;
        $this->lblLabel3->Text = "";

        $this->btnButton3 = new Button($this);
        $this->btnButton3->Text = "Parse and Display";
        $this->btnButton3->addAction(new Click(), new Ajax('btnButton3_Click'));

        // full protection with an allowed list of tags
        $this->txtTextbox4 = new TextBox($this);
        $this->txtTextbox4->CrossScripting = TextBox::XSS_HTML_PURIFIER;
        $this->txtTextbox4->setPurifierConfig("HTML.Allowed", "b,strong,i,em,img[src]");
        $this->txtTextbox4->Text = 'Hello!';
        $this->txtTextbox4->Width = 500;

        $this->lblLabel4 = new Label($this);
        $this->lblLabel4->HtmlEntities = false;
        $this->lblLabel4->Text = "";

        $this->btnButton4 = new Button($this);
        $this->btnButton4->Text = "Parse and Display";
        $this->btnButton4->addAction(new Click(), new Ajax('btnButton4_Click'));

        // the textbox won't have the XSS protection!
        $this->txtTextbox5 = new TextBox($this);
        $this->txtTextbox5->CrossScripting = TextBox::XSS_ALLOW;
        $this->txtTextbox5->Text = 'Hello!';
        $this->txtTextbox5->Width = 500;

        $this->lblLabel5 = new Label($this);
        $this->lblLabel5->HtmlEntities = false;
        $this->lblLabel5->Text = "";

        $this->btnButton5 = new Button($this);
        $this->btnButton5->Text = "Parse and Display";
        $this->btnButton5->addAction(new Click(), new Ajax('btnButton5_Click'));
    }

    protected function btnButton1_Click($strFormId, $strControlId, $strParameter)
    {
        $this->lblLabel1->Text = $this->txtTextbox1->Text;
    }

    protected function btnButton2_Click($strFormId, $strControlId, $strParameter)
    {
        $this->lblLabel2->Text = $this->txtTextbox2->Text;
    }

    protected function btnButton3_Click($strFormId, $strControlId, $strParameter)
    {
        $this->lblLabel3->Text = $this->txtTextbox3->Text;
    }

    protected function btnButton4_Click($strFormId, $strControlId, $strParameter)
    {
        $this->lblLabel4->Text = $this->txtTextbox4->Text;
    }

    protected function btnButton5_Click($strFormId, $strControlId, $strParameter)
    {
        $this->lblLabel5->Text = $this->txtTextbox5->Text;
    }
}

// Run the Form we have defined
ExamplesForm::run('ExamplesForm');
