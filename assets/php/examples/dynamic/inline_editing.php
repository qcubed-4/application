<?php /** @noinspection PhpUnhandledExceptionInspection */

use QCubed\Action\Ajax;
use QCubed\Action\Terminate;
use QCubed\Event\Click;
use QCubed\Event\EscapeKey;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\DataGrid;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\TextBox;
use QCubed\QString;
use QCubed\Query\QQ;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{
    // Declare the DataGrid, and the buttons and textboxes for inline editing
    protected DataGrid $dtgPersons;
    protected TextBox $txtFirstName;
    protected TextBox $txtLastName;
    protected Button $btnSave;
    protected Button $btnCancel;
    protected Button $btnNew;

    protected mixed $intEditPersonId = null;
    protected array $objPersonArray = [];

    /**
     * @throws InvalidCast
     * @throws Caller
     */
    protected function formCreate(): void
    {
        // Define the DataGrid
        $this->dtgPersons = new DataGrid($this);

        // Define Columns -- we will define render helper methods to help with the rendering
        // of the HTML for most of these columns
        $col = $this->dtgPersons->createNodeColumn('Person Id', QQN::person()->Id);
        $col->CellStyler->Width = 100;
        $col = $this->dtgPersons->createCallableColumn('First Name', [$this, 'FirstNameColumn_Render']);
        $col->CellStyler->Width = 200;
        $col->HtmlEntities = false;
        $col = $this->dtgPersons->createCallableColumn('Last Name', [$this, 'LastNameColumn_Render']);
        $col->HtmlEntities = false;
        $col->CellStyler->Width = 200;
        $col = $this->dtgPersons->createCallableColumn('Edit', [$this, 'EditColumn_Render']);
        $col->HtmlEntities = false;
        $col->CellStyler->Width = 200;

        // Let's pre-default the sorting by an id (column index #0) and use AJAX
        //$this->dtgPersons->SortColumnIndex = 2;
        $this->dtgPersons->UseAjax = true;

        // Specify the DataBinder method for the DataGrid
        $this->dtgPersons->setDataBinder('dtgPersons_Bind');

        // Create the other textboxes and buttons -- make sure we specify
        // the datagrid as the parent.  If they hit the escape key, let's perform a Cancel.
        // Note that we need to terminate the action on the escape key event; too, b/c
        // many browsers will perform additional processing that we won't want.
        $this->txtFirstName = new TextBox($this->dtgPersons);
        $this->txtFirstName->Required = true;
        $this->txtFirstName->MaxLength = 50;
        $this->txtFirstName->Width = 200;
        $this->txtFirstName->addAction(new EscapeKey(), new Ajax('btnCancel_Click'));
        $this->txtFirstName->addAction(new EscapeKey(), new Terminate());

        $this->txtLastName = new TextBox($this->dtgPersons);
        $this->txtLastName->Required = true;
        $this->txtLastName->MaxLength = 50;
        $this->txtLastName->Width = 200;
        $this->txtLastName->addAction(new EscapeKey(), new Ajax('btnCancel_Click'));
        $this->txtLastName->addAction(new EscapeKey(), new Terminate());

        // We want the Save button to be Primary, so that the save will perform if the
        // user hits the enter key in either of the textboxes.
        $this->btnSave = new Button($this->dtgPersons);
        $this->btnSave->Text = 'Save';
        $this->btnSave->CausesValidation = true;
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->addAction(new Click(), new Ajax('btnSave_Click'));

        // Make sure we turn off validation on the Cancel button
        $this->btnCancel = new Button($this->dtgPersons);
        $this->btnCancel->Text = 'Cancel';
        $this->btnCancel->CausesValidation = false;
        $this->btnCancel->addAction(new Click(), new Ajax('btnCancel_Click'));

        // Finally, let's add a "New" button
        $this->btnNew = new Button($this);
        $this->btnNew->Text = 'New';
        $this->btnNew->CausesValidation = true;
        $this->btnNew->addAction(new Click(), new Ajax('btnNew_Click'));
    }

    /**
     * @throws Caller
     */
    protected function dtgPersons_Bind(): void
    {
        if (isset($this)) $this->objPersonArray = $this->dtgPersons->DataSource = Person::loadAll(QQ::clause(
            $this->dtgPersons->OrderByClause,
            $this->dtgPersons->LimitClause
        ));

        // If we are editing someone new, we need to add a new (blank) person to the data source
        if ($this->intEditPersonId == -1) {
            $this->objPersonArray[] = new Person();
        }

        // Bind the datasource to the datagrid
        $this->dtgPersons->DataSource = $this->objPersonArray;
    }

    // When we Render, we need to see if we are currently editing someone
    protected function formPreRender(): void
    {
        // We want to force the datagrid to refresh on EVERY button click
        // Normally, the datagrid won't re-render on the ajax actions because nothing
        // in the datagrid itself is being modified.  But considering that every ajax action
        // on the page (e.g., every click button) makes changes to things that AFFECT the datagrid,
        // we need to explicitly force the datagrid to "refresh" on every event/action.  Therefore,
        // we make the call to Refresh() in Form_PreRender
        $this->dtgPersons->refresh();

        // If we are adding or editing a person, then we should disable the edit button
        if ($this->intEditPersonId) {
            $this->btnNew->Enabled = false;
        } else {
            $this->btnNew->Enabled = true;
        }
    }

    // If the person for the row we are rendering is currently being edited,
    // show the textbox.  Otherwise, display the contents as is.
    /**
     * @throws Caller
     */
    public function firstNameColumn_Render(Person $objPerson): string
    {
        if (($objPerson->Id == $this->intEditPersonId) ||
            (($this->intEditPersonId == -1) && (!$objPerson->Id))
        ) {
            return $this->txtFirstName->renderWithError(false);
        } else {
            // Since we are rendering with HtmlEntities set to false in this column,
            // We must definitely avoid the value
            return QString::htmlEntities($objPerson->FirstName);
        }
    }

    // If the person for the row we are rendering is currently being edited,
    // show the textbox.  Otherwise, display the contents as is.
    /**
     * @throws Caller
     */
    public function lastNameColumn_Render(Person $objPerson): string
    {
        if (($objPerson->Id == $this->intEditPersonId) ||
            (($this->intEditPersonId == -1) && (!$objPerson->Id))
        ) {
            return $this->txtLastName->renderWithError(false);
        } else {
            // Since we are rendering with HtmlEntities set to false in this column,
            // we must definitely avoid the value
            return QString::htmlEntities($objPerson->LastName);
        }
    }

    // If the person for the row we are rendering is currently being edited,
    // show the Save & Cancel buttons.  And the rest of the row edit buttons
    // should be disabled.  Otherwise, show the edit button normally.
    /**
     * @throws Caller
     */
    public function editColumn_Render(Person $objPerson): string
    {
        if (($objPerson->Id == $this->intEditPersonId) ||
            (($this->intEditPersonId == -1) && (!$objPerson->Id))
        )
            // We are rendering the row of the person we are editing, OR we are rending the row
            // of the NEW (blank) person.  Go ahead and render the Save and Cancel buttons.
        {
            return $this->btnSave->render(false) . '&nbsp;' . $this->btnCancel->render(false);
        } else {
            // Get the Edit button for this row (we will create it if it doesn't yet exist)
            $strControlId = 'btnEdit' . $objPerson->Id;
            $btnEdit = $this->getControl($strControlId);
            if (!$btnEdit) {
                // Create the Edit button for this row in the DataGrid
                // Use ActionParameter to specify the ID of the person
                $btnEdit = new Button($this->dtgPersons, $strControlId);
                $btnEdit->Text = 'Edit This Person';
                $btnEdit->ActionParameter = $objPerson->Id;
                $btnEdit->CausesValidation = false;
                $btnEdit->addAction(new Click(), new Ajax('btnEdit_Click'));
            }

            // If we are currently editing a person, then set this Edit button to be disabled
            if ($this->intEditPersonId) {
                $btnEdit->Enabled = false;
            } else {
                $btnEdit->Enabled = true;
            }

            // Return the rendered Edit button
            return $btnEdit->render(false);
        }
    }

    // Handle the action for the Edit button being clicked.  We must
    // set up the FirstName and LastName textboxes to contain the name of the person
    // we are editing.
    /**
     * Handles the click event for the edit button.
     * Loads the person's details using the provided parameter and pre-fills the associated form fields.
     * Focus is set on the FirstName text box after data is loaded.
     *
     * @param string $strFormId The ID of the form where the button is located.
     * @param string $strControlId The ID of the clicked control.
     * @param mixed $strParameter The parameter provided, typically the ID of the person to edit.
     * @return void
     * @throws Caller
     */
    protected function btnEdit_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        $this->intEditPersonId = $strParameter;
        $objPerson = Person::load($strParameter);
        $this->txtFirstName->Text = $objPerson->FirstName;
        $this->txtLastName->Text = $objPerson->LastName;

        // Let's put the focus on the FirstName Textbox
        Application::executeControlCommand($this->txtFirstName->ControlId, 'focus');
    }

    // Handle the action for the Save button being clicked.

    /**
     * Handles the save button click event. Creates a new Person object if no existing ID is being edited,
     * otherwise loads the existing Person object, updates the fields, and saves changes to the database.
     *
     * @param string $strFormId The ID of the form from which the event originated.
     * @param string $strControlId The ID of the control that triggered the event.
     * @param string $strParameter Additional parameters passed during the event.
     * @return void
     * @throws Exception If the save operation fails.
     */
    protected function btnSave_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        if ($this->intEditPersonId == -1) {
            $objPerson = new Person();
        } else {
            $objPerson = Person::load($this->intEditPersonId);
        }

        if ($this->txtFirstName->Text && $this->txtLastName->Text) {
            $objPerson->FirstName = trim($this->txtFirstName->Text);
            $objPerson->LastName = trim($this->txtLastName->Text);

            //$objPerson->save();
        }

        Application::displayAlert("In the real application, the person's data can be updated or a new person can be added");

        $this->intEditPersonId = null;
    }

    // Handle the action for the Cancel button being clicked.

    /**
     * Handles the cancel button click event, resetting the editing state by nullifying the edit person ID.
     *
     * @param string $strFormId The ID of the form that contains the cancel button.
     * @param string $strControlId The ID of the button control that triggered the event.
     * @param string $strParameter Additional parameters or context for the event, if any.
     * @return void
     */
    protected function btnCancel_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        $this->intEditPersonId = null;
    }

    // Handle the action for the New button being clicked. Clear the
    // contents of the Firstname and LastName textboxes.
    protected function btnNew_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        //Application::displayAlert("In the real application, the person's data can be updated or a new person can be added");

        $this->intEditPersonId = -1;
        $this->txtFirstName->Text = '';
        $this->txtLastName->Text = '';

        // Let's put the focus on the FirstName Textbox
        $this->txtFirstName->focus();
    }
}
ExampleForm::run('ExampleForm');