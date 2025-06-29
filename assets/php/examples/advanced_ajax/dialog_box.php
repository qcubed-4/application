<?php
use QCubed\Action\ActionParams;
use QCubed\Action\Ajax;
use QCubed\Action\ShowDialog;
use QCubed\Control\FloatTextBox;
use QCubed\Control\Panel;
use QCubed\Event\Click;
use QCubed\Event\DialogButton;
use QCubed\Exception\Caller;
use QCubed\Jqui\DialogBase;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\Dialog;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\TextBox;

require_once('../qcubed.inc.php');
require('CalculatorWidget.class.php');

// Define the \QCubed\Project\Control\FormBase with all our Controls
class ExamplesForm extends FormBase
{
    // Local declarations of our Controls
    protected Dialog $dlgSimpleMessage;
    protected Button $btnDisplaySimpleMessage;
    protected Button $btnDisplaySimpleMessageJsOnly;

    protected CalculatorWidget $dlgCalculatorWidget;
    protected TextBox $txtValue;
    protected Button $btnCalculator;

    protected Panel $pnlAnswer;
    protected Button $btnDisplayYesNo;

    protected Button $btnValidation;

    protected Dialog $dlgErrorMessage;
    protected Button $btnErrorMessage;
    protected Dialog $dlgInfoMessage;
    protected Button $btnInfoMessage;


    // Initialize our Controls during the Form Creation process
    protected function formCreate(): void
    {
        // Define the Simple Message Dialog Box
        $this->dlgSimpleMessage = new Dialog($this);
        $this->dlgSimpleMessage->Title = "Hello World!";
        $this->dlgSimpleMessage->Text = '<p><em>Hello, world!</em></p><p>This is a standard, no-frills dialog box.</p><p>Notice how the contents of the dialog ' .
            'box can scroll, and notice how everything else in the application is grayed out.</p><p>Because we set <strong>MatteClickable</strong> to <strong>true</strong> ' .
            '(by default), you can click anywhere outside of this dialog box to "close" it.</p><p>Additional text here is just to help show the scrolling ' .
            'capability built-in to the panel/dialog box via the "Overflow" property of the control.</p>';
        $this->dlgSimpleMessage->AutoOpen = false;

        // Make sure this Dialog Box is "hidden"
        // Like any other \QCubed\Control\Panel or QControl, this can be toggled using the "Display" or the "Visible" property
        $this->dlgSimpleMessage->Display = false;

        // The First "Display Simple Message" button will utilize an AJAX call to Show the Dialog Box
        $this->btnDisplaySimpleMessage = new Button($this);
        $this->btnDisplaySimpleMessage->Text = t('Display Simple Message Dialog');
        $this->btnDisplaySimpleMessage->addAction(new Click(), new Ajax('btnDisplaySimpleMessage_Click'));

        // The Second "Display Simple Message" button will utilize Client Side-only JavaScripts to Show the Dialog Box
        // (No postback/post ajax is used)
        $this->btnDisplaySimpleMessageJsOnly = new Button($this);
        $this->btnDisplaySimpleMessageJsOnly->Text = 'Display Simple Message Dialog (ClientSide Only)';
        $this->btnDisplaySimpleMessageJsOnly->addAction(new Click(), new ShowDialog($this->dlgSimpleMessage));

        $this->pnlAnswer = new Panel($this);
        $this->pnlAnswer->Text = 'Hmmm';

        $this->btnDisplayYesNo = new Button($this);
        $this->btnDisplayYesNo->Text = t('Do you love me?');
        $this->btnDisplayYesNo->addAction(new Click(), new Ajax('showYesNoClick'));


        // Define the CalculatorWidget example. Passing in the Method Callback for whenever the Calculator is Closed,
        // This is exampled uses Button instead of the JQuery UI buttons
        $this->dlgCalculatorWidget = new CalculatorWidget($this);
        $this->dlgCalculatorWidget->setCloseCallback('btnCalculator_Close');
        $this->dlgCalculatorWidget->Title = "Calculator Widget";
        $this->dlgCalculatorWidget->AutoOpen = false;
        $this->dlgCalculatorWidget->Resizable = false;
        $this->dlgCalculatorWidget->Modal = false;

        // Set up the Value Textbox and Button for this example
        $this->txtValue = new TextBox($this);

        $this->btnCalculator = new Button($this);
        $this->btnCalculator->Text = 'Show Calculator Widget';
        $this->btnCalculator->addAction(new Click(), new Ajax('btnCalculator_Click'));

        $this->btnValidation = new Button($this);
        $this->btnValidation->Text = 'Show Validation Example';
        $this->btnValidation->addAction(new Click(), new Ajax('dlgValidate_Show'));

        /*** Alert examples  ***/

        $this->btnErrorMessage = new Button($this);
        $this->btnErrorMessage->Text = 'Show Error';
        $this->btnErrorMessage->addAction(new Click(), new Ajax('btnErrorMessage_Click'));

        $this->btnInfoMessage = new Button($this);
        $this->btnInfoMessage->Text = 'Get Info';
        $this->btnInfoMessage->addAction(new Click(), new Ajax('btnGetInfo_Click'));
    }

