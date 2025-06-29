<?php

use QCubed\Control\Panel;
use QCubed\Control\TextBoxBase;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\TextBox;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Controls
class ExamplesForm extends FormBase {

	// Local declarations of our QPanels to Resize
	protected Panel $pnlLeftTop;
	protected TextBox $txtTextbox;

	// Initialize our Controls during the Form Creation process
	protected function formCreate(): void
    {
		// Define the main panel that will resize.
		$this->pnlLeftTop = new Panel($this);
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
        $this->pnlLeftTop->BackColor = '#f6f6f6';
        $this->pnlLeftTop->BorderColor = '#dedede';
        $this->pnlLeftTop->BorderWidth= '1px 1px 1px 1px';

		$this->txtTextbox = new TextBox($this);
		$this->txtTextbox->TextMode = TextBoxBase::MULTI_LINE;
		$this->txtTextbox->Width = 400;
		$this->txtTextbox->Height = 200;
		$this->txtTextbox->Resizable = true;
	}

}

// Run the Form we have defined
ExamplesForm::Run('ExamplesForm');
