<?php
    require_once('../qcubed.inc.php');
    
	class SelectForm extends \QCubed\Project\Control\FormBase {
		protected $auto1;
		protected $auto2;

		protected $btnServer;
		protected $btnAjax;

		protected function formCreate() {
			$this->auto1 = new \QCubed\Project\Jqui\Autocomplete($this);
			$this->auto1->Name = 'Autocomplete';

			$a = [new \QCubed\Control\ListItem ('A', 1),
				new \QCubed\Control\ListItem ('B', 2),
				new \QCubed\Control\ListItem ('C', 3),
				new \QCubed\Control\ListItem ('D', 4)
			];

			$this->auto1->Source = $a;

			$this->btnServer = new \QCubed\Project\Control\Button ($this);
			$this->btnServer->Text = 'Server Submit';
			$this->btnServer->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\Server('submit_click'));
			$this->btnServer->CausesValidation = true;

			$this->btnAjax = new \QCubed\Project\Control\Button ($this);
			$this->btnAjax->Text = 'Ajax Submit';
			$this->btnAjax->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\Ajax('submit_click'));
			$this->btnAjax->CausesValidation = true;

			$this->auto2 = new \QCubed\Project\Jqui\Autocomplete($this);
			$this->auto2->Name = 'Autocomplete w/Ajax and Validation';
			$this->auto2->SetDataBinder('auto_Bind');
			$this->auto2->Required = true;
		}

		protected function submit_click($strFormId, $strControlId, $strParameter) {
			$this->auto1->Warning = 'Text = ' . $this->auto1->Text . ' Value = ' . $this->auto1->SelectedId;
		}

		public function auto_Bind($strFormId, $strControlId, $term) {
			$cond = \QCubed\Query\QQ::OrCondition(
				\QCubed\Query\QQ::Like(QQN::Person()->FirstName, '%' . $term . '%'),
				\QCubed\Query\QQ::Like(QQN::Person()->LastName, '%' . $term . '%')
			);
			$a = Person::QueryArray($cond);
			$items = array();
			foreach ($a as $obj) {
				$items[] = new \QCubed\Control\ListItem ($obj->FirstName . ' ' . $obj->LastName, $obj->Id);
			}
			$this->auto2->DataSource = $items;
		}
		
	}
	SelectForm::Run('SelectForm');
