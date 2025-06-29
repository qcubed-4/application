<?php

use QCubed\Control\ControlBase;
use QCubed\Control\Panel;
use QCubed\Exception\Caller;
use QCubed\Project\Control\TextBox;
use QCubed\Project\Control\Button;
use QCubed\Event\Click;
use QCubed\Action\AjaxControl;

use QCubed\Project\Application;

class ProjectEditPanel extends Panel
{
    protected TextBox $txtProjectName;
    protected Button $btnSave;
    protected Button $btnCancel;
    protected TeamMemberListPanel $pnlTeamMembers;
    public $callbackCloseEdit;

    // Specify the Template File
    protected string $strTemplate = 'panels/ProjectEditPanel.tpl.php';

    public function __construct(ControlBase $objParentObject, string $projectId, string $callbackCloseEdit, ?string $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller $objExc) {
            $objExc->IncrementOffset();
            throw $objExc;
        }

        $this->callbackCloseEdit = $callbackCloseEdit;

        Application::displayAlert('callbackCloseEdit: ' . print_r($callbackCloseEdit, true));;

        Application::displayAlert("ProjectEditPanel: $projectId");

        // Simuleeritud projekti nimi
//        $projectName = "Projekt #$projectId";
//        $this->txtProjectName = new TextBox($this);
//        $this->txtProjectName->Text = $projectName;
//
//        $this->btnSave = new Button($this);
//        $this->btnSave->Text = 'Salvesta';
//        $this->btnSave->AddAction(new Click(), new AjaxControl($this, 'btnSave_Click'));
//
//        $this->btnCancel = new Button($this);
//        $this->btnCancel->Text = 'Tühista';
//        $this->btnCancel->AddAction(new Click(), new AjaxControl($this, 'btnCancel_Click'));
//
//        // Tiimiliikmete list DataGridina (alam-paneel)
//        $this->pnlTeamMembers = new TeamMemberListPanel($this, $projectId);
    }

    protected function btnSave_Click(): void
    {
        // Salvesta muudatused (nt nimi)
        call_user_func($this->callbackCloseEdit, true); // true = salvestatud
    }

    protected function btnCancel_Click(): void
    {
        call_user_func($this->callbackCloseEdit, false);
    }
}
