<?php
	/* Note that most of the code from this was copied from the code generated PersonEditFormBase.
	 * The main differences is we add a new constructor (replacing Form_Create).  And also, instead
	 * of using the QueryString to determine the person, SetupPerson() takes in an nullable $objPerson
	 * parameter.
	 *
	 * Finally, Save and Cancel simply closes/removes the control from the form, itself, instead
	 * of "redirecting" to a List page.  (Delete was removed for purposes of the demo).  To implement
	 * this, we updated btnSave_Create() and btnCancel_Create() to execute QAjaxControlActions instead of
	 * QServerActions.  And then the event handlers themselves calls the Form's MethodCallback instead of
	 * \QCubed\Project\Application::Redirect().
	 *
	 * Also, the template file was modified so that $_CONTROL-> is used instead of $this->
	 */

	class PersonEditPanel extends \QCubed\Control\Panel {
		// General Form Variables
		protected $objPerson;
		public $strTitleVerb;
		protected $blnEditMode;
		
		// The Method CallBack after Save or Cancel has been clicked
		protected $strMethodCallBack;

		// Controls for Person's Data Fields
		// Notice that because the FORM is rendering these items, we need to make sure the controls are "public"
		public $lblId;
		public $txtFirstName;
		public $txtLastName;

		// Other ListBoxes (if applicable) via Unique ReverseReferences and ManyToMany References
		public $lstLogin;
		public $lstProjectsAsTeamMember;

		// Button Actions
		public $btnSave;
		public $btnCancel;
		
		// Specify the Teamplte File
		protected $strTemplate = 'PersonEditPanel.tpl.php';
		
		// Customize Look/Feel
		protected $strPadding = '10px';
		protected $strBackColor = '#fefece';

		protected function SetupPerson($objPerson) {
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

		public function __construct($objParentObject, $objPerson, $strMethodCallBack, $strControlId = null) {
			// Call the Parent
			try {
				parent::__construct($objParentObject, $strControlId);
			} catch (\QCubed\Exception\Caller $objExc) {
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

		// Protected Create Methods
		// Create and Setup lblId
		protected function lblId_Create() {
			$this->lblId = new \QCubed\Control\Label($this);
			$this->lblId->Name = t('Id');
			if ($this->blnEditMode)
				$this->lblId->Text = $this->objPerson->Id;
			else
				$this->lblId->Text = 'N/A';
		}

		// Create and Setup txtFirstName
		protected function txtFirstName_Create() {
			$this->txtFirstName = new \QCubed\Project\Control\TextBox($this);
			$this->txtFirstName->Name = t('First Name');
			$this->txtFirstName->Text = $this->objPerson->FirstName;
			$this->txtFirstName->Required = true;
		}

		// Create and Setup txtLastName
		protected function txtLastName_Create() {
			$this->txtLastName = new \QCubed\Project\Control\TextBox($this);
			$this->txtLastName->Name = t('Last Name');
			$this->txtLastName->Text = $this->objPerson->LastName;
			$this->txtLastName->Required = true;
		}

		// Create and Setup lstLogin
		protected function lstLogin_Create() {
			$this->lstLogin = new \QCubed\Project\Control\ListBox($this);
			$this->lstLogin->Name = t('Login');
			$this->lstLogin->AddItem(t('- Select One -'), null);
			$objLoginArray = Login::LoadAll();
			if ($objLoginArray) foreach ($objLoginArray as $objLogin) {
				$objListItem = new \QCubed\Control\ListItem($objLogin->__toString(), $objLogin->Id);
				if ($objLogin->PersonId == $this->objPerson->Id)
					$objListItem->Selected = true;
				$this->lstLogin->AddItem($objListItem);
			}
			// Because Login's Login is not null, if a value is already selected, it cannot be changed.
			if ($this->lstLogin->SelectedValue)
				$this->lstLogin->Enabled = false;
		}

		// Create and Setup lstProjectsAsTeamMember
		protected function lstProjectsAsTeamMember_Create() {
			$this->lstProjectsAsTeamMember = new \QCubed\Project\Control\ListBox($this);
			$this->lstProjectsAsTeamMember->Name = t('Projects As Team Member');
			$this->lstProjectsAsTeamMember->SelectionMode = \QCubed\Control\ListBoxBase::SELECTION_MODE_MULTIPLE;
			$objAssociatedArray = $this->objPerson->GetProjectAsTeamMemberArray();
			$objProjectArray = Project::LoadAll();
			if ($objProjectArray) foreach ($objProjectArray as $objProject) {
				$objListItem = new \QCubed\Control\ListItem($objProject->__toString(), $objProject->Id);
				foreach ($objAssociatedArray as $objAssociated) {
					if ($objAssociated->Id == $objProject->Id)
						$objListItem->Selected = true;
				}
				$this->lstProjectsAsTeamMember->AddItem($objListItem);
			}
		}


		// Setup btnSave
		protected function btnSave_Create() {
			$this->btnSave = new \QCubed\Project\Jqui\Button($this);
			$this->btnSave->Text = t('Save');
			$this->btnSave->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\AjaxControl($this, 'btnSave_Click'));
			$this->btnSave->PrimaryButton = true;
			$this->btnSave->CausesValidation = true;
		}

		// Setup btnCancel
		protected function btnCancel_Create() {
			$this->btnCancel = new \QCubed\Project\Jqui\Button($this);
			$this->btnCancel->Text = t('Cancel');
			$this->btnCancel->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\AjaxControl($this, 'btnCancel_Click'));
			$this->btnCancel->CausesValidation = false;
		}

		// Protected Update Methods
		protected function UpdatePersonFields() {
			$this->objPerson->FirstName = $this->txtFirstName->Text;
			$this->objPerson->LastName = $this->txtLastName->Text;
			$this->objPerson->Login = Login::Load($this->lstLogin->SelectedValue);
		}

		protected function lstProjectsAsTeamMember_Update() {
			$this->objPerson->UnassociateAllProjectsAsTeamMember();
			$objSelectedListItems = $this->lstProjectsAsTeamMember->SelectedItems;
			if ($objSelectedListItems) foreach ($objSelectedListItems as $objListItem) {
				$this->objPerson->AssociateProjectAsTeamMember(Project::Load($objListItem->Value));
			}
		}


		// Event Handlers
		public function btnSave_Click($strFormId, $strControlId, $strParameter) {
			$this->UpdatePersonFields();
			$this->objPerson->Save();

			$this->lstProjectsAsTeamMember_Update();

			// And call the Form's Method CallBack, passing in "true" to state that we've made an update
			$strMethodCallBack = $this->strMethodCallBack;
			$this->objForm->$strMethodCallBack(true);
		}

		public function btnCancel_Click($strFormId, $strControlId, $strParameter) {
			// Call the Form's Method CallBack, passing in "false" to state that we've made no changes
			$strMethodCallBack = $this->strMethodCallBack;
			$this->objForm->$strMethodCallBack(false);
		}
	}
?>