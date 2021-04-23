<?php

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Qcontrols
class ExamplesForm extends \QCubed\Project\Control\FormBase {

	// Local declarations of our QPanels to Resize
	protected $pnlLeftTop;
	protected $txtTextbox;

	// Initialize our Controls during the Form Creation process
	protected function formCreate() {
		// Define the main panel that will resize.
		$this->pnlLeftTop = new \QCubed\Control\Panel($this);
		$this->pnlLeftTop->Width = 400;
		$this->pnlLeftTop->Height = 200;

		$this->pnlLeftTop->Text = '<p>The QCubed Development Framework is an open-source PHP 5 framework that focuses ' .
				'on freeing developers from unnecessary tedious, mundane coding.</p><p>The result is that developers ' .
				'can do what they do best: focus on implementing functionality and usability, improving performance and ' .
				'ensuring security.</p>';

		// Set the panel to resizable!
		$this->pnlLeftTop->Resizable = true;
		$this->pnlLeftTop->ResizeObj->Animate = true;
		$this->pnlLeftTop->ResizeObj->Helper = 'ui-resizable-helper';

		$this->txtTextbox = new \QCubed\Project\Control\TextBox($this);
		$this->txtTextbox->TextMode = \QCubed\Control\TextBoxBase::MULTI_LINE;
		$this->txtTextbox->Width = 400;
		$this->txtTextbox->Height = 200;
		$this->txtTextbox->Resizable = true;
	}

}

// Run the Form we have defined
ExamplesForm::Run('ExamplesForm');
?>