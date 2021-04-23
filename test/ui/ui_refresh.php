<?php
    require_once('../qcubed.inc.php');
    
	class RefreshForm extends \QCubed\Project\Control\FormBase {
		protected $panel1;
		protected $txt1;

		protected $panel2;
		protected $txt2;


		protected $btnServer1;
		protected $btnServer2;

		protected $btnAjax1;
		protected $btnAjax2;

		protected function formCreate() {
			$this->txt1 = new \QCubed\Project\Control\TextBox($this, 'txtNoWrap');
			$this->txt1->Name = "Wrapperless";
			$this->txt1->Text = "Text without wrapper";
			$this->txt1->UseWrapper = false;

			$this->panel1 = new \QCubed\Control\Panel($this, 'pnlNoWrap');
			$this->panel1->Text = 'Stuff in the panel without a wrapper';
			$this->panel1->BorderStyle = \QCubed\Css\BorderStyle::SOLID;
			$this->panel1->BorderColor = 'red';
			$this->panel1->BorderWidth = 1;
			$this->panel1->UseWrapper = false;

			$this->txt2 = new \QCubed\Project\Control\TextBox($this, 'txtWrap');
			$this->txt2->Name = "With Wrapper";
			$this->txt2->Text = "Some stuff with wrapper";
			$this->txt2->UseWrapper = true;
			$this->txt2->Display = false;

			$this->panel2 = new \QCubed\Control\Panel($this, 'pnlWrap');
			$this->panel2->Text = 'Other stuff in the panel with a wrapper';
			$this->panel2->BorderStyle = \QCubed\Css\BorderStyle::DASHED;
			$this->panel2->BorderColor = 'blue';
			$this->panel2->BorderWidth = 1;
			$this->panel2->UseWrapper = true;
			$this->panel2->Display = false;

			$this->btnServer1 = new \QCubed\Project\Control\Button ($this, 'btnServerToggle');
			$this->btnServer1->Text = 'Server Display Toggle';
			$this->btnServer1->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\Server('display_toggle'));

			$this->btnAjax1 = new \QCubed\Project\Control\Button ($this, 'btnAjaxToggle');
			$this->btnAjax1->Text = 'Ajax Display Toggle';
			$this->btnAjax1->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\Ajax('display_toggle'));

			$this->btnServer2 = new \QCubed\Project\Control\Button ($this, 'btnServerChange');
			$this->btnServer2->Text = 'Server Change Item';
			$this->btnServer2->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\Server('change_item'));

			$this->btnAjax2 = new \QCubed\Project\Control\Button ($this, 'btnAjaxChange');
			$this->btnAjax2->Text = 'Ajax Change Item';
			$this->btnAjax2->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\Ajax('change_item'));
		}

		protected function display_toggle($strFormId, $strControlId, $strParameter) {
			$this->panel1->Display = !$this->panel1->Display;
			$this->panel2->Display = !$this->panel2->Display;
			$this->txt1->Display = !$this->txt1->Display;
			$this->txt2->Display = !$this->txt2->Display;
		}

		public function change_item($strFormId, $strControlId, $term) {
			$col = $this->txt1->Columns;
			$this->txt1->Columns = $this->txt1->Columns > 20 ? 10 : 30;
			$this->txt1->Name = $this->txt1->Name . '-';
			$this->txt2->Columns = $this->txt2->Columns > 20 ? 10 : 30;
			$this->txt2->Name = $this->txt2->Name . '-';

			$this->panel1->BorderWidth = $this->panel1->BorderWidth == 1 ? 5 : 1;
			$this->panel2->BorderWidth = $this->panel2->BorderWidth == 1 ? 5 : 1;
		}
		
	}
RefreshForm::Run('RefreshForm');
?>