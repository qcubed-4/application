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
    protected $txtValue1;
    protected $txtValue2;
    protected $lstOperation;
    protected $btnCalculate;
    protected $lblResult;

    // Define all the QContrtol objects for our Calculator
    // Add Names to the Controls so that our RenderWithName method can display it
    // Randomly add properties both here and in the HTML file to show how to change
    // the design/appearance of these controls
    protected function formCreate()
    {
        $this->txtValue1 = new IntegerTextBox($this);
        $this->txtValue1->Required = true;
        $this->txtValue1->Name = 'Value 1';
        $this->txtValue1->BackColor = '#ffeeee';

        $this->txtValue2 = new IntegerTextBox($this);
        $this->txtValue2->Required = true;
        $this->txtValue2->Name = 'Value 2';
        $this->txtValue2->ForeColor = '#0000cc';

        $this->lstOperation = new ListBox($this);
        $this->lstOperation->Name = 'Operation';
        $this->lstOperation->addItem('+', 'add');
        $this->lstOperation->addItem('-', 'subtract');
        $this->lstOperation->addItem('*', 'multiply');
        $this->lstOperation->addItem('/', 'divide');

        $this->btnCalculate = new Button($this);
        $this->btnCalculate->Text = 'Calculate';
        $this->btnCalculate->addAction(new Click(), new Server('btnCalculate_Click'));
        $this->btnCalculate->CausesValidation = true;

        $this->lblResult = new Label($this);
        $this->lblResult->HtmlEntities = false;
    }

    protected function formLoad()
    {
        // Let's always clear the Result label
        $this->lblResult->Text = '';
    }

    protected function formValidate()
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

    // Perform the necessary operations on the operands, and output the value to the lblResult
    protected function btnCalculate_Click($strFormId, $strControlId, $strParameter)
    {
        switch ($this->lstOperation->SelectedValue) {
            case 'add':
                $mixResult = $this->txtValue1->Text + $this->txtValue2->Text;
                break;
            case 'subtract':
                $mixResult = $this->txtValue1->Text - $this->txtValue2->Text;
                break;
            case 'multiply':
                $mixResult = $this->txtValue1->Text * $this->txtValue2->Text;
                break;
            case 'divide':
                $mixResult = $this->txtValue1->Text / $this->txtValue2->Text;
                break;
            default:
                throw new Exception('Invalid Action');
        }

        if (isset($mixResult)) {
            $this->lblResult->Text = '<b>Your Result:</b> ' . $mixResult;
        }
    }
}

// And now run our defined form
CalculatorForm::run('CalculatorForm');
