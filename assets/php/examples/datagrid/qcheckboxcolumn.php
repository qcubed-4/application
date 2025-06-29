<?php
use QCubed\Action\Ajax;
use QCubed\Control\Label;
use QCubed\Event\CheckboxColumnClick;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Project\Control\Checkbox;
use QCubed\Project\Control\DataGrid;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\Paginator;
use QCubed\QString;
use QCubed\Query\QQ;
use QCubed\Table\DataGridCheckboxColumn;

require_once('../qcubed.inc.php');

class ExampleCheckColumn1 extends DataGridCheckboxColumn {
    protected function getAllIds(): ?array
    {
        return Person::queryPrimaryKeys();
    }
}


class ExampleCheckColumn2 extends DataGridCheckboxColumn {
    protected function getItemCheckedState($item): bool
    {
        if(null !== $item->getVirtualAttribute('assn_item')) {
            return true;
        }
        else {
            return false;
        }
    }

    public function setItemCheckedState(int|string $itemId, bool $blnChecked): void
    {
        $objProject = Project::load($itemId);
        if($blnChecked)
        {
            // Simulate an associating with the project
            Application::displayAlert('Associating '.$objProject->Name);

            // To actually do the association, we would execute the following:
            /*
            $objParentProject = Project::load(1);	// We were associating the ACME project
            $objParentProject->associateProjectAsRelated($objProject);
             */
        }
        else
        {
            // Simulate un associating the Project
            Application::displayAlert('Un associating '.$objProject->Name);
        }
    }
}



class ExampleForm extends FormBase
{
    // Declare the DataGrid and Response Label
    protected object $dtgPersons;
    protected object $lblResponse;

    /** @var  DataGridCheckboxColumn */
    protected DataGridCheckboxColumn $colSelect;

    protected object $dtgProjects;
    protected object $colProjectSelected;

    /**
     * @throws Caller
     */
    protected function formCreate(): void
    {
        $this->dtgPersons_Create();
        $this->dtgProjects_Create();

        // Define the Label -- keep it blank for now
        $this->lblResponse = new Label($this);
        $this->lblResponse->HtmlEntities = false;
    }

    /**
     * @throws InvalidCast
     * @throws Caller
     */
    protected function dtgPersons_Create(): void
    {
        // Define the DataGrid
        $this->dtgPersons = new DataGrid($this);

        // Specify Pagination with 10 items per page
        $objPaginator = new Paginator($this->dtgPersons);
        $this->dtgPersons->Paginator = $objPaginator;
        $this->dtgPersons->ItemsPerPage = 10;

        // Define Columns
        $col = $this->dtgPersons->createNodeColumn('Person ID', QQN::person()->Id);
        $col->CellStyler->Width = 100;
        $col = $this->dtgPersons->createNodeColumn('First Name', [QQN::person()->FirstName, QQN::person()->LastName]);
        $col->CellStyler->Width = 200;
        $col = $this->dtgPersons->createNodeColumn('Last Name', [QQN::person()->LastName, QQN::person()->LastName]);
        $col->CellStyler->Width = 200;

        //Create the select column, a subclass of \QCubed\Table\DataGridCheckboxColumn
        $this->colSelect = new ExampleCheckColumn1('');
        $this->colSelect->ShowCheckAll = true;
        $this->colSelect->CellStyler->Width = 20;

        $this->dtgPersons->addColumnAt(0, $this->colSelect);

        // Let's pre-default the sorting by last name (column index #2)
        $this->dtgPersons->SortColumnIndex = 2;

        // Specify the DataBinder method for the DataGrid
        $this->dtgPersons->setDataBinder('dtgPersons_Bind');

        $this->dtgPersons->addAction(new CheckboxColumnClick(), new Ajax ('chkSelected_Click'));

        // Make sure changes to the database by other users are reflected in the datagrid on the next event
        $this->dtgPersons->watch(QQN::person());

    }

    /**
     * @throws Caller
     */
protected function dtgPersons_Bind(): void
    {
        // Let the datagrid know how many total items and then get the data source
        $this->dtgPersons->TotalItemCount = Person::countAll();
        $this->dtgPersons->DataSource = Person::loadAll(QQ::clause(
            $this->dtgPersons->OrderByClause,
            $this->dtgPersons->LimitClause
        ));
    }

    // This btnCopy_Click action will actually perform the copy of the person row being copied

