<?php

use QCubed\Action\Server;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Jqui\Button;

require_once('../qcubed.inc.php');
require('SampleComposite.php');

// Define the \QCubed\Project\Control\FormBase with all our Qcontrols
class ExamplesForm extends FormBase {

	// Local declarations of our Qcontrols
	protected Label $lblMessage;
	protected Button $btnButton;
	// Lets declare our Custom Composite Controls
	protected SampleComposite $objCounter1;
	protected SampleComposite $objCounter2;
	protected SampleComposite $objCounter3;

	// Initialize our Controls during the Form Creation process
	protected function formCreate(): void
    {
		// Define the Label
		$this->lblMessage = new Label($this);
		$this->lblMessage->Text = '';
		$this->lblMessage->HtmlEntities = false;

		// Define the Button
		$this->btnButton = new Button($this);
		$this->btnButton->Text = 'Add the Values';

		// Define our Custom Composite Controls
		$this->objCounter1 = new SampleComposite($this);
		$this->objCounter2 = new SampleComposite($this);
		$this->objCounter3 = new SampleComposite($this);

		// Lets add some flare for the second one
		$this->objCounter2->ForeColor = '#780000';
		$this->objCounter2->BackColor = '#f6f6f6';

		// Lets preset a value for Counter3
		$this->objCounter3->Value = 28;

		// And finally, why not -- lets make 'em use AJAX for their internal events
		$this->objCounter1->UseAjax = true;
		$this->objCounter2->UseAjax = true;
		$this->objCounter3->UseAjax = true;

		// Add a Click event handler to the main "Add the Values" button
		// And, just for variety, lets keep it as a server action
		$this->btnButton->AddAction(new Click(), new Server('btnButton_Click'));
	}

	// The "btnButton_Click" Event handler
	protected function btnButton_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
		$intTotal = $this->objCounter1->Value + $this->objCounter2->Value + $this->objCounter3->Value;

		$this->lblMessage->Text = '<strong>The Total</strong>: ' . $intTotal;
	}
}

// Run the Form we have defined
// The \QCubed\Project\Control\FormBase engine will look to intro.tpl.php to use as its HTML template include file
ExamplesForm::Run('ExamplesForm');
