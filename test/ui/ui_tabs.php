<?php
    require_once('../qcubed.inc.php');
    class NestedTabForm extends \QCubed\Project\Control\FormBase
	{
		/**
		 * @var \QCubed\Project\Jqui\Tabs
		 */
		protected $tabs;
		/**
		 * @var \QCubed\Control\Panel
		 */
		protected $log;
		/**
		 * @var \QCubed\Control\Panel[]
		 */
		protected $panels = [];

		protected function formCreate()
		{
			$this->log = new \QCubed\Control\Panel($this);
			$this->tabs = new \QCubed\Project\Jqui\Tabs($this);
			$this->tabs->Headers = ['one', 'two'];
			$this->panels[] = $this->CreatePanel("hi", $this->tabs);
			$pnl = new \QCubed\Control\Panel($this->tabs);
			$pnl->AutoRenderChildren = true;
			$this->panels[] = $pnl;
			$tabs = new \QCubed\Project\Jqui\Tabs($this->panels[0]);
			$tabs->Headers = ['three', 'four'];
			$this->CreatePanel("aaa2", $tabs);
			$this->CreatePanel("bbb", $tabs);
			$this->tabs->AddAction(new \QCubed\Jqui\Event\TabsActivate(), new \QCubed\Action\Ajax('tabs_Load'));
			//$tabs->AddAction(new \QCubed\Jqui\Event\TabsActivate(), new \QCubed\Action\Ajax('tabs2_Load'));
		}

		public function CreatePanel($strContent, $objTab)
		{
			$pnl = new \QCubed\Control\Panel($objTab);
			$pnl->AutoRenderChildren = true;
			$pnlContent = new \QCubed\Control\Panel($pnl);
			$pnlContent->Text = $strContent;
			return $pnl;
		}

		public function tabs_Load($strForm, $strControl, $strParam, $params)
		{
			/**
			 * @var $objControl \QCubed\Project\Jqui\Tabs
			 */
			$objControl = $this->GetControl($strControl);
			if (!$objControl) {
				return;
			}
			$this->log->Text = $objControl->SelectedId . ' activated';
			if ($objControl->Active == 1) {
				$pnlParent = $this->GetControl($objControl->SelectedId);
				if (count($pnlParent->GetChildControls()) > 0) {
					return;
				}
				$pnlContent = new \QCubed\Control\Panel($this->panels[1]);
				$pnlContent->Text = "there ";
				$this->tabs->Refresh();
			}
		}
	}
    NestedTabForm::Run('NestedTabForm');
