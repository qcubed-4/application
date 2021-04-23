<?php
use QCubed\Action\CssAction;
use QCubed\Action\CssClass;
use QCubed\Action\FocusControl;
use QCubed\Action\SelectControl;
use QCubed\Action\ToggleEnable;
use QCubed\Control\Panel;
use QCubed\Event\Click;
use QCubed\Event\MouseOut;
use QCubed\Event\MouseOver;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\TextBox;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{
    protected $btnFocus;
    protected $btnSelect;
    protected $txtFocus;

    protected $btnToggleDisplay;
    protected $txtDisplay;

    protected $btnToggleEnable;
    protected $txtEnable;

    protected $pnlHover;

    protected $btnCssAction;

    protected function formCreate()
    {
        // Define the Textboxes
        $this->txtFocus = new TextBox($this);
        $this->txtFocus->Text = 'Example Text Here';
        $this->txtDisplay = new TextBox($this);
        $this->txtDisplay->Text = 'Example Text Here';
        $this->txtEnable = new TextBox($this);
        $this->txtEnable->Text = 'Example Text Here';

        // \QCubed\Action\FocusControl example
        $this->btnFocus = new Button($this);
        $this->btnFocus->Text = 'Set Focus';
        $this->btnFocus->addAction(new Click(), new FocusControl($this->txtFocus));

        // \QCubed\Action\SelectControl example
        $this->btnSelect = new Button($this);
        $this->btnSelect->Text = 'Select All in Textbox';
        $this->btnSelect->addAction(new Click(), new SelectControl($this->txtFocus));

        // \QCubed\Action\ToggleDisplay example
        $this->btnToggleDisplay = new Button($this);
        $this->btnToggleDisplay->Text = 'Toggle the Display (show/hide)';
        $this->btnToggleDisplay->addAction(new Click(),
            new \QCubed\Action\ToggleDisplay($this->txtDisplay));

        // \QCubed\Action\ToggleEnable example
        $this->btnToggleEnable = new Button($this);
        $this->btnToggleEnable->Text = 'Toggle the Enable (enabled/disabled)';
        $this->btnToggleEnable->addAction(new Click(), new ToggleEnable($this->txtEnable));

        // \QCubed\Action\CssClass example
        $this->pnlHover = new Panel($this);
        $this->pnlHover->HtmlEntities = false;
        $this->pnlHover->Text = 'Change the CSS class of a control using <strong>CssClass</strong>:<br /><br />(Uses MouseOver and MouseOut to Temporarily Override the Panel\'s CSS Style)';

        // Set a Default Style
        $this->pnlHover->CssClass = 'panelHover';

        // Add QMouseOver and QMouseOut actions to set and then reset temporary style overrides
        // Setting the TemporaryCssClass to "null" will "reset" the style back to the default
        $this->pnlHover->addAction(new MouseOver(), new CssClass('panelHighlight', true));
        $this->pnlHover->addAction(new MouseOut(), new CssClass());

        $this->btnCssAction = new Button($this);
        $this->btnCssAction->Text = "click me to change my background color!";
        $this->btnCssAction->addAction(new Click(), new CssAction("background", "green"));
    }
}

ExampleForm::run('ExampleForm');

