<?php
use QCubed\Action\Ajax;
use QCubed\Event\Click;
use QCubed\Project\Application;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\DataGrid;

require_once('../qcubed.inc.php');

class ExampleForm extends \QCubed\Project\Control\FormBase
{
    // Declare the DataGrid
    protected $dtgButtons;
    public $arRows = array();
    protected $intHitCnt;

    protected function formCreate()
    {
        // Define the DataGrid
        $this->dtgButtons = new DataGrid($this);

        $this->dtgButtons->UseAjax = true;
        $this->intHitCnt = 0;

        for ($ii = 1; $ii < 11; $ii++) {
            $this->arRows[] = "row" . $ii;
        }

        $col = $this->dtgButtons->createCallableColumn('Name', [$this, 'renderName']);
        $col->HtmlEntities = false;
        $col = $this->dtgButtons->createCallableColumn('Start standard priority javascript', [$this, 'renderButton']);
        $col->HtmlEntities = false;
        $col = $this->dtgButtons->createCallableColumn('Start low priority javascript',
            [$this, 'renderLowPriorityButton']);
        $col->HtmlEntities = false;
        $this->dtgButtons->setDataBinder('dtgButtons_Bind');
    }

    public function renderName($rowName)
    {
        return "<i>" . $rowName . "</i> ";
    }

    public function renderLowPriorityButton($row)
    {
        $objControlId = "editButton" . $row . "lowPriority";
        $objControl = $this->getControl($objControlId);
        if (!$objControl) {
            $objControl = new Button($this->dtgButtons, $objControlId);
            $objControl->addAction(new Click(), new Ajax("renderLowPriorityButton_Click"));
        }
        $objControl->Text = "update & low priority alert " . $this->intHitCnt;

        // We pass the parameter of "false" to make sure the control doesn't render
        // itself RIGHT HERE - that it instead returns its string rendering result.
        return $objControl->render(false);
    }

    public function renderButton($row)
    {
        $objControlId = "editButton" . $row;
        $objControl = $this->getControl($objControlId);
        if (!$objControl) {
            $objControl = new Button($this->dtgButtons, $objControlId);
            $objControl->addAction(new Click(), new Ajax("renderButton_Click"));
        }
        $objControl->Text = "update & alert " . $this->intHitCnt;

        // We pass the parameter of "false" to make sure the control doesn't render
        // itself RIGHT HERE - that it instead returns its string rendering result.
        return $objControl->render(false);
    }

    public function renderButton_Click($strFormId, $strControlId, $strParameter)
    {
        $this->intHitCnt++;
        //$this->dtgButtons->markAsModified();
        Application::executeJsFunction('alert', 'alert 2: a standard priority script');
        Application::executeJsFunction('alert', 'alert 1: a standard priority script');
    }

    public function renderLowPriorityButton_Click($strFormId, $strControlId, $strParameter)
    {
        $this->intHitCnt++;
        //$this->dtgButtons->markAsModified();

        Application::executeJsFunction('alert', 'alert 2: a low priority script',
            Application::PRIORITY_LOW);
        Application::executeJsFunction('alert', 'alert 1: a standard priority script');
    }

    protected function dtgButtons_Bind()
    {
        // We load the data source, and set it to the datagrid's DataSource parameter
        $this->dtgButtons->DataSource = $this->arRows;
    }
}

ExampleForm::run('ExampleForm');

