<?php
/*
Here is the Child \QCubed\Project\Control\DataGrid...
*/

// Load the QCubed Development Framework
use QCubed\Control\Panel;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Control\DataGrid;
use QCubed\Project\Control\Paginator;

require_once('../../qcubed.inc.php');

class RecordsSummary extends Panel
{
    public DataGrid $dtgRecordsSummary;

    protected mixed $objParentObject;

    // Protected Objects
    protected Project $objProject;

    // in the contractor pass the item bounded too just for another process

    /**
     * Constructor for setting up the record summary control.
     *
     * @param mixed $objParentObject The parent object which contains this control.
     * @param Project $objProject The project object associated with this control.
     * @param string|null $strControlId Optional control ID to uniquely identify this control.
     * @throws Caller
     * @throws InvalidCast
     */
    public function __construct(mixed $objParentObject, Project $objProject, ?string $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);

            // Watch out for a template later gonna talk about it,
            // need a trick to look good
            // (insert the child content as row in a table already present for Master
            //   close columns - insert row - insert child - close row - open column
            //  </td> <tr><td> render content of this child </td> </tr> <td> )
            $this->Template = 'records.summary.tpl.php';

            // Setting local the Muster \QCubed\Project\Control\DataGrid to refresh on
            // Saves on the Child DataGrid.
            $this->objParentObject = $objParentObject;
            $this->objProject = $objProject;

            // Create the child DataGrid as a normal \QCubed\Project\Control\DataGrid
            $this->dtgRecordsSummary = new DataGrid($this);
            // pagination
            $this->dtgRecordsSummary->Paginator = new Paginator($this->dtgRecordsSummary);

            $this->dtgRecordsSummary->ItemsPerPage = 5;

            $this->dtgRecordsSummary->setDataBinder('dtgRecordsSummary_Bind', $this);


            // Add some data to show...
            $this->dtgRecordsSummary->createCallableColumn('Person', [$this, 'render_PersonColumn']);
            $col = $this->dtgRecordsSummary->createNodeColumn('Id', QQN::person()->Id);
            $col->CellStyler->Width = 120;

        } catch (Caller $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
    }

    public function render_PersonColumn(Person $objPerson): string
    {
        return $objPerson->FirstName . ' ' . $objPerson->LastName;
    }

    public function dtgRecordsSummary_Bind()
    {
        //$objConditions = $this->dtgRecordsSummary->Conditions;

        // setup $objClauses array
        $objClauses = array();

        // add OrderByClause to the $objClauses array
        // if ($objClause = $this->dtgRecordsSummary->OrderByClause){
        if ($objClause = $this->dtgRecordsSummary->OrderByClause) {
            $objClauses[] = $objClause;
        }

        // add LimitByClause to the $objClauses array
        //if ($objClause = $this->dtgRecordsSummary->LimitClause)
        if ($objClause = $this->dtgRecordsSummary->LimitClause) {
            $objClauses[] = $objClause;
        }


        $this->dtgRecordsSummary->TotalItemCount = $this->objProject->countPeopleAsTeamMember();

        $this->dtgRecordsSummary->DataSource = $this->objProject->getPersonAsTeamMemberArray($objClauses);

    }

}


