<?php
use QCubed\Control\Panel;
use QCubed\Css\PositionType;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Controls
class ExamplesForm extends FormBase {

	// Local declarations of our Controls
	protected Panel $pnlPanel;

	// Initialize our Controls during the Form Creation process
	protected function formCreate(): void
    {
		// Define the Panel
		$this->pnlPanel = new Panel($this);
		$this->pnlPanel->Text = 'You can click on me to drag me around.';

		// Make the Panel's Positioning Absolute and specify a starting location
		$this->pnlPanel->Position = PositionType::ABSOLUTE;
		$this->pnlPanel->Top = 30;
		$this->pnlPanel->Left = 70;

        $this->pnlPanel->Cursor = 'move';
        $this->pnlPanel->BackColor = '#f6f6f6';
        $this->pnlPanel->Width = 130;
        $this->pnlPanel->Height = 50;
        $this->pnlPanel->Padding = 10;
        $this->pnlPanel->BorderWidth = 1;

		// Finally, let's make this moveable.  We do this by using the methods
		// which specify it as a move handle, and we assign itself as the target
		// control which it will move.
		$this->pnlPanel->Moveable = true;
	}
}

// Run the Form we have defined
ExamplesForm::run('ExamplesForm');
