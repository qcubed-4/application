<?php
// The Logic here is a bit cheesy... we cheat a little because we don't take into
// account overflow or divide-by-zero errors.  Instead, we cop out by just truncating
// values or setting them to zero.
//
// Obviously, not completely accurate -- but this is really just an example dialog box, and hopefully
// this example will give you enough to understand how \QCubed\Project\Jqui\Dialog works overall. =)

use QCubed\Action\AjaxControl;
use QCubed\Control\ControlBase;
use QCubed\Control\FormBase;
use QCubed\Control\Panel;
use QCubed\Control\Proxy;
use QCubed\Event\Click;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Project\Jqui\Button;
use QCubed\Project\Jqui\Dialog;
use QCubed\Type;

class CalculatorWidget extends Dialog
{
    // PUBLIC Child Controls
    public Panel $pnlValueDisplay;
    public Proxy $pxyNumberControl;
    public Proxy $pxyOperationControl;

    public Button $btnEqual;
    public Button $btnPoint;
    public Button $btnClear;

    public Button $btnUpdate;
    public Button $btnCancel;

    protected ?int $intWidth = 240;

    // Object Variables
    protected mixed $strCloseCallback;
    protected mixed $fltValue = 0;

    // Default Overrides
    protected ?bool $blnMatteClickable = false;
    protected string $strTemplate = 'CalculatorWidget.tpl.php';
    protected string $strCssClass = 'calculator_widget';

    protected float $fltInternalValue = 0;
    protected mixed $strCurrentOperation = null;
    protected bool $blnNextClears = true;

