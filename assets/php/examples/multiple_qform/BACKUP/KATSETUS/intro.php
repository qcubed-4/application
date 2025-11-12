<?php
use QCubed\Action\Ajax;
use QCubed\Control\Panel;
use QCubed\Control\WaitIcon;
use QCubed\Css\PositionType;
use QCubed\Event\Change;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ListBox;
use QCubed\Query\QQ;

use QCubed\Project\Application;

require_once('../qcubed.inc.php');


require('panels/ProjectListPanel.php'); // Vasak paneel: projektide loend
require('panels/ProjectEditPanel.php'); // Parem paneel: projekti detailid JA tiimi liikmed
require('panels/TeamMemberListPanel.php');;// Parem paneeli alam: tiimi liikmed (datagrid)


class DemoForm extends FormBase
{
    protected ?ProjectListPanel $pnlProjectList = null;
    protected ?ProjectEditPanel $pnlProjectEdit = null;

    protected function formCreate(): void
    {
        $this->buildLeftPanel();
    }

    protected function buildLeftPanel(): void
    {
        if (isset($this->pnlProjectList)) {
            $this->removeControl($this->pnlProjectList->ControlId);
        }

        $this->pnlProjectList = new ProjectListPanel($this, 'openRightPanel');
        $this->pnlProjectList->AutoRenderChildren = true;
    }

    public function openRightPanel(int $projectId): void
    {

        Application::displayAlert('Valiti projekt: '.$projectId); // Ei näidata.
        Application::displayAlert('openRightPanel() algas'); // Ei näidata

        // Sulge eelmine, kui on
        if (isset($this->pnlProjectEdit)) {
            $this->removeControl($this->pnlProjectEdit->ControlId);
        }

        // Ava parem paneel (projekt + tiimiliikmed)
        $this->pnlProjectEdit = new ProjectEditPanel(
            $this,
            $projectId,
            fn($saved) => $this->closeRightPanel($saved) // callback sulgemiseks (nt salvestamisel või tühistamisel)
        );

        Application::displayAlert('pnlProjectEdit ID: ' . $this->pnlProjectEdit->ControlId); // Ei näidata
    }

    public function closeRightPanel(bool $saved): void
    {
        if ($this->pnlProjectEdit) {
            $this->removeControl($this->pnlProjectEdit->ControlId);
            $this->pnlProjectEdit = null;
        }
        if ($saved) {
            $this->buildLeftPanel();
        }
    }
}

// Käivita vorm:
DemoForm::Run('DemoForm');

