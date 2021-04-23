<?php
use QCubed\Action\Ajax;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

// Define the FormBase with all our Controls
class ExamplesForm extends FormBase
{
    // Local declarations of our Controls
    protected $lblMessage;
    protected $btnButton;

    // Initialize our Controls during the Form Creation process
    protected function formCreate()
    {
        // Define the Label
        $this->lblMessage = new Label($this);
        $this->lblMessage->Text = 'Click the button to change my message.';

        // Definte the Button
        $this->btnButton = new Button($this);
        $this->btnButton->Text = 'Click Me!';

        // Add a Click event handler to the button -- the action to run is an AjaxAction.
        // The AjaxAction names a PHP method (which will be run asynchronously) called "btnButton_Click"
        $this->btnButton->addAction(new Click(), new Ajax('btnButton_Click'));
    }

    // The "btnButton_Click" Event handler
    protected function btnButton_Click($strFormId, $strControlId, $strParameter)
    {
        $this->lblMessage->Text = 'Hello, world!';
    }

}

// Run the Form we have defined
ExamplesForm::run('ExamplesForm');
