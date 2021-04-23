<?php
use QCubed\Control\DataRepeater;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\Paginator;
use QCubed\Query\QQ;

require_once('../qcubed.inc.php');

class DataRepeaterExample extends FormBase
{
    /** @var  DataRepeater */
    protected $dtrPersons;
    /** @var  DataRepeater */
    protected $dtrBig;

    protected function formCreate()
    {

        /*--- Using a Template ---*/
        $this->dtrPersons = new DataRepeater($this);

        // Let's set up pagination -- note that the form is the parent
        // of the paginator here, because it's on the form where we
        // make the call toe $this->dtrPersons->Paginator->render()
        $this->dtrPersons->Paginator = new Paginator($this);
        $this->dtrPersons->ItemsPerPage = 6;

        // Let's create a second paginator
        $this->dtrPersons->PaginatorAlternate = new Paginator($this);

        // DataRepeaters use Templates to define how the repeated
        // item is rendered
        $this->dtrPersons->Template = 'dtr_persons.tpl.php';

        // Finally, we define the method that we run to bind the data source to the datarepeater
        $this->dtrPersons->setDataBinder('dtrPersons_Bind');

        /*--- Using a callback ---*/
        $this->dtrBig = new DataRepeater($this);
        $this->dtrBig->Paginator = new Paginator($this);
        $this->dtrBig->ItemsPerPage = 10;
        $this->dtrBig->setDataBinder('dtrBig_Bind');
        $this->dtrBig->TagName = 'ul';
        $this->dtrBig->ItemTagName = 'li';
        $this->dtrBig->ItemInnerHtmlCallback = [$this, 'BigItem_Render'];
    }

    protected function dtrPersons_Bind()
    {
        // This function defines how we load the data source into the Data Repeater
        $this->dtrPersons->TotalItemCount = Person::countAll();
        $this->dtrPersons->DataSource = Person::loadAll(QQ::clause($this->dtrPersons->LimitClause));
    }

    protected function dtrBig_Bind()
    {
        // This function defines how we load the data source into the Data Repeater
        $this->dtrBig->TotalItemCount = 1000;
        for ($i = 1; $i <= 10; $i++) {
            $a[] = 'Item number ' . ($i + ($this->dtrBig->PageNumber - 1) * 10);
        }
        $this->dtrBig->DataSource = $a;
    }

    public function bigItem_Render($objItem, $intIndex)
    {
        if ($intIndex % 2) {
            return '<b>' . $objItem . '</b>';
        } else {
            return '<i>' . $objItem . '</i>';
        }
    }

}

DataRepeaterExample::run('DataRepeaterExample');
