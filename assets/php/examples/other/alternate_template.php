<?php
use QCubed\Action\Ajax;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Qcontrols
class ExampleForm extends FormBase {

	// Local declarations of our Qcontrols
	protected $lblMessage;
	protected $btnButton;

	// Initialize our Controls during the Form Creation process
	protected function formCreate() {
		// Define the Label
		$this->lblMessage = new \QCubed\Control\Label($this);
		$this->lblMessage->Text = 'Click the button to change my message.';

		// Define the Button
		$this->btnButton = new Button($this);
		$this->btnButton->Text = 'Click Me!';

		// Add a Click event handler to the button -- the action to run is an AjaxAction.
		// The AjaxAction names a PHP method (which will be run asynchronously) called "btnButton_Click"
		$this->btnButton->addAction(new Click(), new Ajax('btnButton_Click'));
	}

	// The "btnButton_Click" Event handler
	protected function btnButton_Click($strFormId, $strControlId, $strParameter) {
		$this->lblMessage->Text = 'Hello, world!';
	}
}

// Run the Form we have defined
// We will explicitly specify an alternate filepath for the HTML template file.  Note
// that this filepath is relative to the path of this PHP script.
ExampleForm::run('ExampleForm', 'some_template_file.tpl.php');
