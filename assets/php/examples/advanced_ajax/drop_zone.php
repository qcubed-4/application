<?php
use QCubed\Action\Alert;
use QCubed\Control\Panel;
use QCubed\Css\PositionType;
use QCubed\Event\DragDrop;
use QCubed\Jqui\DraggableBase;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Controls
class ExamplesForm extends FormBase
{
    // Local declarations of our Controls
    protected Panel $pnlPanel;
    protected Panel $pnlDropZone1;
    protected Panel $pnlDropZone2;

    // Initialize our Controls during the Form Creation process
    protected function formCreate(): void
    {
        // Define the Panel
        $this->pnlPanel = new Panel($this);
        $this->pnlPanel->Text = 'You can click on me to drag me around.';

        $this->pnlPanel->Cursor = 'move';
        $this->pnlPanel->BackColor = '#f6f6f6';
        $this->pnlPanel->Width = 130;
        $this->pnlPanel->Height = 50;
        $this->pnlPanel->Padding = 10;
        $this->pnlPanel->BorderWidth = 1;
        $this->pnlPanel->CssClass = 'ui-corner-all';

        // Make the Panel's Positioning Absolute and specify a starting location
        $this->pnlPanel->Position = PositionType::ABSOLUTE;
        $this->pnlPanel->Top = 40;
        $this->pnlPanel->Left = 20;

        // Make the Panel Moveable, which also creates a DragObj on the panel
        $this->pnlPanel->Moveable = true;

        // Create some larger panels to use as Drop Zones
        $this->pnlDropZone1 = new Panel($this);
        $this->pnlDropZone1->Position = PositionType::ABSOLUTE;
        $this->pnlDropZone1->Top = 10;
        $this->pnlDropZone1->Left = 10;
        $this->pnlDropZone1->Text = 'Drop Zone 1';

        $this->pnlDropZone1->BackColor = '#ffeeee';
        $this->pnlDropZone1->Width = 250;
        $this->pnlDropZone1->Height = 150;
        $this->pnlDropZone1->Padding = 10;
        $this->pnlDropZone1->BorderWidth = 1;
        $this->pnlDropZone1->CssClass = 'ui-corner-all';

        $this->pnlDropZone2 = new Panel($this);
        $this->pnlDropZone2->Position = PositionType::ABSOLUTE;
        $this->pnlDropZone2->Top = 200;
        $this->pnlDropZone2->Left = 10;
        $this->pnlDropZone2->Text = 'Drop Zone 2';

        $this->pnlDropZone2->BackColor = '#dedede';
        $this->pnlDropZone2->Width = 250;
        $this->pnlDropZone2->Height = 150;
        $this->pnlDropZone2->Padding = 1;
        $this->pnlDropZone2->BorderWidth = 1;
        $this->pnlDropZone2->CssClass = 'ui-corner-all';

        $this->pnlDropZone1->Droppable = true;
        $this->pnlDropZone2->Droppable = true;

        // tell a drag panel to go back to the original location when not dropped correctly
        $this->pnlPanel->DragObj->Revert = DraggableBase::REVERT_INVALID;

        $this->pnlDropZone1->addAction(new DragDrop(), new Alert("dropped on zone 1"));
        $this->pnlDropZone2->addAction(new DragDrop(), new Alert("dropped on zone 2"));
    }
}

// Run the Form we have defined
ExamplesForm::run('ExamplesForm');
