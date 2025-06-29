<?php
use QCubed\Action\Ajax;
use QCubed\Control\IntegerTextBox;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ListBox;

require_once('../qcubed.inc.php');

class CalculatorForm extends FormBase
{

    // Our Calculator needs 2 Textboxes (one for each operand)
    // A listbox of operations to choose from
    // A button to execute the calculation
    // And a label to output the result
    protected IntegerTextBox $txtValue1;
    protected IntegerTextBox $txtValue2;
    protected ListBox $lstOperation;
    protected Button $btnCalculate;
    protected Label $lblResult;

    // Define all the QControl objects for our Calculator
    // Make our textboxes IntegerTextboxes and make them required
    protected function formCreate(): void
    {
        $this->txtValue1 = new IntegerTextBox($this);
        $this->txtValue1->Required = true;

        $this->txtValue2 = new IntegerTextBox($this);
        $this->txtValue2->Required = true;

        $this->lstOperation = new ListBox($this);
        $this->lstOperation->addItem('+', 'add');
        $this->lstOperation->addItem('-', 'subtract');
        $this->lstOperation->addItem('*', 'multiply');
        $this->lstOperation->addItem('/', 'divide');

        $this->btnCalculate = new Button($this);
        $this->btnCalculate->Text = 'Calculate';

        // This is the **ONLY LINE** that has been changed: from Server to Ajax
        $this->btnCalculate->addAction(new Click(), new Ajax('btnCalculate_Click'));

        // With btnCalculate being responsible for the action, we set this Button's CausesValidation to true
        // so that validation will occur on the form when click the button.
        // But if you set it to false, you'll see that integers and null entries would instead always be allowed.
        $this->btnCalculate->CausesValidation = true;

        $this->lblResult = new Label($this);
        $this->lblResult->HtmlEntities = false;
    }

    protected function formLoad(): void
    {
        // Let's always clear the Result label
        $this->lblResult->Text = '';
    }

    protected function formValidate(): bool
    {
        // If we are Dividing and if the divisor is 0, then this is not valid
        if (($this->lstOperation->SelectedValue == 'divide') &&
            ($this->txtValue2->Text == 0)
        ) {
            $this->txtValue2->Warning = 'Cannot Divide by Zero';
            return false;
        }

        // If we're here, then the custom Form validation rule validated properly
        return true;
    }

    // Perform the necessary operations on the operands and output the value to the lblResult
    protected function btnCalculate_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        $mixResult = match ($this->lstOperation->SelectedValue) {
            'add' => $this->txtValue1->Text + $this->txtValue2->Text,
            'subtract' => $this->txtValue1->Text - $this->txtValue2->Text,
            'multiply' => $this->txtValue1->Text * $this->txtValue2->Text,
            'divide' => $this->txtValue1->Text / $this->txtValue2->Text,
            default => throw new Exception('Invalid Action'),
        };

        if (isset($mixResult)) {
            $this->lblResult->Text = '<strong>Your Result:</strong> ' . $mixResult;
        }
    }
}

// And now run our defined form
CalculatorForm::run('CalculatorForm');