    /**
     * Event handler for when the checkbox is clicked. It identifies the person or action
     * associated with the checkbox, updates the current selection, and provides feedback
     * by listing the selected items.
     *
     * @param string $strFormId The ID of the form that triggered the event.
     * @param string $strControlId The ID of the control that triggered the event.
     * @param array $params Parameters associated with the event, including 'checked' status
     * and the control ID.
     * @return void This method does not return a value but updates the response text.
     * @throws Caller
     * @throws InvalidCast
     */
    protected function chkSelected_Click(string $strFormId, string $strControlId, array $params): void
    {
        $blnChecked = $params['checked'];

        // The database record primary key is embedded after the last underscore in the id of the checkbox
        $idItems = explode('_', $params['id']);
        $intPersonId = end($idItems);

        if ($intPersonId == 'all') {
            $strResponse = QString::htmlEntities('You just selected all. ');
        }
        else {
            $objPerson = Person::load($intPersonId);

            // Let's respond to the user what just happened
            if ($blnChecked)
                $strResponse = QString::htmlEntities('You just selected ' . $objPerson->FirstName . ' ' . $objPerson->LastName . '.');
            else
                $strResponse = QString::htmlEntities('You just deselected ' . $objPerson->FirstName . ' ' . $objPerson->LastName . '.');
            $strResponse .= '<br/>';
        }
        // Let's get the selected person

        // Now, let's go through all the checkboxes and list everyone who has been selected
        $arrIds = $this->colSelect->getCheckedItemIds();
        $strNameArray = array();
        foreach($arrIds as $strId) {
            $objPerson = Person::load($strId);
            $strName = QString::htmlEntities($objPerson->FirstName . ' ' . $objPerson->LastName);
            $strNameArray[] = $strName;
        }

        $strResponse .= 'The list of people who are currently selected: ' . implode(', ', $strNameArray);

        // Provide feedback to the user by updating the Response label
        $this->lblResponse->Text = $strResponse;
    }

    /**
     * @throws InvalidCast
     * @throws Caller
     */
    protected function dtgProjects_Create(): void
    {
        // Setup DataGrid
        $this->dtgProjects = new DataGrid($this);
        $this->dtgProjects->CssClass = 'datagrid';

        // Datagrid Paginator
        $this->dtgProjects->Paginator = new Paginator($this->dtgProjects);

        // If desired, use this to set the numbers of items to show per a page
        //$this->lstProjectsAsRelated->ItemsPerPage = 20;

        // Specify Whether or Not to Refresh using Ajax
        $this->dtgProjects->UseAjax = true;

        // Specify the local databind method this datagrid will use
        $this->dtgProjects->setDataBinder('dtgProjects_Bind'); // , $this

        // Setup DataGridColumns
        $this->colProjectSelected = new ExampleCheckColumn2(t('Select'));

        $this->dtgProjects->addColumn($this->colProjectSelected);

        $this->dtgProjects->createNodeColumn(t('Name'), QQN::project()->Name);

        // Make sure changes to the database by other users are reflected in the datagrid on the next event
        $this->dtgProjects->watch(QQN::project());
    }


    /**
     * This method is triggered when the "Project Selected" checkbox is created.
     * It initializes the checkbox state based on the relationship of the given project.
     *
     * @param Project $_ITEM The project item to evaluate for association.
     * @param Checkbox $ctl The checkbox control to modify.
     * @return void
     */
    public function colProjectSelectedCheckbox_Created(Project $_ITEM, Checkbox $ctl): void
    {
        //If it's related to ACME, start it off checked
        if(null !== $_ITEM->getVirtualAttribute('assn_item'))
            $ctl->Checked = true;
        //You could perform an IsProjectAsRelatedAssociated call here instead, but
        //that would cause a database hit
    }

    /**
     * @throws Caller
     */
    public function dtgProjects_Bind(): void
    {
        // Get Total Count b/c of Pagination
        $this->dtgProjects->TotalItemCount = Project::countAll();

        $objClauses = array();
        if ($objClause = $this->dtgProjects->OrderByClause)
            $objClauses[] = $objClause;
        if ($objClause = $this->dtgProjects->LimitClause)
            $objClauses[] = $objClause;

        // Create a virtual attribute that lets us know if this Project is related to ACME
        $objClauses[] = QQ::expand(
            QQ::virtual('assn_item',
                QQ::subSql(
                    'select 
                        project_id
                    from 
                        related_project_assn
                    where 
                        child_project_id = {1} 
                         and project_id = 1',
                        QQN::project()->Id)
            )
        );

        $this->dtgProjects->DataSource = Project::loadAll($objClauses);
    }
}

ExampleForm::run('ExampleForm');

