<?php
use QCubed\Action\Server;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Qcontrols
class ExamplesForm extends FormBase
{

    // Local declarations of our Qcontrols
    protected $lblMessage;
    protected $btnButton;

    // Initialize our Controls during the Form Creation process
    protected function formCreate()
    {
        // Define the Label
        // When we define any QControl, we must specify the control's ParentObject.
        // Note: a QControl's ParentObject is the object that is responsible for rendering
        // the control.  In most cases, the ParentObject is just the form itself, e.g. "$this",
        // because the form is what ends up calling control->render() in its HTML template include file.
        // You can see this call being made in the intro.tpl.php file.  (As you get into more complex forms,
        // you may have QControl objects who's parents are other QControl objects.)
        $this->lblMessage = new Label($this);
        $this->lblMessage->Text = 'Click the button to change my message.';

        // Define the Button
        $this->btnButton = new Button($this);
        $this->btnButton->Text = 'Click Me!';

        // Add a Click event handler to the button -- the action to run is a Server Action (e.g. PHP method)
        // called "btnButton_Click"
        $this->btnButton->addAction(new Click(), new Server('btnButton_Click'));
    }

    // The "btnButton_Click" Event handler
    protected function btnButton_Click($strFormId, $strControlId, $strParameter)
    {
        $this->lblMessage->Text = 'Hello, world!';
    }
}

// Run the Form we have defined
// The \QCubed\Project\Control\FormBase engine will look to intro.tpl.php to use as its HTML template include file
ExamplesForm::run('ExamplesForm');
