<?php
use QCubed\Action\Server;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Qcontrols
class ExamplesForm extends FormBase
{

    // Local declarations of our Qcontrols
    protected $btnButton;
    // The class member variable of the intCounter to show off a \QCubed\Project\Control\FormBase's state
    protected $intCounter = 0;

    // Initialize our Controls during the Form Creation process
    protected function formCreate()
    {
        // Definte the Button
        $this->btnButton = new Button($this);
        $this->btnButton->Text = 'Click Me!';

        // Add a Click event handler to the button -- the action to run is a ServerAction (e.g. PHP method)
        // called "btnButton_Click"
        $this->btnButton->addAction(new Click(), new Server('btnButton_Click'));
    }

    // The "btnButton_Click" Event handler
    protected function btnButton_Click($strFormId, $strControlId, $strParameter)
    {
        // Increment our counter
        $this->intCounter++;
    }

}

// Run the Form we have defined
ExamplesForm::run('ExamplesForm');
