<?php
use QCubed\Action\Ajax;
use QCubed\Action\Terminate;
use QCubed\Event\Click;
use QCubed\Event\EnterKey;
use QCubed\Event\EscapeKey;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

if (!defined('QCUBED_DESIGN_MODE')) {
    define('QCUBED_DESIGN_MODE', 1);
} // normally, you would define this in your config file

// Define the \QCubed\Project\Control\FormBase with all our Qcontrols
class ExamplesForm extends FormBase
{

    // Local declarations of our Qcontrols
    //protected $lblFirstName;
    protected $txtFirstName;
    //protected $lblLastName;
    protected $txtLastName;
    protected $lstPersonTypes;

    protected $btnSave;
    protected $btnCancel;
    // Local instance of a Person ModelConnectors
    protected $mctPerson;

    // Initialize our Controls during the Form Creation process
    protected function formCreate()
    {
        // For now, let's load Person of ID #1
        // Remember that $this is the Model Connector's parent, because every QControl
        // we get from PersonConnector should have $this as its parent.
        $this->mctPerson = PersonConnector::create($this, 1);

        // Instead of manually defining and setting up each \QCubed\Control\Label and \QCubed\Project\Control\TextBox,
        // we utilize the ModelConnector's _create() functionality to create them
        // for us.
        //$this->lblFirstName = $this->mctPerson->lblFirstName_Create();
        //$this->lblLastName = $this->mctPerson->lblLastName_Create();

        $this->txtFirstName = $this->mctPerson->txtFirstName_Create();
        $this->txtLastName = $this->mctPerson->txtLastName_Create();
        $this->lstPersonTypes = $this->mctPerson->lstPersonTypes_Create();

        // We can of course also define any additional controls we wish
        $this->btnSave = new Button($this);
        $this->btnSave->Text = 'Save';
        $this->btnSave->Visible = false;
        $this->btnCancel = new Button($this);
        $this->btnCancel->Text = 'Cancel';
        $this->btnCancel->Visible = false;

        // Finally, we can define all of our actions
        // ON some of these, we can override and set a CausesValidation handler
        $this->btnSave->addAction(new Click(), new Ajax('btnSave_Click', 'default', true));
        $this->btnCancel->addAction(new Click(), new Ajax('btnCancel_Click'));

        //$this->lblFirstName->addAction(new \QCubed\Event\Click(), new \QCubed\Action\Ajax('lblFirstName_Click'));
        //$this->lblLastName->addAction(new \QCubed\Event\Click(), new \QCubed\Action\Ajax('lblLastName_Click'));

        $this->txtFirstName->addAction(new EnterKey(), new Ajax('btnSave_Click', 'default', true));
        $this->txtFirstName->addAction(new EnterKey(), new Terminate());
        $this->txtFirstName->addAction(new EscapeKey(), new Ajax('btnCancel_Click', 'default', true));
        $this->txtFirstName->addAction(new EscapeKey(), new Terminate());

        $this->txtLastName->addAction(new EnterKey(), new Ajax('btnSave_Click', 'default', true));
        $this->txtLastName->addAction(new EnterKey(), new Terminate());
        $this->txtLastName->addAction(new EscapeKey(), new Ajax('btnCancel_Click', 'default', true));
        $this->txtLastName->addAction(new EscapeKey(), new Terminate());
    }

    // Define the Event Handlers
    protected function btnSave_Click($strFormId, $strControlId, $strParameter)
    {
        // Utilize Meta Control to update Person
        $this->mctPerson->savePerson();

        // Unselect everything
        $this->unselect();
    }

    protected function unselect()
    {
        // Let's hide all the textboxes and show all the labels
        $this->txtFirstName->Visible = false;
        //$this->lblFirstName->Visible = true;
        $this->txtLastName->Visible = false;
        //$this->lblLastName->Visible = true;

        // Let's hide the Save and Cancel Buttons
        $this->btnSave->Visible = false;
        $this->btnCancel->Visible = false;

        // Finally, let's utilize the ModelConnector to refresh all the data fields (in case a data was modified and saved
        // or a textbox was modified and NOT saved)
        $this->mctPerson->refresh();
    }

    protected function btnCancel_Click($strFormId, $strControlId, $strParameter)
    {
        $this->unselect();
    }

    protected function lblFirstName_Click($strFormId, $strControlId, $strParameter)
    {
        // In case we are currently Editing lblLastName, let's first implicitly unselect everything
        $this->unselect();

        // Hide the Label and Show the Textboox
        $this->lblFirstName->Visible = false;
        $this->txtFirstName->Visible = true;
        $this->txtFirstName->focus();

        // Finall, show the Save and Cancel Buttons
        $this->btnSave->Visible = true;
        $this->btnCancel->Visible = true;
    }

    protected function lblLastName_Click($strFormId, $strControlId, $strParameter)
    {
        // In case we are currently Editing lblFirstName, let's first implicitly unselect everything
        $this->unselect();

        // Hide the Label and Show the Textboox
        $this->lblLastName->Visible = false;
        $this->txtLastName->Visible = true;
        $this->txtLastName->focus();

        // Finall, show the Save and Cancel Buttons
        $this->btnSave->Visible = true;
        $this->btnCancel->Visible = true;
    }

    protected function formValidate()
    {
        // Blink and FOcus any errant control
        foreach ($this->getErrorControls() as $objControl) {
            $objControl->focus();
            $objControl->blink();
        }

        return true;
    }
}

// Run the Form we have defined
ExamplesForm::run('ExamplesForm');
