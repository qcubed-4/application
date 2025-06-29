<?php
use QCubed\Action\Ajax;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Controls
class ExampleForm extends FormBase
{

    // Local declarations of our Controls
    protected Label $lblMessage;
    protected Button $btnButton;

    // Initialize our Controls during the Form Creation process
    protected function formCreate(): void
    {
        // Define the Label;
        // Even though we are programmatically setting the ForeColor property
        // to Blue here, it will be overridden to Green in the HTML template.
        $this->lblMessage = new Label($this);
        $this->lblMessage->Text = 'Click the button to change my message.';
        $this->lblMessage->ForeColor = '#0000ff';

        // Define the Button
        $this->btnButton = new Button($this);
        $this->btnButton->Text = 'Click Me!';

        // Add a Click event handler to the button -- the action to run is an AjaxAction.
        // The AjaxAction names a PHP method (which will be run asynchronously) called "btnButton_Click"
        $this->btnButton->addAction(new Click(), new Ajax('btnButton_Click'));
    }

    // The "btnButton_Click" Event handler
    protected function btnButton_Click(): void
    {
        $this->lblMessage->Text = 'Hello, world!';
    }
}

// Run the Form we have defined
ExampleForm::run('ExampleForm');
