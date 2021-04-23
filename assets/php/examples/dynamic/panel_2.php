<?php
use QCubed\Action\Ajax;
use QCubed\Control\Panel;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\TextBox;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{
    // Declare the panels and the buttons
    // Notice how we don't declare the textboxes that we will be moving back and forth.
    // We do this to demonstrate that the panel can manage its own set of dynamically controls
    // through using GetChildControls() and AutoRenderChildren

    /** @var  Panel */
    protected $pnlLeft;
    /** @var  Panel */
    protected $pnlRight;
    /** @var  Button */
    protected $btnMoveLeft;
    /** @var  Button */
    protected $btnMoveRight;
    /** @var  Button */
    protected $btnDeleteLeft;

    protected function formCreate()
    {
        // Define the Panels
        $this->pnlLeft = new Panel($this);
        $this->pnlLeft->CssClass = 'textbox_panel';
        $this->pnlLeft->Width = 250;

        $this->pnlRight = new Panel($this);
        $this->pnlRight->CssClass = 'textbox_panel';
        $this->pnlRight->Width = 250;

        // Let's have the panels auto render any and all child controls
        $this->pnlLeft->AutoRenderChildren = true;
        $this->pnlRight->AutoRenderChildren = true;

        // Define the Buttons
        $this->btnMoveLeft = new Button($this);
        $this->btnMoveLeft->Text = '<<';
        $this->btnMoveLeft->addAction(new Click(), new Ajax('MoveTextbox'));
        $this->btnMoveLeft->ActionParameter = 'left';

        $this->btnMoveRight = new Button($this);
        $this->btnMoveRight->Text = '>>';
        $this->btnMoveRight->addAction(new Click(), new Ajax('MoveTextbox'));
        $this->btnMoveRight->ActionParameter = 'right';

        $this->btnDeleteLeft = new Button($this);
        $this->btnDeleteLeft->Text = 'Delete One From Left';
        $this->btnDeleteLeft->addAction(new Click(), new Ajax('btnDeleteLeft_Click'));

        // Define a bunch of textboxes, and put it into the left Panel
        for ($intIndex = 1; $intIndex <= 10; $intIndex++) {
            // The parent must be the panel, because the panel is going to be responsible
            // for rendering it.
            $txtTextbox = new TextBox($this->pnlLeft);
            $txtTextbox->Text = sprintf('Textbox #%s', $intIndex);
            $txtTextbox->Width = 250;
        }
    }

    // Handle the action for the Button being clicked.  We want to basically
    // move one of the textboxes from one panel to the other
    protected function moveTextbox($strFormId, $strControlId, $strParameter)
    {
        if ($strParameter == 'left') {
            $pnlSource = $this->pnlRight;
            $pnlDestination = $this->pnlLeft;
        } else {
            $pnlSource = $this->pnlLeft;
            $pnlDestination = $this->pnlRight;
        }

        // Get the Source's Child Controls
        $objChildControls = $pnlSource->getChildControls();

        // Only make the move if source has at least one control to move
        if (count($objChildControls) > 0) {
            // Set the parent of the last control in this array to be the destination panel,
            // essentially moving it from one panel to the other
            $objChildControls[count($objChildControls) - 1]->setParentControl($pnlDestination);
        }
    }

    // Handle the action to delete a control from pnlLeft
    protected function btnDeleteLeft_Click($strFormId, $strControlId, $strParameter)
    {
        // Get the left panel's Child Controls
        $objChildControls = $this->pnlLeft->getChildControls();

        // Only remove if pnlLeft has at least one control to remove
        if (count($objChildControls) > 0) {
            // Set the parent of the last control in this array to be NULL,
            // essentially removing it from the panel (and the form altogether)
            $objChildControls[count($objChildControls) - 1]->setParentControl(null);
        }
    }
}

ExampleForm::run('ExampleForm');
