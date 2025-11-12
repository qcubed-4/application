<?php
use QCubed\Action\Server;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Controls
class ExamplesForm extends FormBase
{

    // Local declarations of our Controls
    protected Label $lblMessage;
    protected Button $btnButton;

    protected function formRun(): void
    {
        _p('<br><br><br><br><br>', false); // Compensating for the example header

        _p('<b>formRun</b> called<br/>', false);
    }

    // Initialize our Controls during the Form Creation process
    protected function formCreate(): void
    {
        _p('<b>formCreate</b> called<br/>', false);
        // Define the Label -- Set HtmlEntities to false because we intend on hard coding HTML into the Control
        $this->lblMessage = new Label($this);
        $this->lblMessage->HtmlEntities = false;
        $this->lblMessage->Text = 'Click the button to change my message.';

        // Define the Button
        $this->btnButton = new Button($this);
        $this->btnButton->Text = 'Click Me!';

        // We add CausesValidation to the Button so that formValidate() will get called
        $this->btnButton->CausesValidation = true;

        // Add a Click event handler to the button -- the action to run is a ServerAction (e.g., PHP method)
        // called "btnButton_Click"
        $this->btnButton->addAction(new Click(), new Server('btnButton_Click'));
    }

    protected function formLoad(): void
    {
        _p('<b>formLoad</b> called<br/>', false);
    }

    protected function formPreRender(): void
    {
        _p('<b>formPreRender</b> called<br/>', false);
    }

    protected function formValidate(): bool
    {
        _p('<b>formValidate</b> called<br/>', false);

        // Form_Validate needs to return true or false
        return true;
    }

    protected function formExit(): void
    {
        _p('<b>formExit</b> called<br/>', false);
    }

    // The "btnButton_Click" Event handler
    protected function btnButton_Click(string $strFormId, string $strControlId,string  $strParameter): void
    {
        _p('<b>btnButton_Click</b> called<br/>', false);
        $this->lblMessage->Text = 'Hello, world!<br/>';
        $this->lblMessage->Text .= 'Note that instead of <b>formCreate</b> being called, we are now calling <b>formLoad</b> and <b>btnButton_Click</b>';
    }

}

// Run the Form we have defined
ExamplesForm::run('ExamplesForm');
