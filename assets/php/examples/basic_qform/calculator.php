<?php
use QCubed\Action\Server;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ListBox;
use QCubed\Project\Control\TextBox;

require_once('../qcubed.inc.php');

class CalculatorForm extends FormBase
{

    // Our Calculator needs 2 Textboxes (one for each operand)
    // A listbox of operations to choose from
    // A button to execute the calculation
    // And a label to output the result
    protected TextBox $txtValue1;
    protected TextBox $txtValue2;
    protected ListBox $lstOperation;
    protected Button $btnCalculate;
    protected Label $lblResult;

    // Define all the QControl objects for our Calculator
    protected function formCreate(): void
    {
        $this->txtValue1 = new TextBox($this);

        $this->txtValue2 = new TextBox($this);

        $this->lstOperation = new ListBox($this);
        $this->lstOperation->addItem('+', 'add');
        $this->lstOperation->addItem('-', 'subtract');
        $this->lstOperation->addItem('*', 'multiply');
        $this->lstOperation->addItem('/', 'divide');

        $this->btnCalculate = new Button($this);
        $this->btnCalculate->Text = 'Calculate';
        $this->btnCalculate->addAction(new Click(), new Server('btnCalculate_Click'));

        $this->lblResult = new Label($this);
        $this->lblResult->HtmlEntities = false;
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
