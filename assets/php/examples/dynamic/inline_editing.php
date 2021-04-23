<?php
use QCubed\Action\Ajax;
use QCubed\Action\Terminate;
use QCubed\Event\Click;
use QCubed\Event\EscapeKey;
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
    protected $dtgPersons;
    protected $txtFirstName;
    protected $txtLastName;
    protected $btnSave;
    protected $btnCancel;
    protected $btnNew;

    // This value is either a Person->Id, "null" (if nothing is being edited), or "-1" (if creating a new Person)
    protected $intEditPersonId = null;

    protected function formCreate()
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
        $col->CellStyler->Width = 120;

        // Let's pre-default the sorting by id (column index #0) and use AJAX
        $this->dtgPersons->SortColumnIndex = 0;
        $this->dtgPersons->UseAjax = true;

        // Specify the DataBinder method for the DataGrid
        $this->dtgPersons->setDataBinder('dtgPersons_Bind');

        // Create the other textboxes and buttons -- make sure we specify
        // the datagrid as the parent.  If they hit the escape key, let's perform a Cancel.
        // Note that we need to terminate the action on the escape key event, too, b/c
        // many browsers will perform additional processing that we won't not want.
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
        $this->btnSave->addAction(new Click(), new Ajax('btnSave_Click'));
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->CausesValidation = true;

        // Make sure we turn off validation on the Cancel button
        $this->btnCancel = new Button($this->dtgPersons);
        $this->btnCancel->Text = 'Cancel';
        $this->btnCancel->addAction(new Click(), new Ajax('btnCancel_Click'));
        $this->btnCancel->CausesValidation = false;

        // Finally, let's add a "New" button
        $this->btnNew = new Button($this);
        $this->btnNew->Text = 'New';
        $this->btnNew->addAction(new Click(), new Ajax('btnNew_Click'));
        $this->btnNew->CausesValidation = false;
    }

    protected function dtgPersons_Bind()
    {
        $objPersonArray = $this->dtgPersons->DataSource = Person::loadAll(QQ::clause(
            $this->dtgPersons->OrderByClause,
            $this->dtgPersons->LimitClause
        ));

        // If we are editing someone new, we need to add a new (blank) person to the data source
        if ($this->intEditPersonId == -1) {
            array_push($objPersonArray, new Person());
        }

        // Bind the datasource to the datagrid
        $this->dtgPersons->DataSource = $objPersonArray;
    }

    // When we Render, we need to see if we are currently editing someone
    protected function formPreRender()
    {
        // We want to force the datagrid to refresh on EVERY button click
        // Normally, the datagrid won't re-render on the ajaxactions because nothing
        // in the datagrid, itself, is being modified.  But considering that every ajax action
        // on the page (e.g. every button click) makes changes to things that AFFECT the datagrid,
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
    public function firstNameColumn_Render(Person $objPerson)
    {
        if (($objPerson->Id == $this->intEditPersonId) ||
            (($this->intEditPersonId == -1) && (!$objPerson->Id))
        ) {
            return $this->txtFirstName->renderWithError(false);
        } else
            // Because we are rendering with HtmlEntities set to false on this column
            // we need to make sure to escape the value
        {
            return QString::htmlEntities($objPerson->FirstName);
        }
    }

    // If the person for the row we are rendering is currently being edited,
    // show the textbox.  Otherwise, display the contents as is.
    public function lastNameColumn_Render(Person $objPerson)
    {
        if (($objPerson->Id == $this->intEditPersonId) ||
            (($this->intEditPersonId == -1) && (!$objPerson->Id))
        ) {
            return $this->txtLastName->renderWithError(false);
        } else
            // Because we are rendering with HtmlEntities set to false on this column
            // we need to make sure to escape the value
        {
            return QString::htmlEntities($objPerson->LastName);
        }
    }

    // If the person for the row we are rendering is currently being edited,
    // show the Save & Cancel buttons.  And the rest of the rows edit buttons
    // should be disabled.  Otherwise, show the edit button normally.
    public function editColumn_Render(Person $objPerson)
    {
        if (($objPerson->Id == $this->intEditPersonId) ||
            (($this->intEditPersonId == -1) && (!$objPerson->Id))
        )
            // We are rendering the row of the person we are editing OR we are rending the row
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
                $btnEdit->addAction(new Click(), new Ajax('btnEdit_Click'));
                $btnEdit->CausesValidation = false;
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
    // setup the FirstName and LastName textboxes to contain the name of the person
    // we are editing.
    protected function btnEdit_Click($strFormId, $strControlId, $strParameter)
    {
        $this->intEditPersonId = $strParameter;
        $objPerson = Person::load($strParameter);
        $this->txtFirstName->Text = $objPerson->FirstName;
        $this->txtLastName->Text = $objPerson->LastName;

        // Let's put the focus on the FirstName Textbox
        Application::executeControlCommand($this->txtFirstName->ControlId, 'focus');
    }

    // Handle the action for the Save button being clicked.
    protected function btnSave_Click($strFormId, $strControlId, $strParameter)
    {
        if ($this->intEditPersonId == -1) {
            $objPerson = new Person();
        } else {
            $objPerson = Person::load($this->intEditPersonId);
        }

        $objPerson->FirstName = trim($this->txtFirstName->Text);
        $objPerson->LastName = trim($this->txtLastName->Text);
        $objPerson->save();

        $this->intEditPersonId = null;
    }

    // Handle the action for the Cancel button being clicked.
    protected function btnCancel_Click($strFormId, $strControlId, $strParameter)
    {
        $this->intEditPersonId = null;
    }

    // Handle the action for the New button being clicked.  Clear the
    // contents of the Firstname and LastName textboxes.
    protected function btnNew_Click($strFormId, $strControlId, $strParameter)
    {
        $this->intEditPersonId = -1;
        $this->txtFirstName->Text = '';
        $this->txtLastName->Text = '';

        // Let's put the focus on the FirstName Textbox
        $this->txtFirstName->focus();
    }
}

ExampleForm::run('ExampleForm');

