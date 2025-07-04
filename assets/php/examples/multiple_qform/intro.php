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

require_once('../qcubed.inc.php');

// We need to bring in the custom Panels we've created
require('PersonEditPanel.class.php');
require('ProjectViewPanel.class.php');
require('ProjectEditPanel.class.php');

// Define the \QCubed\Project\Control\FormBase with all our Controls
class ExamplesForm extends FormBase
{
    // Local declarations of our Controls
    protected ListBox $lstProjects;
    protected Panel $pnlLeft;
    protected Panel $pnlRight;

    // Initialize our Controls during the Form Creation process

    /**
     * Initializes the form components, including dropdown control and panels.
     *
     * Sets up a dropdown menu with project names loaded from the database,
     * along with event handling for selection changes. Configures left and
     * right panel placeholders with specified properties.
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    protected function formCreate(): void
    {
        // Set up the Dropdown of Project Names
        $this->lstProjects = new ListBox($this);
        $this->lstProjects->addItem('- Select One -', null, true);
        foreach (Project::loadAll([QQ::orderBy(QQN::project()->Name)]) as $objProject) {
            $this->lstProjects->addItem($objProject->Name, $objProject->Id);
        }
        $this->lstProjects->addAction(new Change(), new Ajax('lstProjects_Change'));

        // Set up our Left and Right Panel Placeholders
        // Notice that both panels have "AutoRenderChildren" set to true so that
        // instantiated child panels will automatically get displayed
        $this->pnlLeft = new Panel($this);
        $this->pnlLeft->Position = PositionType::RELATIVE;
        $this->pnlLeft->CssClass = 'panelDefault';
        $this->pnlLeft->AutoRenderChildren = true;

        $this->pnlRight = new Panel($this);
        $this->pnlRight->Position = PositionType::RELATIVE;
        $this->pnlRight->CssClass = 'panelDefault panelRight';
        $this->pnlRight->AutoRenderChildren = true;

        $this->objDefaultWaitIcon = new WaitIcon($this);
    }

    // The "btnButton_Click" Event handler

    /**
     * Handles the change event for the project dropdown list. This method clears existing panels
     * and initializes a new view panel for the selected project.
     *
     * @param string $strFormId The ID of the form that triggered the change event.
     * @param string $strControlId The ID of the control that triggered the change event.
     * @param string $strParameter Additional parameters passed during the event.
     *
     * @return void
     * @throws Caller
     */
    protected function lstProjects_Change(string $strFormId, string $strControlId, string $strParameter): void
    {
        // First, remove all children panels from both pnlLeft and pnlRight
        $this->pnlLeft->removeChildControls(true);
        $this->pnlRight->removeChildControls(true);

        // Now, we create a new ProjectViewPanel and set its parent to pnlLeft
        if ($intProjectId = $this->lstProjects->SelectedValue) {
            $pnlProjectView = new ProjectViewPanel($this->pnlLeft, Project::load($intProjectId),
                $this->pnlRight->ControlId);
        }
    }

    // Method Call back for any of the RightPanel panels (see note in ProjectViewPanel for more information)

    /**
     * Closes the right panel by removing all child controls and optionally updates the left panel
     * based on changes made.
     *
     * @param bool $blnUpdatesMade Indicates whether updates were made, requiring the left panel to be redrawn.
     * @return void
     * @throws Caller
     */
    public function closeRightPanel(bool $blnUpdatesMade): void
    {
        // First, remove all children panels from both pnlRight
        $this->pnlRight->removeChildControls(true);

        // If Updates were Made, then Re-Draw Left Panel to reflect the changes
        // Note that this is a "brute force" method to update the entire left panel
        // Of course, if you want, you can more finely tune this update process by only updating specific
        // controls, etc., depending on what was updated/changed.
        if ($blnUpdatesMade) {
            $this->pnlLeft->removeChildControls(true);
            if ($intProjectId = $this->lstProjects->SelectedValue) {
                $pnlProjectView = new ProjectViewPanel($this->pnlLeft, Project::load($intProjectId),
                    $this->pnlRight->ControlId);
            }
        }
    }

}

// Run the Form we have defined
ExamplesForm::run('ExamplesForm');
