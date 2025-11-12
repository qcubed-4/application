<?php
/**
 * Note that most of the code from this was copied from the code generated PersonEditFormBase.
 * The main difference is we add a new constructor (replacing Form_Create).  And also, instead
 * of using the QueryString to determine the person, SetupPerson() takes in a nullable $objPerson
 * parameter.
 *
 * Finally, Save and Cancel simply closes/removes the control from the form, itself, instead
 * of "redirecting" to a List page.  (Delete was removed for purposes of the demo).  To implement
 * this, we updated btnSave_Create() and btnCancel_Create() to execute AjaxControl instead of
 * Server.  And then the event handlers themselves call the Form's MethodCallback instead of
 * \QCubed\Project\Application::Redirect().
 *
 * Also, the template file was modified so that $_CONTROL-> is used instead of $this->
 */

use QCubed\Action\AjaxControl;
use QCubed\Control\ControlBase;
use QCubed\Control\Label;
use QCubed\Control\ListBoxBase;
use QCubed\Control\ListItem;
use QCubed\Control\Panel;
use QCubed\Database\Exception\UndefinedPrimaryKey;
use QCubed\Event\Click;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Project\Control\ListBox;
use QCubed\Project\Control\TextBox;
use QCubed\Project\Jqui\Button;

class PersonEditPanel extends Panel {
		// General Form Variables
		protected Person $objPerson;
		public string $strTitleVerb;
		protected bool $blnEditMode;
		
		// The Method CallBack after Save or Cancel has been clicked
		protected string $strMethodCallBack;

		// Controls for Person's Data Fields
		// Notice that because the FORM is rendering these items, we need to make sure the controls are "public"
		public Label $lblId;
		public TextBox $txtFirstName;
		public TextBox $txtLastName;

		// Other ListBoxes (if applicable) via Unique ReverseReferences and ManyToMany References
		public ListBox $lstLogin;
		public ListBox $lstProjectsAsTeamMember;

		// Button Actions
		public Button $btnSave;
		public Button $btnCancel;
		
		// Specify the Template File
		protected string $strTemplate = 'PersonEditPanel.tpl.php';
		
		// Customize Look/Feel
		protected string $strPadding = '10px';
		protected string $strBackColor = '#fefece';

    /**
     * Sets up a Person object for editing or creation.
     *
     * @param Person|null $objPerson The Person objects to be edited. If null, a new Person object will be created.
     * @return void
     */
    protected function SetupPerson(?Person $objPerson): void
    {
			// See if a Person Object was passed in (meaning we're editing an existing person)
			// Otherwise, we're creating a new one
			if ($objPerson) {
				$this->objPerson = $objPerson;
				$this->strTitleVerb = t('Edit');
				$this->blnEditMode = true;
			} else {
				$this->objPerson = new Person();
				$this->strTitleVerb = t('Create');
				$this->blnEditMode = false;
			}
		}

    /**
     * Constructor method.
     *
     * Initializes the object, sets up required properties and controls,
     * and establishes references to the specified parameters.
     *
     * @param mixed $objParentObject Reference to the parent object.
     * @param mixed $objPerson The person object to be loaded or created.
     * @param string $strMethodCallBack The callback method to be triggered.
     * @param string|null $strControlId Optional control ID, defaults to null.
     *
     * @throws Caller
     * @throws DateMalformedStringException
     * @throws InvalidCast
     */
    public function __construct(ControlBase $objParentObject, ?Person $objPerson, string $strMethodCallBack, ?string $strControlId = null) {
        // Call the Parent
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        // Let's record the reference to the form's MethodCallBack
        // See note in ProjectViewPanel for more on this.
        $this->strMethodCallBack = $strMethodCallBack;

        // Call SetupPerson to either Load/Edit Existing or Create New
        $this->SetupPerson($objPerson);

        // Create/Setup Controls for Person's Data Fields
        $this->lblId_Create();
        $this->txtFirstName_Create();
        $this->txtLastName_Create();

        // Create/Setup ListBoxes (if applicable) via Unique ReverseReferences and ManyToMany References
        $this->lstLogin_Create();
        $this->lstProjectsAsTeamMember_Create();

        // Create/Setup Button Action controls
        $this->btnSave_Create();
        $this->btnCancel_Create();
    }

    /**
     * Creates and initializes the label control for displaying the ID.
     *
     * This method sets up the label control to show the ID of the associated person object.
     * If in edit mode, the label displays the ID value of the given person.
     * Otherwise, it displays 'N/A'.
     *
     * @return void
     * @throws Caller
     */
    protected function lblId_Create(): void
    {
        $this->lblId = new Label($this);
        $this->lblId->Name = t('Id');
        $this->lblId->Text = $this->blnEditMode ? (string)$this->objPerson->Id : 'N/A';
    }

    /**
     * Creates and initializes the text box for the "First Name" field.
     *
     * This method sets up the text box control to display and edit
     * the "First Name" property of the associated person object. The control
     * is marked as required and pre-populated with the current value.
     *
     * @return void
     * @throws Caller
     */
    protected function txtFirstName_Create(): void
    {
        $this->txtFirstName = new TextBox($this);
        $this->txtFirstName->Name = t('First Name');
        $this->txtFirstName->Text = $this->objPerson->FirstName;
        $this->txtFirstName->Required = true;
    }

