<?php
use QCubed\Action\Ajax;
use QCubed\Event\Click;
use QCubed\Project\Application;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\DataGrid;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{
    // Declare the DataGrid
    protected $dtgPersons;
    protected $dtgPersonsDelegated;

    protected function formCreate()
    {
        // Define the DataGrid
        $this->dtgPersons = new DataGrid($this, 'dtgPersons');
        $this->dtgPersons->Height = "560px";


        // Define the DataGrid using event delegation
        $this->dtgPersonsDelegated = new DataGrid($this, 'dtgPersonsDelegated');

        // Define Columns
        $this->dtgPersons->createNodeColumn('Person Id', QQN::person()->Id);
        $this->dtgPersons->createNodeColumn('First Name', QQN::person()->FirstName);
        $this->dtgPersons->createNodeColumn('Last Name', QQN::person()->LastName);
        $col = $this->dtgPersons->createCallableColumn('', [$this, 'RenderDeleteButton']);
        $col->HtmlEntities = false;

        $this->dtgPersonsDelegated->createNodeColumn('Person Id', QQN::person()->Id);
        $this->dtgPersonsDelegated->createNodeColumn('First Name', QQN::person()->FirstName);
        $this->dtgPersonsDelegated->createNodeColumn('Last Name', QQN::person()->LastName);
        $col = $this->dtgPersonsDelegated->createCallableColumn('', [$this, 'RenderDeleteButton2']);
        $col->HtmlEntities = false;

        // Create the delegated event action. We bind the event to the data grid, even though the event is
        // coming from buttons in the datagrid. These click events will bubble up to the table.
        $this->dtgPersonsDelegated->addAction(
        // The 3rd parameter is the jQuery selector that controls which controls we are listening to. This is similar to a CSS selector.
        // In our example, we are listening to buttons that have a 'data-id' attribute.
            new Click(null, 0, 'button[data-id]'),
            // Respond to click events with an ajax action. The fourth parameter is a JavaScript fragment that controls what
            // the action paremeter will be. In this case, its the value of the data-id attribute. Note that the "event.target" member
            // of the event is the button that was clicked on. Also, we are sending in the record id as the action parameter, so we can
            // use the same dtgPersonsButton_Click for the delegated and non-delegated actions.
            new Ajax('dtgPersonsButton_Click', null, null, '$j(event.target).data("id")')
        );

        // Specify the Datagrid's Data Binder method
        // Notice we are using the same binder for two datagrids
        $this->dtgPersons->setDataBinder('dtgPersons_Bind');
        $this->dtgPersonsDelegated->setDataBinder('dtgPersons_Bind');
    }


    /**
     * Bind the data to the data source. Note that the first parameter is the control we are binding to. This allows
     * us to use the same data binder for multiple controls.
     */
    protected function dtgPersons_Bind($objControl)
    {
        // Use the control passed in to the data binder to know to which to send the data.
        $objControl->DataSource = Person::loadAll();
    }

    /**
     * Respond to the button click for the non-delegated events.
     */
    public function dtgPersonsButton_Click($strFormId, $strControlId, $strParameter)
    {
        $intPersonId = intval($strParameter);

        $objPerson = Person::load($intPersonId);
        Application::displayAlert("You clicked on a person with ID #{$intPersonId}: {$objPerson->FirstName} {$objPerson->LastName}");
    }

    /**
     * A non-delegated event version. Create a new button for each control and attach an action to it.
     *
     * @param Person $objPerson
     * @return String
     */
    public function renderDeleteButton($objPerson)
    {
        $strControlId = 'btn' . $objPerson->Id;
        $objControl = $this->getControl($strControlId);
        if (!$objControl) {
            $objControl = new Button($this);
            $objControl->Text = 'Edit';
            $objControl->ActionParameter = $objPerson->Id;
            $objControl->addAction(new Click(),
                new Ajax('dtgPersonsButton_Click')); // This will generate a javascript call for every button created.
        }
        return $objControl->render(false);
    }

    /**
     * The delegated button. We are directly creating the html for the button and assigning a data-id that corresponds to the action
     * parameter we will eventually send in to the action handler.
     *
     * @param $objPerson
     * @return string
     */
    public function renderDeleteButton2($objPerson)
    {
        //create the delete button row, with a special naming scheme for the button ids: "delete_" . id (where id is a person id)
        return '<button data-id="' . $objPerson->Id . '">Edit</button>';
    }
}


ExampleForm::run('ExampleForm');

