<?php
use QCubed\Action\Ajax;
use QCubed\Action\Terminate;
use QCubed\Control\Label;
use QCubed\Event\Blur;
use QCubed\Event\Click;
use QCubed\Event\EnterKey;
use QCubed\Event\EscapeKey;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\TextBox;

require_once('../qcubed.inc.php');

class SelectableLabel extends Label
{
    // For Simplicity -- We made this a public member variable
    // In the future, you might want to make it protected, and make public get/set accessors
    public $Selected;

}

class ExampleForm extends FormBase
{

    protected $lblArray;
    protected $txtArray;

    protected function formCreate()
    {
        for ($intIndex = 0; $intIndex < 10; $intIndex++) {
            // Create the Label
            $this->lblArray[$intIndex] = new SelectableLabel($this);
            $this->lblArray[$intIndex]->Text = 'This is a Test for Item #' . ($intIndex + 1);
            $this->lblArray[$intIndex]->CssClass = 'renamer_item';
            $this->lblArray[$intIndex]->ActionParameter = $intIndex;
            $this->lblArray[$intIndex]->addAction(new Click(), new Ajax('lblArray_Click'));

            // Create the Textbox (hidden)
            $this->txtArray[$intIndex] = new TextBox($this);
            $this->txtArray[$intIndex]->Visible = false;
            $this->txtArray[$intIndex]->ActionParameter = $intIndex;

            // Create Actions to Save Textbox on Blur or on "Enter" Key
            $this->txtArray[$intIndex]->addAction(new Blur(), new Ajax('TextItem_Save'));
            $this->txtArray[$intIndex]->addAction(new EnterKey(),
                new Ajax('TextItem_Save'));
            $this->txtArray[$intIndex]->addAction(new EnterKey(), new Terminate());

            // Create Action to CANCEL/Revert Textbox on "Escape" Key
            $this->txtArray[$intIndex]->addAction(new EscapeKey(),
                new Ajax('TextItem_Cancel'));
            $this->txtArray[$intIndex]->addAction(new EscapeKey(), new Terminate());
        }
    }

    protected function lblArray_Click($strFormId, $strControlId, $strParameter)
    {
        // Is the Label being clicked already selected?
        if ($this->lblArray[$strParameter]->Selected) {
            // It's already selected -- go ahead and replace it with the textbox
            $this->lblArray[$strParameter]->Visible = false;
            $this->txtArray[$strParameter]->Visible = true;
            $this->txtArray[$strParameter]->Text = html_entity_decode($this->lblArray[$strParameter]->Text, ENT_COMPAT,
                \QCubed\Project\Application::encodingType());
            \QCubed\Project\Application::executeControlCommand($this->txtArray[$strParameter]->ControlId, 'select');
            \QCubed\Project\Application::executeControlCommand($this->txtArray[$strParameter]->ControlId, 'focus');
        } else {
            // Nope -- not yet selected
            // First, unselect everything else
            for ($intIndex = 0; $intIndex < 10; $intIndex++) {
                if ($this->lblArray[$intIndex]->Selected) {
                    $this->lblArray[$intIndex]->Selected = false;
                    $this->lblArray[$intIndex]->CssClass = 'renamer_item';
                }
            }

            // Now, make this item selected
            $this->lblArray[$strParameter]->Selected = true;
            $this->lblArray[$strParameter]->CssClass = 'renamer_item renamer_item_selected';
        }
    }

    protected function textItem_Save($strFormId, $strControlId, $strParameter)
    {
        $strValue = trim($this->txtArray[$strParameter]->Text);

        if (strlen($strValue)) {
            // Copy the Textbox value back to the Label
            $this->lblArray[$strParameter]->Text = $strValue;
        }

        // Hide the Textbox, get the label cleaned up and ready to go
        $this->lblArray[$strParameter]->Visible = true;
        $this->txtArray[$strParameter]->Visible = false;
        $this->lblArray[$strParameter]->Selected = false;
        $this->lblArray[$strParameter]->CssClass = 'renamer_item';
    }

    protected function textItem_Cancel($strFormId, $strControlId, $strParameter)
    {
        // Hide the Textbox, get the label cleaned up and ready to go
        $this->lblArray[$strParameter]->Visible = true;
        $this->txtArray[$strParameter]->Visible = false;
        $this->lblArray[$strParameter]->Selected = false;
        $this->lblArray[$strParameter]->CssClass = 'renamer_item';
    }

}

ExampleForm::run('ExampleForm');
