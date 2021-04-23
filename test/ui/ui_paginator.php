<?php
    require_once('../qcubed.inc.php');
    
	class PaginatorForm extends \QCubed\Project\Control\FormBase {
		/** @var  \QCubed\Project\Control\DataGrid */
		protected $dtg;
		/** @var  \QCubed\Control\IntegerTextBox */
		protected $txtCount;
		/** @var  \QCubed\Control\IntegerTextBox */
		protected $txtPageSize;

		protected function formCreate() {
			$this->dtg = new \QCubed\Project\Control\DataGrid($this);
			$this->dtg->SetDataBinder("dtg_Bind");
			$this->dtg->Paginator = new \QCubed\Project\Control\Paginator($this->dtg);
			$this->dtg->CreateIndexedColumn("Item", 0);

			$this->txtCount = new \QCubed\Control\IntegerTextBox($this);
			$this->txtCount->Name = "Count";
			$this->txtCount->SaveState = true;
			$this->txtCount->AddAction(new \QCubed\Event\Change(), new \QCubed\Action\Ajax("refreshGrid"));

			$this->txtPageSize = new \QCubed\Control\IntegerTextBox($this);
			$this->txtPageSize->Name = "Page Size";
			$this->txtPageSize->Text = 10;
			$this->txtPageSize->SaveState = true;
			$this->txtPageSize->AddAction(new \QCubed\Event\Change(), new \QCubed\Action\Ajax("refreshGrid"));

			$intPageSize = (int)$this->txtPageSize->Text;
			$this->dtg->ItemsPerPage = $intPageSize;

		}

		protected function refreshGrid() {
			$this->dtg->Refresh();
		}

		public function dtg_Bind() {
			$intPageSize = (int)$this->txtPageSize->Text;
			$this->dtg->ItemsPerPage = $intPageSize;
			$intCount = (int)$this->txtCount->Text;
			$this->dtg->TotalItemCount = $intCount;
			$intStart = $this->dtg->ItemsOffset;
			$intEnd = min($intCount, $intStart + $intPageSize);
			for ($i = $intStart; $i < $intEnd; $i++) {
				$a[] = [self::NumToWord($i)];
			}
			if (!empty($a)) {
				$this->dtg->DataSource = $a;
			}
		}

		protected static function NumToWord($intNum) {
			$c = chr($intNum % 26 + 65);
			$intNewNum = (int)floor($intNum / 26);
			if ($intNewNum) {
				$c = self::NumToWord($intNewNum) . $c;
			}
			return $c;
		}
		
	}
PaginatorForm::Run('PaginatorForm');
?>