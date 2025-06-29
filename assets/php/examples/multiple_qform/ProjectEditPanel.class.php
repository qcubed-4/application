<?php

use QCubed\Action\AjaxControl;
use QCubed\Action\Terminate;
use QCubed\Control\ControlBase;
use QCubed\Control\FormBase;
use QCubed\Control\Panel;
use QCubed\Event\Click;
use QCubed\Event\EnterKey;
use QCubed\Event\EscapeKey;
use QCubed\Exception\Caller;
use QCubed\Project\Control\TextBox;
use QCubed\Project\Jqui\Button;

use QCubed\Project\Application;

class ProjectEditPanel extends Panel {
    // Child Controls must be Publicly Accessible so that they can be rendered in the template
    // Typically, you would want to do this by having public __getters for each control,
    // But for the simplicity of this demo, we'll simply make the child controls public, themselves.
    public TextBox $txtName;
    public Button $btnSave;
    public Button $btnCancel;

    // The Local Project object, which this panel represents
    protected mixed $objProject;

    // The Reference to the Main Form's "Method Callback" so that the form can perform additional
    // tasks after save or cancel has been clicked
    protected string $strMethodCallBack;

    // Specify the Template File for this custom \QCubed\Control\Panel
    protected string $strTemplate = 'ProjectEditPanel.tpl.php';

    // Customize the Look/Feel
    protected string $strPadding = '10px';
    protected string $strBackColor = '#fefece';

    // We Create a new __constructor that takes in the Project we are "viewing"
    // The functionality of __construct in a custom \QCubed\Control\Panel is similar to the \QCubed\Project\Control\FormBase's formCreate() functionality
    /**
     * Constructor for initializing the panel with the specified parent object, project, callback method, and optional control ID.
     *
     * @param mixed $objParentObject The parent object to which this panel belongs.
     * @param mixed $objProject The project object used to initialize and configure the panel.
     * @param string $strMethodCallBack The callback method for handling specific panel events.
     * @param string|null $strControlId (Optional) The control ID to uniquely identify this panel instance.
     *
     * @throws Caller
     */
    public function __construct(ControlBase $objParentObject, Project $objProject, string $strMethodCallBack, ?string $strControlId = null) {
        // First, let's call the Parent's __constructor
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        // Next, we set the local project object
        $this->objProject = $objProject;

        // Let's record the reference to the form's MethodCallBack
        // See note in ProjectViewPanel for more on this.
        $this->strMethodCallBack = $strMethodCallBack;



        // Let's set up the other local child control
        // Notice that we define the child controls' parents to be "this", which is this ProjectEditPanel object.
        $this->txtName = new TextBox($this, 'txtProjectName');
        $this->txtName->Text = $objProject->Name;
        $this->txtName->Name = 'Project Name';
        $this->txtName->Required = true;
        $this->txtName->CausesValidation = true;

        // We need to add some Enter and Esc key Events on the Textbox
        $this->txtName->AddAction(new EnterKey(), new AjaxControl($this, 'btnSave_Click'));
        $this->txtName->AddAction(new EnterKey(), new Terminate());
        $this->txtName->AddAction(new EscapeKey(), new AjaxControl($this, 'btnCancel_Click'));
        $this->txtName->AddAction(new EscapeKey(), new Terminate());

        $this->btnSave = new Button($this);
        $this->btnSave->Text = 'Save';
        $this->btnSave->AddAction(new Click(), new AjaxControl($this, 'btnSave_Click'));
        $this->btnSave->CausesValidation = true;

        $this->btnCancel = new Button($this);
        $this->btnCancel->Text = 'Cancel';
        $this->btnCancel->AddAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));
    }

    // Because we don't need any formPreRender() type of functionality, we do not override GetControlHtml()
    // public function getControlHtml() {}

    // Event Handlers Here
    public function btnSave_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        // Go ahead and update the project's name
        $this->objProject->Name = $this->txtName->Text;
        $this->objProject->save();

        // And call the Form's Method CallBack, passing in "true" to state that we've made an update
        $strMethodCallBack = $this->strMethodCallBack;
        $this->objForm->$strMethodCallBack(true);

        Application::displayAlert($strMethodCallBack);
    }

    public function btnCancel_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        // Call the Form's Method CallBack, passing in "false" to state that we've made no changes
        $strMethodCallBack = $this->strMethodCallBack;
        $this->objForm->$strMethodCallBack(false);
    }
}