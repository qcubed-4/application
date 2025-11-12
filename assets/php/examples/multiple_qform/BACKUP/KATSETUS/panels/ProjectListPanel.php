<?php

use QCubed\Control\ControlBase;
use QCubed\Control\Panel;
use QCubed\Exception\Caller;
use QCubed\Project\Control\ListBox;
use QCubed\Event\Change;
use QCubed\Action\AjaxControl;

use QCubed\Project\Application;

class ProjectListPanel extends Panel
{
    protected ListBox $lstProjects;
    protected string $callbackOpenEdit;
    protected string $callbackCloseEdit;

    // Specify the Template File
    protected string $strTemplate = 'panels/ProjectListPanel.tpl.php';

    /**
     * Constructor method for initializing the object.
     *
     * @param mixed $objParentObject The parent object that owns this control.
     * @param string $callbackOpenEdit A callback function name for handling the open edit action.
     * @param string|null $strControlId Optional control ID for uniquely identifying the control.
     *
     * @throws Caller
     */
    public function __construct(mixed $objParentObject, string $callbackOpenEdit, ?string $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        $this->callbackOpenEdit = $callbackOpenEdit;

//        parent::__construct($objParentObject, $strControlId);
//        $this->callbackOpenEdit = $callbackOpenEdit;

        Application::displayAlert('ProjectListPanel: ParentControl='. (is_object($this->ParentControl) ? get_class($this->ParentControl) : 'NULL'));
        Application::displayAlert('WHAT: ParentControl='. (is_object($objParentObject) ? get_class($objParentObject) : 'NULL'));


        $this->lstProjects = new ListBox($this);
        $this->lstProjects->addItem('- Select one -', null, true);
        $this->lstProjects->addItem('Projekt A', 1);
        $this->lstProjects->addItem('Projekt B', 2);
        $this->lstProjects->addItem('Projekt C', 3);

        $this->lstProjects->AddAction(new Change(), new AjaxControl($this, 'lstProjects_Change'));
    }

    protected function lstProjects_Change(): void
    {
        $projectId = $this->lstProjects->SelectedValue;

        Application::displayAlert("ProjectListPanel: $projectId");
        Application::displayAlert(print_r($this->callbackOpenEdit, true));
        // Mõlemad tulemused on ootuspärased: projekti id ja "openRightPanel" on saadavad

        Application::displayAlert('GET-FORM: ' . print_r($this->getForm(), true));

        Application::displayAlert(print_r([
            'ParentControl' => is_object($this->ParentControl) ? get_class($this->ParentControl) : 'NULL',
            'callback' => $this->callbackOpenEdit,
            'exists' => is_object($this->ParentControl) && is_callable([$this->ParentControl, $this->callbackOpenEdit])
        ], true));


        if (is_callable([$this->ParentControl, $this->callbackOpenEdit])) {
            call_user_func([$this->ParentControl, $this->callbackOpenEdit], $projectId);
        }

    }

}
