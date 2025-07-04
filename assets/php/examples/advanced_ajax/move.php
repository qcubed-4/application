<?php
use QCubed\Control\Panel;
use QCubed\Css\PositionType;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\TextBox;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Controls
class ExamplesForm extends FormBase
{
    // Local declarations of our Controls
    protected Panel $pnlPanel;

    protected Panel $lblHandle;
    protected TextBox $txtTextbox;
    protected Panel $pnlParent;

    // Initialize our Controls during the Form Creation process
    protected function formCreate(): void
    {
        // Define the Panel
        $this->pnlPanel = new Panel($this);
        $this->pnlPanel->Text = 'You can click on me to drag me around.';

        $this->pnlPanel->Position = PositionType::ABSOLUTE;
        $this->pnlPanel->Top = 5;
        $this->pnlPanel->Left = 5;

        $this->pnlPanel->Moveable = true;

        $this->pnlParent = new Panel ($this);
        $this->pnlParent->AutoRenderChildren = true;
        $this->pnlParent->Position = PositionType::ABSOLUTE;
        $this->pnlParent->Top = 100;
        $this->pnlParent->Left = 100;

        $this->lblHandle = new Panel($this->pnlParent);
        $this->lblHandle->Text = 'Please Enter your Name';
        $this->lblHandle->Cursor = 'move';
        $this->lblHandle->BackColor = '#333333';
        $this->lblHandle->ForeColor = '#FFFFFF';
        $this->lblHandle->Width = '250px';
        $this->lblHandle->Padding = '4';

        $this->txtTextbox = new TextBox($this->pnlParent);
        $this->txtTextbox->Width = '250px';

        // Let's assign the panel as a moveable control, handled
        // by the label.
        $this->pnlParent->Moveable = true;
        $this->pnlParent->DragObj->Handle = $this->lblHandle;
    }
}

// Run the Form we have defined
ExamplesForm::run('ExamplesForm');
