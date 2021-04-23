<?php
use QCubed\Action\Ajax;
use QCubed\Control\Panel;
use QCubed\Control\Proxy;
use QCubed\Event\MouseOver;
use QCubed\Project\Application;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\Table;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{

    /** @var Table */
    protected $tblProjects;
    protected $pnlClick;
    protected $pxyLink;

    protected function formCreate()
    {
        // define the proxy that we will use later
        $this->pxyLink = new Proxy($this);
        $this->pxyLink->addAction(new MouseOver(), new Ajax('mouseOver'));

        // Define the DataGrid
        $this->tblProjects = new Table($this);

        // This css class is used to style alternate rows and the header, all in css
        $this->tblProjects->CssClass = 'simple_table';

        // Define Columns

        // Create a link column that shows the name of the project, and when clicked, calls back to this page with an id
        // of the item clicked on
        $this->tblProjects->createLinkColumn('Project', '->Name', Application::instance()->context()->scriptName(),
            ['intId' => '->Id']);

        // Create a link column using a proxy
        $col = $this->tblProjects->createLinkColumn('Status', '->ProjectStatusType', $this->pxyLink, '->Id');

        $this->tblProjects->setDataBinder('tblProjects_Bind');

        $this->pnlClick = new Panel($this);

        if (($intId = Application::instance()->context()->queryStringItem('intId')) && ($objProject = Project::load($intId))) {
            $this->pnlClick->Text = 'You clicked on ' . $objProject->Name;
        }

    }

    /**
     * Bind the Projects table to the html table.
     */
    protected function tblProjects_Bind()
    {
        // We load the data source, and set it to the datagrid's DataSource parameter
        $this->tblProjects->DataSource = Project::loadAll();
    }

    public function mouseOver($strFormId, $strControlId, $param)
    {
        if ($objProject = Project::load($param)) {
            $this->pnlClick->Text = 'You hovered over ' . $objProject->Name;
        }
    }

}

ExampleForm::run('ExampleForm');
