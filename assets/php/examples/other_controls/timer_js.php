<?php
use QCubed\Action\Ajax;
use QCubed\Action\AjaxControl;
use QCubed\Action\Server;
use QCubed\Action\ServerControl;
use QCubed\Event\Click;
use QCubed\Event\TimerExpired;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\DataGrid;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\JsTimer;

require_once('../qcubed.inc.php');

class Order
{
    public $Id;
    public $Items;
}

class ExampleForm extends FormBase
{
    // Declare the DataGrid
    protected $dtgOrders;
    public $objOrdersArray = array();

    protected $intOrderCnt = 0;

    protected $btnServerAction;
    protected $btnRestartOnServerAction;

    protected $ctlTimer;

    protected $btnStart;
    protected $btnStop;

    protected $objRandomProductsArray = array();

    protected function formCreate()
    {

        $this->objRandomProductsArray[0] = '1x Sandwich, 2x Coke, 1x Big Pekahuna Burger';
        $this->objRandomProductsArray[1] = '2x French fries, 3x Burritos, 1x Hot Dog';
        $this->objRandomProductsArray[2] = '1x Steak - Lone Star, 5x Wiener Schnitzel';
        $this->objRandomProductsArray[3] = '3x Socks, 3x Shorts';

        // Define the DataGrid
        $this->dtgOrders = new DataGrid($this);
        $this->dtgOrders->UseAjax = true;

        //button to simulate a server action
        $this->btnServerAction = new Button($this);
        $this->btnServerAction->setCssStyle('float', 'right');

        //button for switching the 'RestartOnServer' capability of \QCubed\Project\Control\JsTimer on/off
        $this->btnRestartOnServerAction = new Button($this);
        $this->btnStop = new Button($this);
        $this->btnStart = new Button($this);

        //create the timer: parent = $this, $time = 3000ms, periodic = true, autostart=true
        $this->ctlTimer = new JsTimer($this, 300, true, true);


        $this->dtgOrders->createPropertyColumn('Order-Id', 'Id');
        $this->dtgOrders->createPropertyColumn('Products', 'Items');
        $col = $this->dtgOrders->createCallableColumn('Remove', [$this, 'renderRemoveButton']);
        $col->HtmlEntities = false;
        $this->dtgOrders->setDataBinder('dtgOrders_Bind');

        $this->btnServerAction->addAction(new Click(), new Server('OnServerAction'));
        $this->btnServerAction->Text = "Server Action";

        $this->btnRestartOnServerAction->addAction(new Click(), new Ajax('OnToggleRestartOnServerAction'));
        $this->btnRestartOnServerAction->Text = "Restart On Server Action [off]";


        $this->ctlTimer->addAction(new TimerExpired(), new Ajax('OnUpdateDtg'));

        $this->btnStart->addAction(new Click(), new ServerControl($this->ctlTimer, 'start'));
        $this->btnStop->addAction(new Click(), new ServerControl($this->ctlTimer, 'stop'));
        $this->btnStart->Text = 'Start';
        $this->btnStop->Text = 'Stop';

    }

    //the timer callback function for updating the orders
    public function onUpdateDtg()
    {
        //fetch new orders
        $randProdNum = rand(0, 3);
        $this->intOrderCnt++;
        // Limit the amount of items in a table to 10
        // There is no paging for this datagrid,
        // so many items here can consume CPU greatly
        if ($this->intOrderCnt > 10) {
            $this->intOrderCnt = 1;
            $this->objOrdersArray = array();
        }
        $order = new Order();
        $order->Id = $this->intOrderCnt;
        $order->Items = $this->objRandomProductsArray[$randProdNum];
        $this->objOrdersArray[$this->intOrderCnt] = $order;
        $this->dtgOrders->markAsModified();
    }

    public function onToggleRestartOnServerAction()
    {
        $blnRestart = $this->ctlTimer->RestartOnServerAction;
        if ($blnRestart) {
            $this->btnRestartOnServerAction->Text = "Restart On Server Action [off]";
        } else {
            $this->btnRestartOnServerAction->Text = "Restart On Server Action [on]";
        }

        $this->ctlTimer->RestartOnServerAction = !$blnRestart;
    }

    public function onServerAction()
    {
        //ServerAction test
    }

    public function renderRemoveButton($item)
    {
        $objControlId = "removeButton" . $item->Id;
        $objControl = $this->getControl($objControlId);
        if (!$objControl) {
            $objControl = new \QCubed\Project\Jqui\Button($this->dtgOrders, $objControlId);
            $objControl->Text = true;
            $objControl->ActionParameter = $item->Id;
            $objControl->addAction(new Click(), new Ajax("removeButton_Click"));
        }

        $objControl->Label = "Remove";

        // We pass the parameter of "false" to make sure the control doesn't render
        // itself RIGHT HERE - that it instead returns its string rendering result.
        return $objControl->render(false);
    }


    public function removeButton_Click($strFormId, $strControlId, $strParameter)
    {
        unset($this->objOrdersArray[$strParameter]);
        $this->dtgOrders->markAsModified();
    }

    protected function dtgOrders_Bind()
    {
        // We load the data source, and set it to the datagrid's DataSource parameter
        $this->dtgOrders->DataSource = $this->objOrdersArray;
    }
}

ExampleForm::run('ExampleForm');