    /**
     * Creates and initializes the Last Name text box control.
     *
     * This method sets up the text box for entering the last name,
     * assigns a label, binds it to the person object, and enforces
     * that the field is required.
     *
     * @return void
     * @throws Caller
     */
    protected function txtLastName_Create(): void
    {
        $this->txtLastName = new TextBox($this);
        $this->txtLastName->Name = t('Last Name');
        $this->txtLastName->Text = $this->objPerson->LastName;
        $this->txtLastName->Required = true;
		}

    protected function lstLogin_Create(): void
    {
        $this->lstLogin = new ListBox($this);
        $this->lstLogin->Name = t('Login');
        $this->lstLogin->AddItem(t('- Select One -'), null);
        $objLoginArray = Login::LoadAll();

        if ($objLoginArray) {
            foreach ($objLoginArray as $objLogin) {
                $objListItem = new ListItem($objLogin->__toString(), $objLogin->Id);
                if ($objLogin->PersonId === $this->objPerson->Id) {
                    $objListItem->Selected = true;
                }
                $this->lstLogin->AddItem($objListItem);
            }
        }
        // Kui on konkreetselt valitud (mittetühi ja mitte-null), siis ära lase muuta
        if ($this->lstLogin->SelectedValue !== null && $this->lstLogin->SelectedValue !== '') {
            $this->lstLogin->Enabled = false;
        }
    }

    protected function lstProjectsAsTeamMember_Create(): void
    {
        $this->lstProjectsAsTeamMember = new ListBox($this);
        $this->lstProjectsAsTeamMember->Name = t('Projects As Team Member');
        $this->lstProjectsAsTeamMember->SelectionMode = ListBoxBase::SELECTION_MODE_MULTIPLE;
        $objAssociatedArray = $this->objPerson->getProjectAsTeamMemberArray();
        $objProjectArray = Project::LoadAll();
        if ($objProjectArray) foreach ($objProjectArray as $objProject) {
            $objListItem = new ListItem($objProject->__toString(), $objProject->Id);
            foreach ($objAssociatedArray as $objAssociated) {
                if ($objAssociated->Id == $objProject->Id) {
                    $objListItem->Selected = true;
                    break;
                }
            }
            $this->lstProjectsAsTeamMember->AddItem($objListItem);
        }
    }


    /**
     * Creates and initializes the "Save" button control.
     *
     * The button is configured with a label, click event handling, validation requirements,
     * and is marked as the primary button within the form.
     *
     * @return void
     * @throws Caller
     */
    protected function btnSave_Create(): void
    {
        $this->btnSave = new Button($this);
        $this->btnSave->Text = t('Save');
        $this->btnSave->addAction(new Click(), new AjaxControl($this, 'btnSave_Click'));
        $this->btnSave->PrimaryButton = true;
        $this->btnSave->CausesValidation = true;
    }

    // Setup btnCancel

    /**
     * Creates and initializes the "Cancel" button, setting its properties and actions.
     * The button does not trigger validation and calls a specified action on a click.
     *
     * @return void
     * @throws Caller
     */
    protected function btnCancel_Create(): void
    {
        $this->btnCancel = new Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->AddAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));
        $this->btnCancel->CausesValidation = false;
    }

    // Protected Update Methods

    /**
     * Updates the fields of the person object with values from user inputs.
     *
     * This method assigns the first name, last name, and login information
     * to the respective properties of the person object based on the current input values.
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    protected function UpdatePersonFields(): void
    {
        $this->objPerson->FirstName = $this->txtFirstName->Text;
        $this->objPerson->LastName = $this->txtLastName->Text;
        $this->objPerson->Login = Login::Load($this->lstLogin->SelectedValue);
    }

    /**
     * Updates the list of associated projects for the person as a team member.
     *
     * This method removes all existing project associations for the person
     * and re-associates them based on the selected items in the list control.
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     * @throws UndefinedPrimaryKey
     */
    protected function lstProjectsAsTeamMember_Update(): void
    {
        $this->objPerson->UnassociateAllProjectsAsTeamMember();
        $objSelectedListItems = $this->lstProjectsAsTeamMember->SelectedItems;
        if ($objSelectedListItems) foreach ($objSelectedListItems as $objListItem) {
            $objProject = Project::Load($objListItem->Value);
            if ($objProject !== null) {
                $this->objPerson->AssociateProjectAsTeamMember($objProject);
            }
        }
    }


    // Event Handlers

    /**
     * Click the event handler to save a button.
     *
     * Handles the save operation by updating the person fields, saving the person object,
     * updating related project memberships, and invoking the callback method to confirm the update.
     *
     * @param string $strFormId The ID of the form triggering the event.
     * @param string $strControlId The ID of the control triggering the event.
     * @param string $strParameter Additional parameters passed with the event.
     *
     * @return void
     * @throws Caller
     * @throws UndefinedPrimaryKey
     */
    public function btnSave_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        $this->UpdatePersonFields();
        $this->objPerson->Save();

        $this->lstProjectsAsTeamMember_Update();

        // And call the Form's Method CallBack, passing in "true" to state that we've made an update
        $strMethodCallBack = $this->strMethodCallBack;
        $this->objForm->$strMethodCallBack(true);
    }

    /**
     * Handles the click event for the cancel button.
     *
     * Calls the form's specified callback method, indicating that no changes have been made.
     *
     * @param string $strFormId The ID of the form containing the cancel button.
     * @param string $strControlId The ID of the cancel button control.
     * @param string $strParameter An additional parameter passed with the event.
     *
     * @return void
     */
    public function btnCancel_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        // Call the Form's Method CallBack, passing in "false" to state that we've made no changes
        $strMethodCallBack = $this->strMethodCallBack;
        $this->objForm->$strMethodCallBack(false);
    }
}