<?php
use QCubed\Action\Ajax;
use QCubed\Control\Proxy;
use QCubed\Event\Click;
use QCubed\Event\TimerExpired;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\DataGrid;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\JsTimer;
use QCubed\Project\Control\TextBox;

const __IN_EXAMPLE__ = true;
require_once('../qcubed.inc.php');


// The following code sets up a temporary watcher just for this example, since the examples are based on the default
// installation of the code, and the default installation does not create a watcher class. Normally, to be able to
// use watchers correctly, you must edit the \QCubed\Project\Watcher\Watcher.class.php to specify the kind of watcher you want to use.


class ExampleForm extends FormBase
{

    // Declare the DataGrid
    protected DataGrid $dtgPersons;
    protected TextBox $txtFirstName;
    protected TextBox $txtLastName;
    protected Button $btnNew;
    protected JsTimer $timer;
    /** @var  Proxy */
    protected Proxy $pxyDelete;

    protected function formCreate(): void
    {
        // Define the DataGrid
        $this->dtgPersons = new DataGrid($this);

        // Define Columns
        $this->dtgPersons->createNodeColumn('First Name', QQN::person()->FirstName);
        $this->dtgPersons->createNodeColumn('Last Name', QQN::person()->LastName);

        // Specify the local Method which will actually bind the data source to the datagrid.
        $this->dtgPersons->setDataBinder('dtgPersons_Bind');

        // By default, the example database uses the qc watchers table to record when something in the database has changed.
        // To configure this, including changing the table name, or even using a shared caching mechanism like
        // APC or Memcached, modify the Watcher class in project/qcubed/Watcher

        // Tell the datagrid to watch the Person table.
        $this->dtgPersons->watch(QQN::person());

        // Create a timer to periodically check whether another user has changed the database. Depending on your
        // application, you might not need to do this, as any activity the user does to a control will also check.
        //$this->timer = new JsTimer($this, 500, true);
        //$this->timer->addAction(new TimerExpired(), new Ajax());

        $this->txtFirstName = new TextBox($this);
        $this->txtFirstName->Required = true;
        $this->txtLastName = new TextBox($this);
        $this->txtLastName->Required = true;
        $this->btnNew = new Button($this);
        $this->btnNew->Text = 'Add';
        $this->btnNew->addAction(new Click(), new Ajax('btnNew_Click'));

        // Create a proxy control to handle clicking for a deleted
        $this->pxyDelete = new Proxy($this);
        //$this->pxyDelete->addAction(new Click(), new Ajax ('delete_Click'));
    }

    protected function dtgPersons_Bind(): void
    {
        // We load the data source and set it to the datagrid's DataSource parameter
        $this->dtgPersons->DataSource = Person::loadAll();
    }

    protected function btnNew_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        if (!$this->txtFirstName->Text || !$this->txtLastName->Text) {
            $objPerson = new Person();
            $objPerson->FirstName = $this->txtFirstName->Text;
            $objPerson->LastName = $this->txtLastName->Text;
            $objPerson->save();
        }
    }
}

ExampleForm::run('ExampleForm');
