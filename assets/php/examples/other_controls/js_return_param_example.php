<?php
/** @noinspection PhpIncludeInspection */
use QCubed\Action\ActionParams;
use QCubed\Action\Ajax;
use QCubed\Action\Server;
use QCubed\Control\Panel;
use QCubed\Jqui\Event\ResizableStop;
use QCubed\Jqui\Event\SelectableStop;
use QCubed\Jqui\Event\SortableUpdate;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Jqui\Button;
use QCubed\Project\Jqui\Resizable;
use QCubed\Project\Jqui\Selectable;
use QCubed\Project\Jqui\Slider;
use QCubed\Project\Jqui\Sortable;

require_once('../qcubed.inc.php');

// adding the javascript return parameter to the event is one
// possibility to retrieve values/objects/arrays via an Ajax or Server Action
class MyQSlider_ChangeEvent extends \QCubed\Event\EventBase
{
    const EVENT_NAME = 'slidechange';
    const JS_RETURN_PARAM = 'arguments[1].value';
}

class ExampleForm extends FormBase
{
    /** @var Resizable */
    protected $Resizable;
    /** @var Selectable */
    protected $Selectable;
    /** @var Sortable */
    protected $Sortable;
    /** @var Slider */
    protected $Slider;
    /** @var Button */
    protected $btnSubmit;
    /** @var Sortable */
    protected $Sortable2;

    /** @var Panel */
    protected $SortableResult;
    /** @var Panel */
    protected $Sortable2Result;
    /** @var Panel */
    protected $ResizableResult;
    /** @var Panel */
    protected $SelectableResult;
    /** @var Panel */
    protected $SubmitResult;
    /** @var Panel */
    protected $SliderResult;

    /**
     *
     */
    protected function formCreate()
    {
        $strServerActionJsParam = "";

        $this->btnSubmit = new Button($this);
        $this->btnSubmit->Text = "ServerAction Submit";
        $this->SubmitResult = new Panel($this);

        // Slider
        $this->Slider = new Slider($this);
        $this->Slider->Max = 1250;
        $this->Slider->addAction(new MyQSlider_ChangeEvent(), new Ajax('onSlide'));
        $this->SliderResult = new Panel($this);

        // Resizable
        $this->Resizable = new Panel($this);
        $this->Resizable->CssClass = 'resizable';
        $this->Resizable->Resizable = true;
        $this->ResizableResult = new Panel($this);
        $strJsParam = '{ 
				"width": $j("#' . $this->Resizable->ControlId . '").width(), 
				"height": $j("#' . $this->Resizable->ControlId . '").height() 
			}';
        $this->Resizable->addAction(new ResizableStop(),
            new Ajax("onResize", "default", null, $strJsParam));
        $this->ResizableResult = new Panel($this);

        $strServerActionJsParam = '{"resizable": ' . $strJsParam . ', ';

        // Selectable
        $this->Selectable = new Selectable($this);
        $this->Selectable->AutoRenderChildren = true;
        $this->Selectable->CssClass = 'selectable';
        for ($i = 1; $i <= 5; ++$i) {
            $pnl = new Panel($this->Selectable);
            $pnl->Text = 'Item ' . $i;
            $pnl->CssClass = 'selitem';
        }
        $this->Selectable->Filter = 'div.selitem';

