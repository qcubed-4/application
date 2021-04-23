<?php
use QCubed\Action\Ajax;
use QCubed\Action\JavaScript;
use QCubed\Action\Server;
use QCubed\Action\ShowDialog;
use QCubed\Control\CheckboxList;
use QCubed\Control\ListItem;
use QCubed\Control\Panel;
use QCubed\Control\RadioButtonList;
use QCubed\Event\BackspaceKey;
use QCubed\Event\Change;
use QCubed\Event\Click;
use QCubed\Event\DialogButton;
use QCubed\Event\KeyPress;
use QCubed\Jqui\Event\AutocompleteChange;
use QCubed\Jqui\Event\DatepickerSelect2;
use QCubed\Jqui\Event\DraggableStop;
use QCubed\Jqui\Event\DroppableDrop;
use QCubed\Jqui\Event\ResizableStop;
use QCubed\Jqui\Event\SelectableStop;
use QCubed\Jqui\Event\SliderChange;
use QCubed\Jqui\Event\SliderSlide;
use QCubed\Jqui\Event\SortableStop;
use QCubed\Jqui\Event\TabsActivate;
use QCubed\Jqui\Icon;
use QCubed\Project\Application;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\TextBox;
use QCubed\Project\Jqui\Accordion;
use QCubed\Project\Jqui\Autocomplete;
use QCubed\Project\Jqui\Button;
use QCubed\Project\Jqui\Checkbox;
use QCubed\Project\Jqui\Datepicker;
use QCubed\Project\Jqui\DatepickerBox;
use QCubed\Project\Jqui\Dialog;
use QCubed\Project\Jqui\Draggable;
use QCubed\Project\Jqui\Droppable;
use QCubed\Project\Jqui\Progressbar;
use QCubed\Project\Jqui\RadioButton;
use QCubed\Project\Jqui\Resizable;
use QCubed\Project\Jqui\Selectable;
use QCubed\Project\Jqui\SelectMenu;
use QCubed\Project\Jqui\Slider;
use QCubed\Project\Jqui\Sortable;
use QCubed\Project\Jqui\Tabs;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{
    /** @var Draggable */
    protected $Draggable;
    /** @var Droppable */
    protected $Droppable;
    /** @var Resizable */
    protected $Resizable;
    /** @var Selectable */
    protected $Selectable;
    /** @var Sortable */
    protected $Sortable;

    /** @var Accordion */
    protected $Accordion;
    /** @var Autocomplete */
    protected $Autocomplete;
    /** @var Autocomplete */
    protected $AjaxAutocomplete;
    /** @var Button */
    protected $Button;
    /** @var Checkbox */
    protected $CheckBox;
    /** @var RadioButton */
    protected $RadioButton;
    /** @var Button */
    protected $IconButton;
    /** @var CheckboxList */
    protected $CheckList1;
    /** @var CheckboxList */
    protected $CheckList2;
    /** @var RadioButtonList */
    protected $RadioList1;
    /** @var RadioButtonList */
    protected $RadioList2;
    /** @var SelectMenu */
    protected $SelectMenu;

    /** @var Datepicker */
    protected $Datepicker;
    /** @var DatepickerBox */
    protected $DatepickerBox;
    /** @var Dialog */
    protected $Dialog;
    /** @var Progressbar */
    protected $Progressbar;
    /** @var Slider */
    protected $Slider;
    protected $Slider2;
    /** @var Tabs */
    protected $Tabs;
    /** @var  Button */
    protected $btnShowDialog;
    /** @var  TextBox */
    protected $txtDlgTitle;
    /** @var  TextBox */
    protected $txtDlgText;

    // Array we'll use to demonstrate the autocomplete functionality
    static private $LANGUAGES = array(
        "c++",
        "java",
        "php",
        "coldfusion",
        "javascript",
        "asp",
        "ruby"
    );

    protected function formCreate()
    {
        $this->Draggable = new Panel($this);
        $this->Draggable->Text = 'Drag me';
        $this->Draggable->CssClass = 'draggable';
        $this->Draggable->Moveable = true;
        $this->Draggable->addAction(new DraggableStop(), new Ajax("drag_stop"));

        // Dropable
        $this->Droppable = new Panel($this);
        $this->Droppable->Text = "Drop here";
        $this->Droppable->addAction(new DroppableDrop(), new Ajax("droppable_drop"));
        $this->Droppable->CssClass = 'droppable';
        $this->Droppable->Droppable = true;

        // Resizable
        $this->Resizable = new Panel($this);
        $this->Resizable->CssClass = 'resizable';
        $this->Resizable->Resizable = true;
        $this->Resizable->addAction(new ResizableStop(), new Ajax ('resizable_stop'));


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
        $this->Selectable->SelectedItems = array($pnl->ControlId);    // pre-select last item
        $this->Selectable->addAction(new SelectableStop(), new Ajax ('selectable_stop'));

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
        $this->Sortable->addAction(new SortableStop(), new Ajax ('sortable_stop'));

        // Accordion
        $this->Accordion = new Accordion($this, 'accordionCtl');
        $lbl = new \QCubed\Control\LinkButton($this->Accordion);
        $lbl->Text = 'Header 1';
        $pnl = new Panel($this->Accordion);
        $pnl->Text = 'Section 1';
        $lbl = new \QCubed\Control\LinkButton($this->Accordion);
        $lbl->Text = 'Header 2';
        $pnl = new Panel($this->Accordion);
        $pnl->Text = 'Section 2';
        $lbl = new \QCubed\Control\LinkButton($this->Accordion);
        $lbl->Text = 'Header 3';
        $pnl = new Panel($this->Accordion);
        $pnl->Text = 'Section 3';

        $this->Accordion->addAction(new Change(), new Ajax ('accordion_change'));

        // Autocomplete

        // Both autocomplete controls below will use the mode
        // "match only on the beginning of the word"
        Autocomplete::useFilter(Autocomplete::FILTER_STARTS_WITH);

        // Client-side only autocomplete
        $this->Autocomplete = new Autocomplete($this);
        $this->Autocomplete->Source = self::$LANGUAGES;
        $this->Autocomplete->Name = "Standard Autocomplete";

        // Ajax Autocomplete
        // Note: To show the little spinner while the ajax search is happening, you
        // need to define the .ui-autocomplete-loading class in a style sheet. See
        // header.inc.php for an example.
        $this->AjaxAutocomplete = new Autocomplete($this);
        $this->AjaxAutocomplete->setDataBinder("update_autocompleteList");
        $this->AjaxAutocomplete->addAction(new AutocompleteChange(),
            new Ajax ('ajaxautocomplete_change'));
        $this->AjaxAutocomplete->AutoFocus = true;
        $this->AjaxAutocomplete->Name = 'With AutoFocus';

        // Button
        $this->Button = new Button($this);
        $this->Button->Label = "Click me";    // Label overrides Text
        $this->Button->addAction(new Click, new Server("button_click"));

        $this->CheckBox = new Checkbox($this);
        $this->CheckBox->Text = "Checkbox";

        $this->RadioButton = new RadioButton($this);
        $this->RadioButton->Text = "RadioButton";

        $this->IconButton = new Button($this);
        $this->IconButton->Text = "Sample";
        $this->IconButton->ShowText = false;
        $this->IconButton->Icon = Icon::Lightbulb;

        // Lists
        $this->CheckList1 = new CheckboxList($this);
        $this->CheckList1->Name = "CheckBoxList with buttonset";
        foreach (self::$LANGUAGES as $strLang) {
            $this->CheckList1->addItem($strLang);
        }
        $this->CheckList1->ButtonMode = CheckboxList::BUTTON_MODE_SET;

        $this->CheckList2 = new CheckboxList($this);
        $this->CheckList2->Name = "CheckBoxList with button style";
        foreach (self::$LANGUAGES as $strLang) {
            $this->CheckList2->addItem($strLang);
        }
        $this->CheckList2->ButtonMode = CheckboxList::BUTTON_MODE_JQ;
        $this->CheckList2->RepeatColumns = 4;

        $this->RadioList1 = new RadioButtonList($this);
        $this->RadioList1->Name = "RadioButtonList with buttonset";
        foreach (self::$LANGUAGES as $strLang) {
            $this->RadioList1->addItem($strLang);
        }
        $this->RadioList1->ButtonMode = CheckboxList::BUTTON_MODE_SET;

        $this->RadioList2 = new RadioButtonList($this);
        $this->RadioList2->Name = "RadioButtonList with button style";
        foreach (self::$LANGUAGES as $strLang) {
            $this->RadioList2->addItem($strLang);
        }
        $this->RadioList2->ButtonMode = CheckboxList::BUTTON_MODE_JQ;
        $this->RadioList2->RepeatColumns = 4;

        $this->SelectMenu = new SelectMenu($this);
        $this->SelectMenu->Name = "SelectMenu";
        $this->SelectMenu->Width = 200;
        foreach (self::$LANGUAGES as $strLang) {
            $this->SelectMenu->addItem($strLang);
        }


        // Datepicker
        $this->Datepicker = new Datepicker($this);
        $this->Datepicker->addAction(new DatepickerSelect2(), new Ajax('setDate'));
        $this->Datepicker->ActionParameter = 'Datepicker';

        // DatepickerBox
        $this->DatepickerBox = new DatepickerBox($this);
        $this->DatepickerBox->addAction(new Change(), new Ajax('setDate'));
        $this->DatepickerBox->ActionParameter = 'DatepickerBox';


        // Dialog
        $this->Dialog = new Dialog($this);
        $this->Dialog->Text = 'a non modal dialog';
        $this->Dialog->addButton('Cancel', 'cancel');
        $this->Dialog->addButton('OK', 'ok');
        $this->Dialog->addAction(new DialogButton(), new Ajax ('dialog_press'));
        $this->Dialog->AutoOpen = false;

        $this->btnShowDialog = new Button($this);
        $this->btnShowDialog->Text = 'Show Dialog';
        $this->btnShowDialog->addAction(new Click(), new ShowDialog ($this->Dialog));

        $this->txtDlgTitle = new TextBox($this);
        $this->txtDlgTitle->Name = "Set Title To:";
        $this->txtDlgTitle->addAction(new KeyPress(10), new Ajax('dlgTitle_Change'));
        $this->txtDlgTitle->addAction(new BackspaceKey(10), new Ajax('dlgTitle_Change'));

        $this->txtDlgText = new TextBox($this);
        $this->txtDlgText->Name = "Set Text To:";
        $this->txtDlgText->addAction(new KeyPress(10), new Ajax('dlgText_Change'));
        $this->txtDlgText->addAction(new BackspaceKey(10), new Ajax('dlgText_Change'));

        // Progressbar
        $this->Progressbar = new Progressbar($this);
        $this->Progressbar->Value = 37;

        // Slider
        $this->Slider = new Slider($this);
        $this->Slider->addAction(new SliderSlide(), new JavaScript (
            'jQuery("#' . $this->Progressbar->ControlId . '").progressbar ("value", ui.value)'
        ));
        $this->Slider->addAction(new SliderChange(), new Ajax ('slider_change'));

        $this->Slider2 = new Slider($this);
        $this->Slider2->Range = true;
        $this->Slider2->Values = array(10, 50);
        $this->Slider2->addAction(new SliderChange(), new Ajax ('slider2_change'));

        // Tabs
        $this->Tabs = new Tabs($this);
        $tab1 = new Panel($this->Tabs);
        $tab1->Text = 'First tab is active by default';
        $tab2 = new Panel($this->Tabs);
        $tab2->Text = 'Tab 2';
        $tab3 = new Panel($this->Tabs);
        $tab3->Text = 'Tab 3';
        $this->Tabs->Headers = array('One', 'Two', 'Three');
        $this->Tabs->addAction(new TabsActivate(), new Ajax('tabs_change'));
    }

    protected function update_autocompleteList($strFormId, $strControlId, $strParameter)
    {
        $strLookup = $strParameter;
        $objControl = $this->getControl($strControlId);

        $cond = \QCubed\Query\QQ::orCondition(
            \QCubed\Query\QQ::like(QQN::person()->FirstName, '%' . $strLookup . '%'),
            \QCubed\Query\QQ::like(QQN::person()->LastName, '%' . $strLookup . '%')
        );

        $clauses[] = \QCubed\Query\QQ::orderBy(QQN::person()->LastName, QQN::person()->FirstName);

        $lst = Person::queryArray($cond, $clauses);

        /*
         * If you implement Person::__toString in the model->Person.class.php file, you
         * could just pass the $lst to the DataSource. If you want to add a 'label' item
         * to the display, you can override toJsObject in the People.class.php file.
         *
         * For puposes of this example, we will build a custom list using list items below.
         *
         */

        //$this->AjaxAutocomplete->DataSource = $lst;
        $a = array();
        foreach ($lst as $objPerson) {
            $item = new ListItem ($objPerson->FirstName . ' ' . $objPerson->LastName, $objPerson->Id);
            $a[] = $item;
        }
        $objControl->DataSource = $a;
    }

    protected function ajaxautocomplete_change()
    {
        Application::displayAlert('Selected item ID: ' . $this->AjaxAutocomplete->SelectedId);
    }

    protected function button_click()
    {
        $dtt = $this->DatepickerBox->DateTime;
        if ($dtt) {
            Application::displayAlert($dtt->qFormat('MM/DD/YY'));
        }
    }

    protected function slider_change()
    {
        Application::displayAlert($this->Progressbar->Value . ', ' . $this->Slider->Value);
    }

    protected function slider2_change()
    {
        $a = $this->Slider2->Values;
        Application::displayAlert($a[0] . ', ' . $a[1]);
    }

    public function dialog_press($strFormId, $strControlId, $strParameter)
    {
        $id = $this->Dialog->ClickedButton;
        Application::displayAlert($id . ' was pressed');
    }

    public function droppable_drop($strFormId, $strControlId, $strParameter)
    {
        $id = $this->Droppable->DropObj->DroppedId;
        Application::displayAlert($id . ' was dropped.');
    }

    public function resizable_stop($strFormId, $strControlId, $strParameter)
    {
        Application::displayAlert('Width change = ' . $this->Resizable->ResizeObj->DeltaX . ', height change = ' . $this->Resizable->ResizeObj->DeltaY);
    }

    public function drag_stop($strFormId, $strControlId, $strParameter)
    {
        $x = $this->Draggable->DragObj->DeltaX;
        $y = $this->Draggable->DragObj->DeltaY;
        Application::displayAlert('Left change = ' . $x . ', top change = ' . $y);
    }

    public function selectable_stop($strFormId, $strControlId, $strParameter)
    {
        $a = $this->Selectable->SelectedItems;
        $strItems = join(",", $a);
        Application::displayAlert($strItems);
    }

    public function sortable_stop($strFormId, $strControlId, $strParameter)
    {
        $a = $this->Sortable->ItemArray;
        $strItems = join(",", $a);
        Application::displayAlert($strItems);
    }

    protected function accordion_change()
    {
        Application::displayAlert($this->Accordion->Active . ' selected.');
    }

    protected function dlgTitle_Change($strFormId, $strControlId, $strParameter)
    {
        $strNewTitle = $this->txtDlgTitle->Text;
        $this->Dialog->Title = $strNewTitle;
    }

    protected function dlgText_Change($strFormId, $strControlId, $strParameter)
    {
        $strNewText = $this->txtDlgText->Text;
        $this->Dialog->Text = $strNewText;
    }

    protected function setDate($strFormId, $strControlId, $strParameter)
    {
        if ($strParameter == 'Datepicker') {
            $this->DatepickerBox->DateTime = $this->Datepicker->DateTime;
        } else {
            $this->Datepicker->DateTime = $this->DatepickerBox->DateTime;
        }
    }

    protected function tabs_change($strFormId, $strControlId, $strParameter)
    {
        $index = $this->Tabs->Active;
        $id = $this->Tabs->SelectedId;
        $strItems = $index . ', ' . $id;
        Application::displayAlert($strItems);
    }

}

ExampleForm::run('ExampleForm');
