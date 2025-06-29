<?php
use QCubed\Action\Ajax;
use QCubed\Control\Calendar;
use QCubed\Control\DateTimePicker;
use QCubed\Control\DateTimeTextBox;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{
    protected DateTimeTextBox $dtxDateTimeTextBox;
    protected Button $btnDateTimeTextBox;

    protected Calendar $calQJQCalendar;
    protected Button $btnQJQCalendar;

    protected DateTimePicker $dtpDatePicker;
    protected Button $btnDatePicker;

    protected DateTimePicker $dtpDateTimePicker;
    protected Button $btnDateTimePicker;

    protected Label $lblResult;

    protected function formCreate(): void
    {

        $this->calQJQCalendar = new Calendar($this);

        $this->dtxDateTimeTextBox = new DateTimeTextBox($this);

        // \QCubed\Control\DateTimePicker can have different "Types"
        $this->dtpDatePicker = new DateTimePicker($this);
        $this->dtpDatePicker->DateTimePickerType = DateTimePicker::SHOW_DATE;

        $this->dtpDateTimePicker = new DateTimePicker($this);
        $this->dtpDateTimePicker->DateTimePickerType = DateTimePicker::SHOW_DATE_TIME;

        // To View the "Results"
        $this->lblResult = new Label($this);
        $this->lblResult->Text = 'Results...';
        $this->lblResult->HtmlEntities = false;

        // Various Buttons
        $this->btnQJQCalendar = new Button($this);
        $this->btnQJQCalendar->Text = 'Update';
        $this->btnQJQCalendar->addAction(new Click(), new Ajax('btnUpdate_Click'));
        $this->btnQJQCalendar->ActionParameter = $this->calQJQCalendar->ControlId;

        $this->btnDateTimeTextBox = new Button($this);
        $this->btnDateTimeTextBox->Text = 'Update';
        $this->btnDateTimeTextBox->addAction(new Click(), new Ajax('btnUpdate_Click'));
        $this->btnDateTimeTextBox->ActionParameter = $this->dtxDateTimeTextBox->ControlId;

        $this->btnDatePicker = new Button($this);
        $this->btnDatePicker->Text = 'Update';
        $this->btnDatePicker->addAction(new Click(), new Ajax('btnUpdate_Click'));
        $this->btnDatePicker->ActionParameter = $this->dtpDatePicker->ControlId;

        $this->btnDateTimePicker = new Button($this);
        $this->btnDateTimePicker->Text = 'Update';
        $this->btnDateTimePicker->addAction(new Click(), new Ajax('btnUpdate_Click'));
        $this->btnDateTimePicker->ActionParameter = $this->dtpDateTimePicker->ControlId;
    }

    protected function btnUpdate_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        $objControlToLookup = $this->getControl($strParameter);
        $dttDateTime = $objControlToLookup->DateTime;

        // If a DateTime value is NOT selected or is INVALID, then this will be NULL
        if ($dttDateTime) {
            $this->lblResult->Text = 'QDateTime object:<br/>';
            if (!$dttDateTime->isDateNull()) {
                $this->lblResult->Text .= 'Date: <strong>' . $dttDateTime->qFormat('MMM D, YYYY') . '</strong><br/>';
            } else {
                $this->lblResult->Text .= 'Date: <strong>Null</strong><br/>';
            }
            if (!$dttDateTime->isTimeNull()) {
                $this->lblResult->Text .= 'Time: <strong>' . $dttDateTime->qFormat('h:mm:ss z') . '</strong>';
            } else {
                $this->lblResult->Text .= 'Time: <strong>Null</strong>';
            }
        } else {
            $this->lblResult->Text = 'QDateTime object: <strong>Null</strong>';
        }
    }
}

// And now run our defined form
ExampleForm::run('ExampleForm');
