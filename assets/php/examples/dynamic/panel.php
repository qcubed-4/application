<?php
use QCubed\Control\Fieldset;
use QCubed\Control\Panel;
use QCubed\Css\TextAlignType;
use QCubed\Project\Control\Checkbox;
use QCubed\Project\Control\TextBox;

require_once('../qcubed.inc.php');

class ExampleForm extends \QCubed\Project\Control\FormBase
{
    // Declare the panel
    // Notice how we don't declare the textboxes that we will display
    // We do this to demonstrate that the panel can render its own set of dynamically created controls
    // through using AutoRenderChildren
    protected $pnlPanel;

    protected $pnlFieldset;

    // For this example, show how the panel can display this strMessage
    public $strMessage = 'Hello, world!';

    protected function formCreate()
    {
        // Define the Panel
        $this->pnlPanel = new Panel($this);
        $this->pnlPanel->Width = 400;
        $this->pnlPanel->BackColor = '#dddddd';
        $this->pnlPanel->Padding = '10px 0px';
        $this->pnlPanel->TextAlign = TextAlignType::CENTER;

        // Define a Template to make it Pretty
        $this->pnlPanel->Text = 'Text Here Goes First';
        $this->pnlPanel->Template = 'pnl_panel.tpl.php';


        // Let's have the pnlPanel auto render any and all child controls
        $this->pnlPanel->AutoRenderChildren = true;

        // Define a bunch of textboxes, and put it into the panel
        for ($intIndex = 1; $intIndex <= 10; $intIndex++) {
            // The parent must be the panel, because the panel is going to be responsible
            // for rendering it.
            $txtTextbox = new TextBox($this->pnlPanel);
            $txtTextbox->Text = sprintf('Textbox #%s', $intIndex);
            $txtTextbox->Width = 350;
        }

        $this->pnlFieldset = new Fieldset ($this);
        $this->pnlFieldset->Legend = 'Fieldset Example';
        $this->pnlFieldset->AutoRenderChildren = true;
        $this->pnlFieldset->Width = 300;
        $this->pnlFieldset->Padding = '10px 10px';

        // Define a bunch of checkboxes, and put it into the fieldset. Fieldsets can encapsulate any form element.
        for ($intIndex = 1; $intIndex <= 5; $intIndex++) {
            // The parent must be the fields, because the fieldset is going to be responsible
            // for rendering it.
            $chkCheckbox = new Checkbox($this->pnlFieldset);
            $chkCheckbox->Text = sprintf('Checkbox #%s', $intIndex);
            $chkCheckbox->Width = 250;
        }


    }
}

ExampleForm::run('ExampleForm');