    /**
     * Constructor method to initialize the object, define child controls, and set up actions.
     *
     * @param FormBase|ControlBase $objParentObject The parent object with which this control is associated.
     * @param string|null $strControlId An optional control ID for the current control.
     * @throws Caller
     */
    public function __construct(FormBase|ControlBase $objParentObject, ?string $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->DialogClass = $this->strCssClass;

        // Define local child controls
        $this->pnlValueDisplay = new Panel($this);
        $this->pnlValueDisplay->Text = '0';
        $this->pnlValueDisplay->Height = 33;
        $this->pnlValueDisplay->CssClass = 'calculator_display';

        // Define the Proxy
        $this->pxyNumberControl = new Proxy($this);
        $this->pxyNumberControl->AddAction(new Click(), new AjaxControl($this, 'pxyNumber_Click'));

        $this->pxyOperationControl = new Proxy($this);
        $this->pxyOperationControl->AddAction(new Click(), new AjaxControl($this, 'pxyOperation_Click'));

        $this->btnEqual = new Button($this);
        $this->btnEqual->Text = '=';
        $this->btnEqual->AddAction(new Click(), new AjaxControl($this, 'btnEqual_Click'));

        $this->btnPoint = new Button($this);
        $this->btnPoint->Text = '.';
        $this->btnPoint->AddAction(new Click(), new AjaxControl($this, 'btnPoint_Click'));

        $this->btnClear = new Button($this);
        $this->btnClear->Text = 'C';
        $this->btnClear->AddAction(new Click(), new AjaxControl($this, 'btnClear_Click'));

        $this->btnUpdate = new Button($this);
        $this->btnUpdate->Text = 'Save';
        $this->btnUpdate->AddAction(new Click(), new AjaxControl($this, 'btnUpdate_Click'));

        $this->btnCancel = new Button($this);
        $this->btnCancel->Text = 'Cancel';
        $this->btnCancel->AddAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));
    }

    /**
     * Sets a callback function to be executed when a close event occurs.
     *
     * @param string $callback The callback function to handle the close event.
     * @return void
     */
    public function setCloseCallback(string $callback): void
        {
            $this->strCloseCallback = $callback;
        }

    /**
     * Handles a click event for a numeric proxy control and updates the display panel's text
     * based on the provided parameters and the current state of the display.
     *
     * @param string $strFormId The identifier of the form in which the control resides.
     * @param string $strControlId The identifier of the control that triggered the event.
     * @param string $strParameter The parameter representing the numeric value to be processed.
     *
     * @return void
     */
    public function pxyNumber_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        //Application::displayAlert(print_r(func_get_args(), true));

        if ($this->pnlValueDisplay->Text === '0' && $strParameter == '') {
            return;
        }

        if ($this->blnNextClears) {
            $this->blnNextClears = false;
            $this->pnlValueDisplay->Text = $strParameter;
        } else if ($this->pnlValueDisplay->Text === '0') {
            $this->pnlValueDisplay->Text = $strParameter;
        } else if ($strParameter == '') {
            if (strlen($this->pnlValueDisplay->Text) < 13) {
                $this->pnlValueDisplay->Text .= 0;
            }
        } else if (strlen($this->pnlValueDisplay->Text) < 13)
            $this->pnlValueDisplay->Text .= $strParameter;
    }

    /**
     * Handles the click event for the point (".") button. Updates the display panel
     * to add a decimal point if it does not already contain one. If the next input
     * should clear the display, it resets the display to "0." before appending the decimal point.
     *
     * @return void
     */
    public function btnPoint_Click(): void
    {
        if ($this->blnNextClears) {
            $this->pnlValueDisplay->Text = '0.';
            $this->blnNextClears = false;
        } else {
            if (!str_contains($this->pnlValueDisplay->Text, '.'))
                $this->pnlValueDisplay->Text .= '.';
        }
    }

    /**
     * Handles the click event for the operation proxy control. This method updates the current operation,
     * performs a calculation if applicable, and updates the internal value and display.
     *
     * @param string $strFormId The ID of the form that contains the control.
     * @param string $strControlId The ID of the control that triggered the event.
     * @param string $strParameter The parameter passed from the proxy control, typically indicating the operation.
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function pxyOperation_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        if ($this->strCurrentOperation && !$this->blnNextClears)
            $this->btnEqual_Click();
        $this->strCurrentOperation = $strParameter;
        $this->blnNextClears = true;
        if (str_contains($this->pnlValueDisplay->Text, '.'))
            $this->pnlValueDisplay->Text .= '0';

        $this->fltInternalValue = Type::Cast($this->pnlValueDisplay->Text, Type::FLOAT);
        try {
            $this->fltInternalValue = Type::Cast($this->pnlValueDisplay->Text, Type::INTEGER);
        } catch (InvalidCast $objExc) {}

        $this->pnlValueDisplay->Text = $this->fltInternalValue;
    }

    /**
     * Handles the click event of the equal button, performing the selected arithmetic operation
     * and updating the display with the result. Resets the current operation state afterward.
     *
     * @return void This method does not return a value but updates the internal state and display text.
     * @throws Caller
     * @throws InvalidCast
     */
    public function btnEqual_Click(): void
    {
        $this->blnNextClears = true;

        if (str_contains($this->pnlValueDisplay->Text, '.'))
            $this->pnlValueDisplay->Text .= '0';
        $fltOtherValue = Type::cast($this->pnlValueDisplay->Text, Type::FLOAT);
        try {
            $fltOtherValue = Type::cast($this->pnlValueDisplay->Text, Type::INTEGER);
        } catch (InvalidCast $objExc) {}

        switch ($this->strCurrentOperation) {
            case '+':
                $this->fltInternalValue = $this->fltInternalValue + $fltOtherValue;
                break;
            case '-':
                $this->fltInternalValue = $this->fltInternalValue - $fltOtherValue;
                break;
            case '*':
                $this->fltInternalValue = $this->fltInternalValue * $fltOtherValue;
                break;
            case '/':
                if ($fltOtherValue == 0)
                    $this->fltInternalValue = 0;
                else
                    $this->fltInternalValue = $this->fltInternalValue / $fltOtherValue;
                break;
        }

        $this->strCurrentOperation = null;
        $this->pnlValueDisplay->Text = substr('' . $this->fltInternalValue, 0, 13);
    }

    /**
     * Event handler for the clear button click event. Resets the calculator's internal state and display.
     *
     * @return void
     */
    public function btnClear_Click(): void
    {
        $this->fltValue = 0;
        $this->pnlValueDisplay->Text = 0;

        $this->fltInternalValue = 0;
        $this->blnNextClears = true;
        $this->strCurrentOperation = null;
    }

    /**
     * Event handler for the Cancel button click action.
     * Closes the current dialog or interface when the Cancel button is clicked.
     *
     * @return void
     */
    public function btnCancel_Click(): void
    {
        $this->Close();
    }

    /**
     * Handles the click event for the update button. Retrieves the current value from the display panel,
     * triggers the specified close callback on the associated form, and closes the current dialog.
     *
     * @return void
     */
    public function btnUpdate_Click(): void
    {
        $this->fltValue = $this->pnlValueDisplay->Text;
        call_user_func(array($this->objForm, $this->strCloseCallback));
        $this->Close();
    }

    public function Open(): void
    {
        parent::Open();
        $this->pnlValueDisplay->Text = $this->fltValue ?? '0' ;

        $this->fltInternalValue = 0;
        $this->blnNextClears = true;
        $this->strCurrentOperation = null;
    }

    /**
     * Magic getter method to retrieve the value of a property.
     *
     * @param string $strName The name of the property to retrieve.
     * @return mixed The value of the requested property.
     * @throws Caller If the property does not exist or is inaccessible.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case "Value": return $this->fltValue;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * Magic method to set the value of a property dynamically.
     *
     * @param string $strName The name of the property being set.
     * @param mixed $mixValue The value to assign to the property.
     * @return void
     * @throws Caller Thrown if the property name is invalid or cannot be handled by the parent::__set method.
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        $this->blnModified = true;

        switch ($strName) {
            case "Value":
                // Depending on the format of $mixValue, set $this->fltValue appropriately
                // It will try to cast to Integer if possible, otherwise Float, otherwise just 0
                $this->fltValue = 0;
                try {
                    $this->fltValue = Type::cast($mixValue, Type::FLOAT);
                    break;
                } catch (InvalidCast $objExc) {}
                try {
                    $this->fltValue = Type::cast($mixValue, Type::INTEGER);
                    break;
                } catch (InvalidCast $objExc) {}
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
        }
    }
}