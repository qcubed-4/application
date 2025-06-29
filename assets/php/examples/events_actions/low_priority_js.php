<?php
use QCubed\Action\Ajax;
use QCubed\ApplicationBase;
use QCubed\Event\Click;
use QCubed\Exception\Caller;
use QCubed\Project\Application;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\DataGrid;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{
    // Declare the DataGrid
    protected DataGrid $dtgButtons;
    public array $arRows = array();
    protected int $intHitCnt;

    protected function formCreate(): void
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
        $col = $this->dtgButtons->createCallableColumn('Start standard priority JavaScript', [$this, 'renderButton']);
        $col->HtmlEntities = false;
        $col = $this->dtgButtons->createCallableColumn('Start low-priority JavaScript',
            [$this, 'renderLowPriorityButton']);
        $col->HtmlEntities = false;
        $this->dtgButtons->setDataBinder('dtgButtons_Bind');
    }

    /**
     * Renders a name wrapped in italic tags.
     *
     * @param mixed $rowName The value of the name to be rendered.
     * @return string The rendered name in italic format.
     */
    public function renderName(mixed $rowName): string
    {
        return "<i>" . $rowName . "</i> ";
    }

    /**
     * Renders a low-priority button control for a specified row.
     *
     * @param mixed $row The row value used to uniquely identify and generate the low-priority button control.
     * @return string The rendered output of the low-priority button control.
     * @throws Caller
     */
    public function renderLowPriorityButton(mixed $row): string
    {
        $objControlId = "editButton" . $row . "lowPriority";
        $objControl = $this->getControl($objControlId);
        if (!$objControl) {
            $objControl = new Button($this->dtgButtons, $objControlId);
            $objControl->addAction(new Click(), new Ajax("renderLowPriorityButton_Click"));
        }
        $objControl->Text = "update & low-priority alert " . $this->intHitCnt;

        // We pass the parameter of "false" to make sure the control doesn't render
        // itself RIGHT HERE - that it instead returns its string rendering result.
        return $objControl->render(false);
    }

    /**
     * Renders a button control for a specified row.
     *
     * @param mixed $row The row value used to uniquely identify and generate the button control.
     * @return string The rendered output of the button control.
     * @throws Caller
     */
    public function renderButton(mixed $row): string
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

    public function renderButton_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        $this->intHitCnt++;
        //$this->dtgButtons->markAsModified();
        Application::executeJsFunction('alert', 'alert 2: a standard priority script');
        Application::executeJsFunction('alert', 'alert 1: a standard priority script');
    }

    public function renderLowPriorityButton_Click(string $strFormId, string $strControlId, string $strParameter): void
    {
        $this->intHitCnt++;
        //$this->dtgButtons->markAsModified();

        Application::executeJsFunction('alert', 'alert 2: a low-priority script',
            ApplicationBase::PRIORITY_LOW);
        Application::executeJsFunction('alert', 'alert 1: a standard priority script');
    }

    protected function dtgButtons_Bind(): void
    {
        // We load the data source and set it to the datagrid's DataSource parameter
        $this->dtgButtons->DataSource = $this->arRows;
    }
}

ExampleForm::run('ExampleForm');

