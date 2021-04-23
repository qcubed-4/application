<?php
use QCubed\Project\Control\DataGrid;
use QCubed\Project\Control\FormBase;
use QCubed\Query\QQ;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{

    // Declare the DataGrid
    protected $dtgPersons;

    protected function formCreate()
    {
        // Define the DataGrid
        $this->dtgPersons = new DataGrid($this);

        // Define Columns
        // Note how we add SortByCommand and ReverseSortByCommand properties to each column
        $col = $this->dtgPersons->createPropertyColumn('Person Id', 'Id');
        $col->OrderByClause = QQ::orderBy(QQN::person()->Id);    // The clause to use when the column header is first clicked.
        $col->ReverseOrderByClause = QQ::orderBy(QQN::person()->Id,
            false); // The clause to use the second time the column header is clicked.
        // Note above the use of 'false' in the node list of the OrderBy clause. This tells OrderBy to go in descending order for the previous database column.


        // Here we illustrate how you can sort on multiple columns by specifying multiple nodes in the OrderBy clause
        $col = $this->dtgPersons->createPropertyColumn('First Name', 'FirstName');
        $col->OrderByClause = QQ::orderBy(QQN::person()->FirstName,
            QQN::person()->LastName);    // The clause to use when the column header is first clicked.
        $col->ReverseOrderByClause = QQ::orderBy(QQN::person()->FirstName, false, QQN::person()->LastName,
            false); // The clause to use the second time the column header is clicked.

        // Here we save some typing by using a NodeColumn. Node columns use the nodes to both display the data, and sort on the data.
        // Notice that we are passing an array of nodes here. The first node is used for display, and the entire list of nodes is
        // used for sorting.
        $this->dtgPersons->createNodeColumn('Last Name', [QQN::person()->LastName, QQN::person()->FirstName]);


        // Let's pre-default the sorting by last name (column index #2)
        $this->dtgPersons->SortColumnIndex = 2;

        // Specify the Datagrid2's Data Binder method
        $this->dtgPersons->setDataBinder('dtgPersons_Bind');
    }

    protected function dtgPersons_Bind()
    {
        // We must be sure to load the data source

        // Ask the datagrid for the sorting clause that corresponds to the currently active sort column.
        $clauses[] = $this->dtgPersons->OrderByClause;

        // Give that clause to our sql query so it returns sorted data
        $this->dtgPersons->DataSource = Person::loadAll($clauses);
    }
}

ExampleForm::run('ExampleForm');
