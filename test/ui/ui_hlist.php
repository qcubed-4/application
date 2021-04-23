<?php
    require_once('../qcubed.inc.php');
    
    class SelectForm extends \QCubed\Project\Control\FormBase
    {
        protected $list1;

        protected $btnServer;
        protected $btnAjax;

        protected $a;


        protected function formCreate()
        {
            $this->a = [new \QCubed\Control\HListItem('A', 1),
                new \QCubed\Control\HListItem('B', 2),
                new \QCubed\Control\HListItem('C', 3),
                new \QCubed\Control\HListItem('D', 4)
            ];

            $this->list1 = new \QCubed\Control\HList($this);
            $this->list1->Name = 'List';


            $this->list1->addItems($this->a);
            $this->list1->setDataBinder([$this, 'DataBind']);

            $this->btnServer = new \QCubed\Project\Control\Button($this);
            $this->btnServer->Text = 'Server Submit';
            $this->btnServer->addAction(new \QCubed\Event\Click(), new \QCubed\Action\Server('submit_click'));

            $this->btnAjax = new \QCubed\Project\Control\Button($this);
            $this->btnAjax->Text = 'Ajax Submit';
            $this->btnAjax->addAction(new \QCubed\Event\Click(), new \QCubed\Action\Ajax('submit_click'));
        }

        protected function submit_click($strFormId, $strControlId, $strParameter)
        {
        }

        public function dataBind()
        {
            $this->a[0]->addItems(['aa'=>0, 'ab'=>2, 'ac'=>3]);
            $this->a[1]->addItems(['ba'=>0, 'bb'=>1]);

            $this->list1->removeAllItems();
            $this->list1->addListItems($this->a);
        }
    }
    SelectForm::run('SelectForm');
