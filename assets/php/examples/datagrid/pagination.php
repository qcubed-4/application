<?php

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Control\DataGrid;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\Paginator;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{

    // Declare the DataGrid
    protected DataGrid $dtgPersons;

    /**
     * @throws InvalidCast
     * @throws Caller
     */
    protected function formCreate(): void
    {
        //print ('TOTAL ITEMS: ' . Person::countAll());

        // Define the DataGrid
        $this->dtgPersons = new DataGrid($this);

        // Using Ajax for Pagination
        $this->dtgPersons->UseAjax = true;

        // To create pagination, we will create a new paginator and specify the datagrid
        // as the pagination's parent.  (We do this because the datagrid is the control
        // who is responsible for rendering the paginator, as opposed to the form.)
        $objPaginator = new Paginator($this->dtgPersons);
        $this->dtgPersons->Paginator = $objPaginator;

        // Now that the pagination is defined, we can set some additional properties for the data grid.
        // For this example, we will display only 5 elements per a page in the data grid.
        $this->dtgPersons->ItemsPerPage = 5;

        // Define Columns
        $col = $this->dtgPersons->createNodeColumn('Person ID', QQN::person()->Id);
        $col->CellStyler->Width = 100;
        $col = $this->dtgPersons->createNodeColumn('First Name', [QQN::person()->FirstName, QQN::person()->LastName]);
        $col->CellStyler->Width = 200;
        $col = $this->dtgPersons->createNodeColumn('Last Name', [QQN::person()->LastName, QQN::person()->LastName]);
        $col->CellStyler->Width = 200;

        // Let's pre-default the sorting by last name (column index #2)
        $this->dtgPersons->SortColumnIndex = 2;

        // Specify the Datagrid's Data Binder method
        $this->dtgPersons->setDataBinder('dtgPersons_Bind');
    }

    /**
     * @throws Caller
     */
    protected function dtgPersons_Bind(): void
    {
        // We must first let the datagrid know how many total items there are
        // IMPORTANT: Do not pass a limit clause here to CountAll
        $this->dtgPersons->TotalItemCount = Person::countAll();

        //print ('TOTAL ITEMS: ' . Person::countAll());

        // Ask the datagrid for the sorting information for the currently active sort column
        $clauses[] = $this->dtgPersons->OrderByClause;

        // Ask the datagrid for the Limit clause that will limit what portion of the data we will get from the database
        $clauses[] = $this->dtgPersons->LimitClause;

        // Next, we must be sure to load the data source, passing in the datagrid's
        // limit info into our load all methods.
        $this->dtgPersons->DataSource = Person::loadAll($clauses);
    }

}

ExampleForm::run('ExampleForm');