    /**
     * Handles the button click event to display a simple message dialog.
     * This method triggers the opening of the dialog box using its Open() method.
     *
     * @return void
     */
    protected function btnDisplaySimpleMessage_Click(): void
    {
        // "Show" the Dialog Box using the Open() method
        $this->dlgSimpleMessage->open();
    }

    /**
     * Handles the click event for the calculator button. Sets the value of the calculator widget
     * based on the input text and displays the calculator dialog.
     *
     * @return void
     */
    protected function btnCalculator_Click(): void
    {
        // Set up the Calculator Widget's Value
        $this->dlgCalculatorWidget->Value = $this->txtValue->Text;

        // And Show it
        $this->dlgCalculatorWidget->open();
    }

    /**
     * This is an example of creating a dialog on the fly, rather than as part of the form. The advantage is that
     * the dialog is not part of the formstate, which saves space in the formstate. The disadvantage is that the
     * entire dialog's HTML is sent to the browser whenever it is shown, which increases traffic. If the dialog
     * has to change a lot before being shown, then creating on the fly is the best.
     */
    public function dlgValidate_Show(): void
    {
        // Validate on JQuery UI buttons
        $dlgValidation = new Dialog();  // No parent object here!
        $dlgValidation->addButton('OK', 'ok', true,
            true); // specify that this button causes validation and is the default button
        $dlgValidation->addButton('Cancel', 'cancel');

        // This next button demonstrates a confirmation button that is styled to the left side of the dialog box.
        // This is a QCubed addition to the jquery ui functionality
        $dlgValidation->addButton('Confirm', 'confirm', true, false, 'Are you sure?',
            array('class' => 'ui-button-left'));
        $dlgValidation->Width = 400; // Need extra room for buttons

        $dlgValidation->addAction(new DialogButton(), new Ajax('dlgValidate_Click'));
        $dlgValidation->Title = 'Enter a number';

        // Set up a field to be auto-rendered, so no template is needed
        $dlgValidation->AutoRenderChildren = true;
        $txtFloat = new FloatTextBox($dlgValidation);
        $txtFloat->Placeholder = 'Float only';
        $txtFloat->PreferredRenderMethod = 'RenderWithError'; // Tell the panel to use this method when rendering
    }

    /**
     * Handles the click event for the validation dialog. Closes the dialog upon execution.
     *
     * @param ActionParams $params Parameters related to the action, including the dialog control.
     * @return void
     */
    public function dlgValidate_Click(ActionParams $params): void
    {
        $params->Control->close();
    }


    // Set up the "Callback" function for when the calculator closes
    // This needs to be a public method
    /**
     * Handles the close action of the calculator button. Sets the text value of the input field
     * to the value retrieved from the calculator widget.
     *
     * @return void
     */
    public function btnCalculator_Close(): void
    {
        $this->txtValue->Text = $this->dlgCalculatorWidget->Value;
    }

