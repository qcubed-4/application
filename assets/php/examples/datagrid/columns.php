<?php
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\Table;
use QCubed\Table\CallableColumn;
use QCubed\Table\IndexedColumn;

require_once('../qcubed.inc.php');

class ComplexColumn extends IndexedColumn
{
    public function renderHeaderCell()
    {
        if ($this->objParentTable->CurrentHeaderRowIndex == 0 &&
            $this->Index > 1
        ) {
            return null; // don't draw, first col is a span
        } else {
            return parent::renderHeaderCell();
        }
    }

    public function fetchHeaderCellValue()
    {
        if ($this->objParentTable->CurrentHeaderRowIndex == 0 &&
            $this->Index == 1
        ) {
            return 'Year';
        }
        return parent::fetchHeaderCellValue();
    }


    public function getHeaderCellParams()
    {
        $a = parent::getHeaderCellParams();
        if ($this->Index == 0) {
            //make background white
            $a['style'] = 'background-color: white';
        }
        if ($this->ParentTable->CurrentHeaderRowIndex == 0) {
            if ($this->Index == 1) {
                $a['colspan'] = 3;
            }
        }
        return $a;
    }
}


class ExampleForm extends FormBase
{

    /** @var Table */
    protected $tblPersons;

    /** @var Table */
    protected $tblReport;

    /** @var Table */
    protected $tblComplex;

    protected function formCreate()
    {
        // Define the DataGrid
        $this->tblPersons = new Table($this);
        $this->tblPersons->CssClass = 'simple_table';
        $this->tblPersons->RowCssClass = 'odd_row';
        $this->tblPersons->AlternateRowCssClass = 'even_row';
        $this->tblPersons->HeaderRowCssClass = 'header_row';

        // Define Columns
        // This demonstrates how to first create a column, and then add it to the table
        $objColumn = new CallableColumn('Full Name', [$this, 'getFullName']);
        $this->tblPersons->addColumn($objColumn);

        // The second column demonstrates using a property name for fetching the data
        // This also demonstrates how to create a column and add it to the table all at once, using the CreatePropertyColumn shortcut
        $this->tblPersons->createPropertyColumn('First Name', 'FirstName');

        // The second column demonstrates using a node column for fetching the data
        $this->tblPersons->createNodeColumn('Last Name', QQN::person()->LastName);

        // Specify the local Method which will actually bind the data source to the datagrid.
        // In order to not over-bloat the form state, the datagrid will use the data source only when rendering itself,
        // and then it will proceed to remove the data source from memory.  Because of this, you will need to define
        // a "data binding" method which will set the datagrid's data source.  You specify the name of the method
        // here.  The framework will be responsible for calling your data binding method whenever the datagrid wants
        // to render itself.
        $this->tblPersons->setDataBinder('tblPersons_Bind');

        $this->tblReport = new Table($this);
        $this->tblReport->CssClass = 'simple_table';
        $this->tblReport->RowCssClass = 'odd_row';
        $this->tblReport->AlternateRowCssClass = 'even_row';
        $this->tblReport->HeaderRowCssClass = 'header_row';

        // "named" index columns
        $this->tblReport->createIndexedColumn("Year", 0);
        $this->tblReport->createIndexedColumn("Model", 1);
        // "unnamed" index columns
        $this->tblReport->createIndexedColumn();
        $this->tblReport->createIndexedColumn();
        // index columns for associative arrays
        $this->tblReport->createIndexedColumn("Count", "#count");

        $this->tblReport->setDataBinder('tblReport_Bind');

        $this->tblComplex = new Table($this);
        $this->tblComplex->CssClass = 'simple_table';
        $this->tblComplex->RowCssClass = 'odd_row';
        $this->tblComplex->AlternateRowCssClass = 'even_row';
        $this->tblComplex->HeaderRowCssClass = 'header_row';

        // "named" index columns
        $col = $this->tblComplex->addColumn(new ComplexColumn("", "Name"));
        $col->RenderAsHeader = true;
        $this->tblComplex->addColumn(new ComplexColumn("2000", 1));
        $this->tblComplex->addColumn(new ComplexColumn("2001", 2));
        $this->tblComplex->addColumn(new ComplexColumn("2002", 3));
        $this->tblComplex->HeaderRowCount = 2;

        $this->tblComplex->setDataBinder('tblComplex_Bind');
    }

    protected function tblPersons_Bind()
    {
        // We load the data source, and set it to the datagrid's DataSource parameter
        $this->tblPersons->DataSource = Person::loadAll();
    }

    public function getFullName($item)
    {
        return 'Full Name is "' . $item->FirstName . ' ' . $item->LastName . '"';
    }

    protected function tblReport_Bind()
    {
        // build the entire datasource as an array of arrays.
        $csv = '1997,Ford,E350,"ac, abs, moon",3000.00
1999,Chevy,"Venture ""Extended Edition""","",4900.00
1999,Chevy,"Venture ""Extended Edition, Very Large""","",5000.00
1996,Jeep,Grand Cherokee,"MUST SELL!';
        $data = str_getcsv($csv, "\n");
        foreach ($data as &$row) {
            $row = str_getcsv($row, ",");
            $row["#count"] = count($row);
        }
        $this->tblReport->DataSource = $data;
    }

    protected function tblComplex_Bind()
    {
        $a[] = array('Name' => 'Income', 1 => 1000, 2 => 2000, 3 => 1500);
        $a[] = array('Name' => 'Expense', 1 => 500, 2 => 700, 3 => 2100);
        $a[] = array('Name' => 'Net', 1 => 1000 - 500, 2 => 2000 - 700, 3 => 1500 - 2100);

        $this->tblComplex->DataSource = $a;
    }

}

ExampleForm::run('ExampleForm');
