<?php
use QCubed\Action\Ajax;
use QCubed\Action\Terminate;
use QCubed\Action\ToggleEnable;
use QCubed\Event\Change;
use QCubed\Event\Click;
use QCubed\Event\EnterKey;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ListBox;
use QCubed\Project\Control\TextBox;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{
    protected $lstListbox;
    protected $txtItem;
    protected $btnAdd;

    protected $lblSelected;

    protected function formCreate()
    {
        // Define the Controls
        $this->lstListbox = new ListBox($this);
        $this->lstListbox->Name = 'Items to Choose From';
        $this->lstListbox->Rows = 6;

        // When the the user changes the selection on the listbox, we'll call lstListbox_Change
        $this->lstListbox->addAction(new Change(), new Ajax('lstListbox_Change'));
        $this->lstListbox->addItem('Sample Item', 'Sample Item');

        $this->txtItem = new TextBox($this);
        $this->txtItem->Name = 'Item to Add';

        $this->btnAdd = new Button($this);
        $this->btnAdd->Text = 'Add Item';

        $this->lblSelected = new \QCubed\Control\Label($this);
        $this->lblSelected->Name = 'Item Currently Selected';
        $this->lblSelected->Text = '<none>';

        // When we submit, we want to do the following actions:
        // * Immediately disable the button, textbox and listbox
        // * Perform the AddListItem action via AJAX
        $objSubmitListItemActions = array(
            new ToggleEnable($this->btnAdd, false),
            new ToggleEnable($this->txtItem, false),
            new ToggleEnable($this->lstListbox, false),
            new Ajax('addListItem')
        );

        // Let's add this set of actions to the Add Button
        $this->btnAdd->addActionArray(new Click(), $objSubmitListItemActions);

        // Let's add this set of actions to the Textbox, as a EnterKeyEvent
        $this->txtItem->addActionArray(new EnterKey(), $objSubmitListItemActions);

        // Because the enter key will also call form.submit() on some browsers, which we
        // absolutely DON'T want to have happen, let's be sure to terminate any additional
        // actions on EnterKey
        $this->txtItem->addAction(new EnterKey(), new Terminate());
    }

    protected function lstListbox_Change()
    {
        // Whenever the user changes the selected listbox item, let's
        // update the label to reflect the selected item
        $this->lblSelected->Text = $this->lstListbox->SelectedValue;
    }

    protected function addListItem()
    {
        // First off, let's make sure that data was typed in
        if (!strlen(trim($this->txtItem->Text))) {
            $this->txtItem->Warning = 'Nothing was entered';
        } else {
            // Add the new item
            $this->lstListbox->addItem(trim($this->txtItem->Text), trim($this->txtItem->Text));
        }

        // Clear the textbox
        $this->txtItem->Text = '';

        // Let's re-enable all the controls;
        $this->txtItem->Enabled = true;
        $this->lstListbox->Enabled = true;
        $this->btnAdd->Enabled = true;
    }

}

ExampleForm::run('ExampleForm');
