<?php
use QCubed\Action\Server;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

// First of all, let's override the way FormBase stores state information.
// We will use the session-based FormState Handler, instead of the standard/default
// formstate handler.  Also, let's encrypt the formstate index by defining
// an encryption key.
//

// NOTE: This preference can be set globally, by updating the FormBase class
// which is located at /project/includes/QCubed/Control/FormBase.php
FormBase::$FormStateHandler = '\QCubed\FormState\SessionHandler';

// Uncommenting this would also encrypt the formstate key. Its not really necessary since we should have control of our session
// variables since those are private to our server, but it does add an additional measure of security for the truly paranoid
//FormBase::$EncryptionKey = '\rSome.Random!Key\0';

// Everything else below should be the exact same as our original Hello, World! example
class ExampleForm extends FormBase {

	// Local declarations of our Qcontrols
	protected $lblMessage;
	protected $btnButton;

	// Initialize our Controls during the Form Creation process
	protected function formCreate() {
		// Define the Label
		$this->lblMessage = new Label($this);
		$this->lblMessage->Text = 'Click the button to change my message.';

		// Define the Button
		$this->btnButton = new Button($this);
		$this->btnButton->Text = 'Click Me!';

		// Add a Click event handler to the button
		$this->btnButton->addAction(new Click(), new Server('btnButton_Click'));
	}

	// The "btnButton_Click" Event handler
	protected function btnButton_Click($strFormId, $strControlId, $strParameter) {
		$this->lblMessage->Text = 'Hello, world!';
	}
}

// Run the Form we have defined
ExampleForm::run('ExampleForm');
