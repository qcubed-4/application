<?php
use QCubed\Action\Ajax;
use QCubed\Action\Terminate;
use QCubed\Action\ToggleDisplay;
use QCubed\Control\Label;
use QCubed\Control\Panel;
use QCubed\Control\Proxy;
use QCubed\Css\BorderStyleType;
use QCubed\Event\Click;
use QCubed\Event\MouseOut;
use QCubed\Event\MouseOver;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{
    // Declare the Proxy Control
    // Notice how this control is NEVER RENDERED outright.  Instead, we use
    // RenderAsHref() and RenderAsEvents() on it.
    protected $pxyExample;
    protected $pnlHover;

    // For this example, show how to use custom HTML to trigger events that updates this Message label
    protected $lblMessage;

    /**
     *
     */
    protected function formCreate()
    {
        // Define the Proxy
        $this->pxyExample = new Proxy($this);

        // Define a Message label
        $this->lblMessage = new Label($this);

        // Define a Panel to display/hide whenever we're hovering
        $this->pnlHover = new Panel($this);
        $this->pnlHover->Text = 'Hovering over a button or link...';
        $this->pnlHover->Padding = 10;
        $this->pnlHover->BorderStyle = BorderStyleType::SOLID;
        $this->pnlHover->BorderWidth = 1;
        $this->pnlHover->Width = 200;
        $this->pnlHover->BackColor = '#ffffcc';
        $this->pnlHover->Display = false;

        // Define any applicable actions on the Proxy
        // Note that all events will flow through to any DOM element (in the HTML) that is calling RenderAsEvents.
        $this->pxyExample->addAction(new Click(), new Ajax('pxyExample_Click'));
        $this->pxyExample->addAction(new Click(), new Terminate());
        $this->pxyExample->addAction(new MouseOver(),
            new ToggleDisplay($this->pnlHover, true));
        $this->pxyExample->addAction(new MouseOut(),
            new ToggleDisplay($this->pnlHover, false));
    }

    // Notice how the optional "action parameter" we used in the RenderAsHref() or RenderEvents() call gets passed in as $strParameter here.
    protected function pxyExample_Click($strFormId, $strControlId, $strParameter)
    {
        $this->lblMessage->Text = 'You clicked on: ' . $strParameter;
    }
}

ExampleForm::run('ExampleForm');

