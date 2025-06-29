<?php
use QCubed\Action\Server;
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
    protected ListBox$lstOperation;
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
        $this->btnCalculate->addAction(new Click(), new Server('btnCalculate_Click'));

        // With btnCalculate being responsible for the action, we set this \QCubed\Project\Jqui\Button's CausesValidation to true
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
        // Add a Custom Form Validation rule here
        // If we are Dividing and if the divisor is 0, then this is not valid
        if (($this->lstOperation->SelectedValue == 'divide') &&
            ($this->txtValue2->Text == 0)
        ) {
            // Warnings and Errors are rendered the same way by RenderWithError()
            $this->txtValue2->Warning = 'Cannot Divide by Zero';

            // We need to make sure the \QCubed\Project\Control\FormBase knows that this form is not valid
            // We do this by returning false
            return false;
        }

        // If we're here, then the custom Form validation rule validated properly,
        // Therefore, returns true
        return true;
    }

    // Perform the necessary operations on the operands and output the value to the lblResult
    protected function btnCalculate_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        $value1 = (float) $this->txtValue1->Text;
        $value2 = (float) $this->txtValue2->Text;

        $mixResult = match ($this->lstOperation->SelectedValue) {
            'add' => $value1 + $value2,
            'subtract' => $value1 - $value2,
            'multiply' => $value1 * $value2,
            'divide' => $value2 != 0 ? $value1 / $value2 : 'Division by zero is not allowed',
            default => throw new Exception('Invalid Action'),
        };

        if (isset($mixResult)) {
            $this->lblResult->Text = '<b>Your Result:</b> ' . $mixResult;
        }
    }
}

// And now run our defined form
CalculatorForm::run('CalculatorForm');
