<?php
use QCubed\Control\Panel;
use QCubed\Css\PositionType;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Qcontrols
class ExamplesForm extends FormBase {

	// Local declarations of our Qcontrols
	protected $pnlPanel;

	// Initialize our Controls during the Form Creation process
	protected function formCreate() {
		// Define the Panel
		$this->pnlPanel = new Panel($this);
		$this->pnlPanel->Text = 'You can click on me to drag me around.';

		// Make the Panel's Positioning Absolute, and specify a starting location
		$this->pnlPanel->Position = PositionType::ABSOLUTE;
		$this->pnlPanel->Top = 30;
		$this->pnlPanel->Left = 70;

		// Finally, let's make this moveable.  We do this by using the methods
		// which specify it as a move handle, and we assign itself as the target
		// control which it will move.
		$this->pnlPanel->Moveable = true;
	}
}

// Run the Form we have defined
ExamplesForm::run('ExamplesForm');
