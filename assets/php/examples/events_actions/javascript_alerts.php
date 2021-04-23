<?php
use QCubed\Action\Ajax;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{
    protected $lblMessage;
    protected $btnJavaScript;
    protected $btnAlert;
    protected $btnConfirm;

    protected function formCreate()
    {
        // Define the Controls
        $this->lblMessage = new Label($this);
        $this->lblMessage->Text = 'Click on the "Confirm Example" button to change.';

        // Define different buttons to show off the various JavaScript-based Actions
        $this->btnJavaScript = new Button($this);
        $this->btnJavaScript->Text = 'JavaScript Example';
        $this->btnJavaScript->addAction(new Click(),
            new \QCubed\Action\JavaScript('SomeArbitraryJavaScript();'));

        // Define different buttons to show off the various Alert-based Actions
        $this->btnAlert = new Button($this);
        $this->btnAlert->Text = 'Alert Example';
        $this->btnAlert->addAction(new Click(),
            new \QCubed\Action\Alert("This is a test of the \"Alert\" example.\r\nIsn't this fun? =)"));

        // Define different buttons to show off the various Confirm-based Actions
        $this->btnConfirm = new Button($this);
        $this->btnConfirm->Text = 'Confirm Example';
        $this->btnConfirm->addAction(new Click(),
            new \QCubed\Action\Confirm('Are you SURE you want to update the lblMessage?'));
        // Notice: this next action ONLY RUNS if the user hit "Ok"
        $this->btnConfirm->addAction(new Click(), new Ajax('btnConfirm_Click'));
    }

    protected function btnConfirm_Click()
    {
        // Update the Label
        if ($this->lblMessage->Text == 'Hello, world!') {
            $this->lblMessage->Text = 'Buh Bye!';
        } else {
            $this->lblMessage->Text = 'Hello, world!';
        }
    }
}

ExampleForm::run('ExampleForm');
