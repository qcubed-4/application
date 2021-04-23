<?php use QCubed\Action\Ajax;
use QCubed\Project\Application;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ListBox;
use QCubed\Project\Control\TextBox;
use QCubed\Query\QQ;

require_once('../qcubed.inc.php');

class PersistentExampleForm extends FormBase
{
    // We will persist this control in the $_SESSION
    protected $ddnProjectPicker1;
    protected $ddnProjectPicker2;
    protected $fld1;
    protected $fld2;

    protected $btnReload;

    protected function formCreate()
    {

        $this->ddnProjectPicker1 = new ProjectPickerListBox ($this);
        $this->ddnProjectPicker2 = new ProjectPickerListBox ($this);
        $this->ddnProjectPicker2->SaveState = true;

        $this->fld1 = new TextBox($this);
        $this->fld1->Text = 'Change Me';
        $this->fld2 = new TextBox($this);
        $this->fld2->Text = 'Change Me';
        $this->fld2->SaveState = true;

        $this->btnReload = new Button($this);
        $this->btnReload->Text = 'Reload the Page';
        // Any action will trigger the communication from the client to the server to record changes on the client side.
        $this->btnReload->onClick(new Ajax('btnReload_Click'));
    }

    protected function btnReload_Click()
    {
        Application::redirect('persist.php');
    }
}

/**
 * This class encapsulates the logic of populating a list box
 * with a set of projects.
 */
class ProjectPickerListBox extends ListBox
{
    /**
     * This constructor will only be executed once - afterwards,
     * the state of the control will be stored into the $_SESSION
     * and, on future loads, populated from the session state.
     */
    public function __construct($objParentObject)
    {
        parent::__construct($objParentObject);

        $projects = Project::queryArray(
            QQ::all(),
            QQ::orderBy(QQN::project()->Name)
        );

        foreach ($projects as $project) {
            $this->addItem($project->Name, $project->Id);
        }
    }
}

PersistentExampleForm::run('PersistentExampleForm');