    /** Alert Examples **/

    /**
     * Note that in the following examples, you do NOT save a copy of the dialog in the form. Alerts are brief
     * messages that are displayed and then taken down immediately and are not part of the form state.
     */

    /**
     * Handles the button click event to display an error message dialog.
     *
     * This method brings up a dialog box with an error message. The dialog has an error styling
     * and optionally includes a title. The dialog can be closed by the user through the close box provided.
     *
     * @return void
     * @throws Caller
     */
    protected function btnErrorMessage_Click(): void
    {
        /**
         * Bring up the dialog. Here we specify a simple dialog with no buttons.
         * With no buttons, a close box will be displayed so the user can close the dialog.
         * With one button, no close box will be displayed, but the single button will close the dialog.
         */

        $dlg = Dialog::alert("Don't do that!");
        $dlg->Title = 'Error'; // Optional title for the alert.
        $dlg->DialogState = DialogBase::STATE_ERROR; // Optional error styling.
    }

    /**
     * Handles the button click event to display a dialog with two options.
     * The dialog includes buttons allowing the user to choose an option, with an action added to handle button clicks.
     *
     * @return void
     * @throws Caller
     */
    protected function btnGetInfo_Click(): void
    {
        /**
         * Bring up the dialog. Here we specify two buttons.
         * With two or more buttons, we must detect a button click and close the dialog if a button is clicked.
         */

        $dlg = Dialog::alert("Which do you want?", ['This', 'That']);
        $dlg->DialogState = DialogBase::STATE_HIGHLIGHT;
        $dlg->Title = 'Info';
        $dlg->addAction(new DialogButton(), new Ajax('infoClick')); // Add the action to detect a button click.
    }

    /**
     * Handles the click event triggered by the dialog's control.
     *
     * @param ActionParams $params Contains information about the current action, including the triggering control and parameters.
     * @return void
     * @throws Caller
     */
    protected function infoClick(ActionParams $params): void
    {
        $dlg = $params->Control;    // get the dialog object from the form.
        $dlg->close(); // Close the dialog. Note that you could detect which button was clicked and only close on some of the buttons.
        Dialog::alert($params->ActionParameter . ' was clicked.', ['OK']);
    }

    /**
     * Displays a confirmation dialog with "Yes" and "No" options.
     *
     * Initializes and configures a dialog window that asks the user a yes-or-no question.
     * The dialog includes buttons for "Yes" and "No," and is set up to trigger an Ajax
     * action when a button is clicked. Additional properties such as resizable option
     * and close button visibility are configured.
     *
     * @return void
     */
    protected function showYesNoClick(): void
    {
        $dlgYesNo = new Dialog();    // Note here there is no "$this" as the first parameter. By leaving this off, you
                                     // are telling QCubed to manage the dialog.
        $dlgYesNo->Text = t("Do you like QCubed?");
        $dlgYesNo->addButton('Yes');
        $dlgYesNo->addButton('No');
        $dlgYesNo->addAction(new DialogButton(), new Ajax ('dlgYesNo_Button'));
        $dlgYesNo->Resizable = false;
        $dlgYesNo->HasCloseButton = false;
    }

    /**
     * Handles the button click event for a Yes/No dialog.
     *
     * @param ActionParams $params Represents the parameters associated with the action,
     *                             including the control and action parameter values.
     * @return void This method does not return any value.
     */
    protected function dlgYesNo_Button(ActionParams $params): void
    {
        $dlg = $params->Control;    // get the dialog object from the form.
        if ($params->ActionParameter == 'Yes') {
            $this->pnlAnswer->Text = t('They love me');
        } else {
            $this->pnlAnswer->Text = t('They love me not');
        }
        $dlg->close();
    }
}

// Run the Form we have defined
ExamplesForm::run('ExamplesForm');
