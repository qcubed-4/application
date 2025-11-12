<?php

use QCubed\Action\AjaxControl;
use QCubed\Control\ControlBase;
use QCubed\Control\FormBase;
use QCubed\Control\Panel;
use QCubed\Event\Click;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Control\DataGrid;
use QCubed\Project\Jqui\Button;
use QCubed\Query\QQ;

use QCubed\Project\Application;

class ProjectViewPanel extends Panel {
    // Child Controls must be Publicly Accessible so that they can be rendered in the template
    // Typically, you would want to do this by having public __getters for each control,
    // But for the simplicity of this demo, we'll simply make the child controls public, themselves.
    public Panel $pnlTitle;
    public DataGrid $dtgMembers;
    public Button $btnEditProject;

    // The Local Project object, which this panel represents
    protected mixed $objProject;

    // The Reference to the Main Form's "Right Panel" so that this panel
    // can make changes to the right panel on the page
    protected string $strPanelRightControlId;

    // Specify the Template File for this custom \QCubed\Control\Panel
    protected string $strTemplate = 'ProjectViewPanel.tpl.php';

    // Customize the Look/Feel
    protected string $strPadding = '10px';
    protected string $strBackColor = '#fefece';

    // We Create a new __constructor that takes in the Project we are "viewing"
    // The functionality of __construct in a custom \QCubed\Control\Panel is similar to the \QCubed\Project\Control\FormBase's formCreate() functionality
    /**
     * Constructor for the ProjectViewPanel.
     *
     * @param mixed $objParentObject The parent object which the ProjectViewPanel is bounded to.
     * @param mixed $objProject The project object containing the project details to be displayed and managed.
     * @param string $strPanelRightControlId The ID of the right control panel to enable interactions or updates.
     * @param string|null $strControlId The optional control ID for the current instance.
     *
     * @throws Caller
     * @throws InvalidCast
     */
    public function __construct(ControlBase $objParentObject, Project $objProject, string $strPanelRightControlId, ?string $strControlId = null) {

        // First, let's call the Parent's __constructor
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        // Next, we set the local project object
        $this->objProject = $objProject;

        /* Let's record the reference to the form's RightPanel
         * Note: this ProjectViewPanel needs the reference to the main form's RightPanel so that it can
         * "update" the RightPanel's contents during the ProjectViewPanel's event handlers (e.g., when the user
         * click's "Edit" on a Person, this ProjectViewPanel's btnEdit_Click handler will update RightPanel
         * to display the PersonEditPanel panel.
         *
         * HOWEVER, realize that this interaction can be done in many different ways.
         * A very suitable alternative would be for this __construct to take in a public method name from the Form instead
         * of $strPanelRightControlId.  And btnEdit_Click, instead of updating the right panel directly, could simply
         * make a call to the Form's method, and the interaction could be defined on the Form itself.
         *
         * This design decision depends on how tightly coupled the custom panels are together, or if each panel
         * is to be more independent, and you want the Form to define the interaction only.  So it would depend on how
         * the developer would want to do it.
         *
         * We show an example of accessing the RightPanel directly in ProjectViewPanel, and we show examples
         * of MethodCallbacks in the Form in ProjectEditPanel and PersonEditPanel.
         */
        $this->strPanelRightControlId = $strPanelRightControlId;

        // Let's set up some other local child control
        // Notice that we define the child controls' parents to be "this", which is this ProjectViewPanel object.
        $this->pnlTitle = new Panel($this);
        $this->pnlTitle->Text = $objProject->Name;
        $this->pnlTitle->CssClass = 'projectTitle';

        $this->btnEditProject = new Button($this);
        $this->btnEditProject->Text = 'Edit Project Name';
        $this->btnEditProject->addAction(new Click(), new AjaxControl($this, 'btnEditProject_Click'));

        // Now, let's set up this custom panel's child controls
        $this->dtgMembers = new DataGrid($this);
        $col = $this->dtgMembers->createNodeColumn('ID', QQN::Person()->Id);
        $col->CellStyler->Width = 30;
        $col = $this->dtgMembers->createNodeColumn('First Name', QQN::Person()->FirstName);
        $col->CellStyler->Width = 120;
        $col = $this->dtgMembers->createNodeColumn('Last Name', QQN::Person()->LastName);
        $col->CellStyler->Width = 120;
        $col = $this->dtgMembers->createCallableColumn('Edit', [$this, 'EditColumn_Render']);
        $col->HtmlEntities = false;


        // Let's make sorting Ajax-ivied
        $this->dtgMembers->UseAjax = true;

        // Finally, we take advantage of the DataGrid's SetDataBinder to specify the method we use to actually bind
        // a datasource to the DataGrid
        $this->dtgMembers->SetDataBinder('dtgMembers_Bind', $this);
    }

    // This is the method that will perform the actual data binding on the dtgMembers datagrid
    // Note that because it is called by the \QCubed\Project\Control\FormBase, this needs to be public
    public function dtgMembers_Bind(): void
    {
        $this->dtgMembers->DataSource = $this->objProject->getPersonAsTeamMemberArray(QQ::Clause($this->dtgMembers->OrderByClause));
    }

    // DataGrid Render Handlers Below
    public function EditColumn_Render(Person $objPerson): string
    {
        // Let's specify a specific Control ID for our button, using the datagrid's CurrentRowIndex
        $strControlId = 'btnEditPerson' . $this->dtgMembers->CurrentRowIndex;

        $btnEdit = $this->objForm->getControl($strControlId);
        if (!$btnEdit) {
            // Only create/instantiate a new Edit button for this Row if it doesn't yet exist
            $btnEdit = new Button($this->dtgMembers, $strControlId);
            $btnEdit->Text = 'Edit';

            // Define an Event Handler on the Button
            // Because the event handler itself is defined in the control, we use \QCubed\Action\AjaxControl instead of \QCubed\Action\Ajax
            $btnEdit->addAction(new Click(), new AjaxControl($this, 'btnEditPerson_Click'));
        }

        // Finally, update the ActionParameter for our button to store the $objPerson's ID.
        $btnEdit->ActionParameter = $objPerson->Id;

        // Return the Rendered Button Control
        return $btnEdit->render(false);
    }

    // Event Handlers Here
    public function btnEditPerson_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        // Get pnlRight from the Parent Form
        $pnlRight = $this->objForm->getControl($this->strPanelRightControlId);

        // First, remove all children panels from pnlRight
        $pnlRight->removeChildControls(true);

        // Now create a new PersonEditPanel, setting pnlRight as its parent
        // and specifying parent form's "CloseRightPanel" as the method callback
        // See the note in _constructor, above, for more information
        $objPersonToEdit = Person::load($strParameter);

        //Application::displayAlert('PERSON-ID: ' . print_r($objPersonToEdit, true));

        if ($objPersonToEdit) {
            new PersonEditPanel($pnlRight, $objPersonToEdit, 'CloseRightPanel');
        }
    }

    public function btnEditProject_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        // Get pnlRight from the Parent Form
        $pnlRight = $this->objForm->getControl($this->strPanelRightControlId);

        if (!$pnlRight instanceof Panel) {
            // Application::displayAlert('Viga: PanelRight (' . $this->strPanelRightControlId . ') puudub!');
            return;
        }


        // First, remove all children panels from pnlRight
        $pnlRight->removeChildControls(true);

        // Now create a new PersonEditPanel, setting pnlRight as its parent
        // and specifying parent form's "CloseRightPanel" as the method callback
        // See the note in _constructor, above, for more information
        new ProjectEditPanel($pnlRight, $this->objProject, 'CloseRightPanel');
    }
}