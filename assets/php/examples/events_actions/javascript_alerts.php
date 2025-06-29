<?php
use QCubed\Action\Ajax;
use QCubed\Action\Alert;
use QCubed\Action\Confirm;
use QCubed\Action\JavaScript;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{
    protected Label $lblMessage;
    protected Button $btnJavaScript;
    protected Button $btnAlert;
    protected Button$btnConfirm;

    protected function formCreate(): void
    {
        // Define the Controls
        $this->lblMessage = new Label($this);
        $this->lblMessage->Text = 'Click on the "Confirm Example" button to change.';

        // Define different buttons to show off the various JavaScript-based Actions
        $this->btnJavaScript = new Button($this);
        $this->btnJavaScript->Text = 'JavaScript Example';
        $this->btnJavaScript->addAction(new Click(),
            new JavaScript('SomeArbitraryJavaScript();'));

        // Define different buttons to show off the various Alert-based Actions
        $this->btnAlert = new Button($this);
        $this->btnAlert->Text = 'Alert Example';
        $this->btnAlert->addAction(new Click(),
            new Alert("This is a test of the \"Alert\" example.\r\nIsn't this fun? =)"));

        // Define different buttons to show off the various Confirm-based Actions
        $this->btnConfirm = new Button($this);
        $this->btnConfirm->Text = 'Confirm Example';
        $this->btnConfirm->addAction(new Click(),
            new Confirm('Are you SURE you want to update the lblMessage?'));
        // Notice: this next action ONLY RUNS if the user hit "Ok"
        $this->btnConfirm->addAction(new Click(), new Ajax('btnConfirm_Click'));
    }

    protected function btnConfirm_Click(): void
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
