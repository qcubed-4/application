<?php
use QCubed\Action\Ajax;
use QCubed\Event\CellClick;
use QCubed\Project\Application;
use QCubed\Project\Control\DataGrid;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{

    // Declare the DataGrid
    protected $dtgPersons;

    protected function formCreate()
    {
        // Define the DataGrid
        $this->dtgPersons = new DataGrid($this, 'dtgPersons');

        // Style this with a QCubed built-in style that will highlight the row hovered over.
        $this->dtgPersons->addCssClass('clickable-rows');

        // Define Columns
        $this->dtgPersons->createNodeColumn('First Name', QQN::person()->FirstName);
        $this->dtgPersons->createNodeColumn('Last Name', QQN::person()->LastName);

        // Specify the Datagrid's Data Binder method
        $this->dtgPersons->setDataBinder('dtgPersons_Bind');

        // Attach a callback to the table that will create an attribute in the row's tr tag that will be the id of data row in the database
        $this->dtgPersons->RowParamsCallback = [$this, 'dtgPersons_GetRowParams'];

        // Add an action that will detect a click on the row, and return the html data value that was created by RowParamsCallback
        $this->dtgPersons->addAction(new CellClick(0, null, CellClick::rowDataValue('value')),
            new Ajax('dtgPersonsRow_Click'));
    }

    // DisplayFullName will be called by the DataGrid on each row, whenever it tries to render
    // the Full Name column.  Note that we take in the $objPerson as a Person parameter.  Also
    // note that DisplayFullName is a PUBLIC function -- because it will be called by the \QCubed\Project\Control\DataGrid class.
    public function displayFullName(Person $objPerson)
    {
        $strToReturn = sprintf('%s, %s', $objPerson->LastName, $objPerson->FirstName);
        return $strToReturn;
    }

    protected function dtgPersons_Bind()
    {
        // We must be sure to load the data source
        $this->dtgPersons->DataSource = Person::loadAll();
    }

    public function dtgPersons_GetRowParams($objRowObject, $intRowIndex)
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }


    public function dtgPersonsRow_Click($strFormId, $strControlId, $strParameter)
    {
        $intPersonId = intval($strParameter);

        $objPerson = Person::load($intPersonId);

        Application::displayAlert("You clicked on a person with ID #" . $intPersonId .
            ": " . $objPerson->FirstName . " " . $objPerson->LastName);
    }
}

ExampleForm::run('ExampleForm');
