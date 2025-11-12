<?php
use QCubed\Action\AjaxControl;
use QCubed\Action\ServerControl;
use QCubed\Control\ControlBase;
use QCubed\Control\FormBase;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Exception\Caller;
use QCubed\Project\Control\Button;
use QCubed\Type;

/**
 * This is a completely custom Control, and it is also a composite control,
 * meaning it utilizes several individual QControls (e.g., a \QCubed\Control\Label and two
 * QButtons) to make one larger control.
 */
class SampleComposite extends ControlBase
{

    // Our SubControls
    protected Label $lblMessage;
    protected Button $btnIncrement;
    protected Button $btnDecrement;
    // Some Member Variables
    protected int $intValue = 0;
    protected bool $blnUseAjax = false;
    protected string $strPadding = '10px';
    // Let's Override the Default Style Settings
    protected string $strWidth = '100px';
    protected string $strFontSize = '36px';
    protected bool $blnFontBold = true;
    protected string $strBackColor = '#cccccc';
    // Because we're generating a Block Element (at its core, the control is a
    // DIV with a bunch of stuff inside), let's set this to true.
    // (This is required for X/HTML Strict Standards Compliance)
    protected bool $blnIsBlockElement = true;

    // We want to override the constructor in order to set up the subcontrols
    public function __construct(FormBase|ControlBase $objParentObject, ?string $strControlId = null)
    {
        // First, call the parent to do most of the basic setup
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }

        // Next, we'll create our local subcontrols.  Make sure to set "this" as these
        // subcontrols' parent.
        $this->lblMessage = new Label($this);
        $this->btnIncrement = new Button($this);
        $this->btnDecrement = new Button($this);

        // Let's set up these button controls
        $this->btnIncrement->Text = '>>';
        $this->btnDecrement->Text = '<<';

        // And setup actions on those button controls
        $this->setupButtonActions();
    }

    protected function setupButtonActions(): void
    {
        // In case any actions are set up already, let's remove them
        $this->btnIncrement->removeAllActions(Click::EVENT_NAME);
        $this->btnDecrement->removeAllActions(Click::EVENT_NAME);

        // Notice how, instead of Server or Ajax actions, we use Server-
        // or Ajax-CONTROL actions.  This is because the actual PHP method
        // we want to run is in this CONTROL, instead of on the form.  We must specify
        // which control has the method we want to run, or in this case, $this.
        if ($this->blnUseAjax) {
            $this->btnIncrement->addAction(new Click(),
                new AjaxControl($this, 'btnIncrement_Click'));
            $this->btnDecrement->addAction(new Click(),
                new AjaxControl($this, 'btnDecrement_Click'));
        } else {
            $this->btnIncrement->addAction(new Click(),
                new ServerControl($this, 'btnIncrement_Click'));
            $this->btnDecrement->addAction(new Click(),
                new ServerControl($this, 'btnDecrement_Click'));
        }
    }

    // All functions MUST implement ParsePostData
    // In this case, because the values only get changed by event handlers, no
    // apostatised logic is needed.
    public function parsePostData(): void
    {

    }

    // All functions MUST implement Validate
    // Our specific example here should always basically be valid
    public function validate(): bool
    {
        return true;
    }

    // Now, for the fun part -- we get to define how our sample control gets rendered
    protected function getControlHtml(): string
    {
        // Let's get Style attributes
        $strStyle = $this->getStyleAttributes();
        if ($this->strPadding) {
            $strStyle .= sprintf('padding:%s;', $this->strPadding);
        }
        $strStyle = sprintf('style="%s;text-align:center;"', $strStyle);

        $strAttributes = $this->getAttributes();

        // Let's update the label
        $this->lblMessage->Text = $this->intValue;

        // Let's get the rendered subcontrols -- remember to use FALSE for "blnDisplayOutput"
        $strMessage = $this->lblMessage->render(false);
        $strIncrement = $this->btnIncrement->render(false);
        $strDecrement = $this->btnDecrement->render(false);

        // Let's render it out
        return sprintf('<div id="%s" %s%s><div>%s</div><div>%s%s</div></div>', $this->strControlId, $strStyle,
            $strAttributes, $strMessage, $strDecrement, $strIncrement);
    }

    // Event Handlers -- Because these will be called by the Form (which triggers ALL events), these
    // MUST be declared as PUBLIC.
    public function btnIncrement_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        $this->intValue++;

        // Let's set this as modified so that it will re-render on an ajax refresh
        $this->blnModified = true;
    }

    public function btnDecrement_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        $this->intValue--;

        // Let's set this as modified so that it will re-render on an ajax refresh
        $this->blnModified = true;
    }

    // And our public getter/setters
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'Value':
                return $this->intValue;
            case 'Padding':
                return $this->strPadding;
            case 'UseAjax':
                return $this->blnUseAjax;
            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set(string $strName, mixed $mixValue): void
    {
        // Whenever we set a property, we must set the Modified flag to true
        $this->blnModified = true;

        try {
            switch ($strName) {
                case 'Value':
                    $this->intValue = Type::cast($mixValue, Type::INTEGER);
                    break;
                case 'Padding':
                    $this->strPadding = Type::cast($mixValue, Type::STRING);
                    break;
                case 'UseAjax':
                    $blnToReturn = ($this->blnUseAjax = Type::cast($mixValue, Type::BOOLEAN));

                    // Whenever we change UseAjax, we must be sure to update our two buttons
                    // and their defined actions.
                    $this->setupButtonActions();
                    break;

                //return $blnToReturn;

                default:
                    parent::__set($strName, $mixValue);
            }
        } catch (Caller $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
    }
}