        /*
        * if your objects to return get more complex you can define a javascript function that returns your
        * object. the essential thing is the ".call()", this executes the function that you have just defined
        * and returns your object.
        * In this example a function is uesd to temporary store jquery's search result for selected items,
        * because it is needed twice. then the ids are stored to objRet.ids as a comma-separated string and
        * the contents of the selected items are stored to objRet.content as an array.
        *
        */
        $this->SelectableResult = new Panel($this);
        $strJsParam = 'function() { 
				objRet = new Object(); 
				selection = $j("#' . $this->Selectable->ControlId . '")
					.find(".ui-selected");
				objRet.ids = selection.map(function(){
						return this.id;
					}).get()
					.join(",");
				objRet.content = selection.map(function() { 
					return $j(this).html();
				}).get(); 
				return objRet;
			}.call()';
        $this->Selectable->addAction(new SelectableStop(),
            new Ajax("onSelect", "default", null, $strJsParam));

        $strServerActionJsParam .= '"selectable": ' . $strJsParam . ', ';


        // Sortable
        $this->Sortable = new Sortable($this);
        $this->Sortable->AutoRenderChildren = true;
        $this->Sortable->CssClass = 'sortable';
        for ($i = 1; $i <= 5; ++$i) {
            $pnl = new Panel($this->Sortable);
            $pnl->Text = 'Item ' . $i;
            $pnl->CssClass = 'sortitem';
        }
        $this->Sortable->Items = 'div.sortitem';

        $this->SortableResult = new Panel($this);
        $strJsParam = '$j("#' . $this->Sortable->ControlId . '").
				find("div.sortitem").
				map(function() { 
					return $j(this).html()
				}).get()';
        $this->Sortable->addAction(new SortableUpdate(),
            new Ajax("onSort", "default", null, $strJsParam));

        $strServerActionJsParam .= '"sortable": ' . $strJsParam . '}';


        //a second Sortable that can receive items from the first Sortable
        //when an item is dragged over from the first sortable an receive event is triggered
        $this->Sortable2 = new Sortable($this);
        $this->Sortable2->AutoRenderChildren = true;
        $this->Sortable2->CssClass = 'sortable';
        for ($i = 6; $i <= 10; ++$i) {
            $pnl = new Panel($this->Sortable2);
            $pnl->Text = 'Item ' . $i;
            $pnl->CssClass = 'sortitem';
        }
        $this->Sortable2->Items = 'div.sortitem';

        //allow dragging from Sortable to Sortable2
        $this->Sortable->ConnectWith = '#' . $this->Sortable2->ControlId;
        //enable the following line to allow dragging Sortable2 child items to the Sortable list
        // $this->Sortable2->ConnectWith = '#' . $this->Sortable->ControlId;

        //using a \QCubed\Js\Closure as the ActionParameter for Sortable2 to return a Js object
        //the ActionParameter is used for every ajax / server action defined on this control
        $this->Sortable2->ActionParameter =
            new \QCubed\Js\Closure('return $j("#' . $this->Sortable2->ControlId . '")
					.find("div.sortitem")
					.map(function() { 
						return $j(this).html()
					}).get();');

        //(the list of names from the containing items) is returned for the following two Ajax Actions
        $this->Sortable2->addAction(new SortableUpdate(), new Ajax("onSort2"));
        //$this->Sortable2->addAction(new \QCubed\Jqui\Event\SortableReceive() ,new \QCubed\Action\Ajax("onSort2"));

        $this->Sortable2Result = new Panel($this);

        $this->btnSubmit->onClick(new Server("onSubmit", null, $strServerActionJsParam));
    }

    public function onSort(ActionParams $params)
    {
        $this->SortableResult->Text = print_r($params->ActionParameter, true);
    }

    public function onSort2(ActionParams $params)
    {
        $this->Sortable2Result->Text = print_r($params->ActionParameter, true);
    }

    public function onResize(ActionParams $params)
    {
        $this->ResizableResult->Text = print_r($params->ActionParameter, true);
    }

    public function onSelect(ActionParams $params)
    {
        $this->SelectableResult->Text = print_r($params->ActionParameter, true);
    }

    public function onSubmit(ActionParams $params)
    {
        $this->SubmitResult->Text = print_r($params->ActionParameter, true);
    }

    public function onSlide(ActionParams $params)
    {
        $this->SliderResult->Text = print_r($params->ActionParameter, true);
    }
}

ExampleForm::run('ExampleForm');
