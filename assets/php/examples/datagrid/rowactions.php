<?php
use QCubed\Action\Ajax;
use QCubed\Event\CellClick;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Project\Control\DataGrid;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{

    // Declare the DataGrid
    protected object $dtgPersons;

    /**
     * @throws InvalidCast
     * @throws Caller
     */
    protected function formCreate(): void
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

        // Add an action that will detect a click on the row and return the HTML data value that was created by RowParamsCallback
        $this->dtgPersons->addAction(new CellClick(0, null, CellClick::rowDataValue('value')),
            new Ajax('dtgPersonsRow_Click'));
    }

    // DisplayFullName will be called by the DataGrid on each row whenever it tries to render
    // the Full Name column.  Note that we take in the $objPerson as a Person parameter. Also
    // note that DisplayFullName is a PUBLIC function -- because it will be called by the \QCubed\Project\Control\DataGrid class.
    public function displayFullName(Person $objPerson): string
    {
        return sprintf('%s, %s', $objPerson->LastName, $objPerson->FirstName);
    }

    /**
     * @throws Caller
     */
    protected function dtgPersons_Bind(): void
    {
        // We must be sure to load the data source
        $this->dtgPersons->DataSource = Person::loadAll();
    }

    /**
     * Generates and returns an associative array of parameters for a row in the data grid.
     *
     * @param object $objRowObject The object representing the data for the current row.
     * @param int $intRowIndex The index of the current row in the data grid.
     * @return array An associative array of parameters for the row, such as data attributes.
     */
    public function dtgPersons_GetRowParams(object $objRowObject, int $intRowIndex): array
    {
        $strKey = $objRowObject->primaryKey();
        $params['data-value'] = $strKey;
        return $params;
    }

    /**
     * Handles the event when a row in the person's data grid is clicked.
     *
     * @param string $strFormId The Form ID that triggered the event.
     * @param string $strControlId The Control ID that triggered the event.
     * @param string $strParameter A parameter, typically containing the ID of the person associated with the clicked row.
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function dtgPersonsRow_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        $intPersonId = intval($strParameter);

        $objPerson = Person::load($intPersonId);

        Application::displayAlert("You clicked on a person with ID #" . $intPersonId .
            ": " . $objPerson->FirstName . " " . $objPerson->LastName);
    }
}

ExampleForm::run('ExampleForm');
