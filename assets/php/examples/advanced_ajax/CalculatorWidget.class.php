<?php
	// The Logic here is a bit cheesy... we cheat a little because we don't take into
	// account overflow or divide-by-zero errors.  Instead, we cop out by just truncating
	// values or setting them to zero.
	//
	// Obviously, not completely accurate -- but this is really just an example dialog box, and hopefully
	// this example will give you enough to understand how \QCubed\Project\Jqui\Dialog works overall. =)
	class CalculatorWidget extends \QCubed\Project\Jqui\Dialog {
		// PUBLIC Child Controls
		public $pnlValueDisplay;
		public $pxyNumberControl;
		public $pxyOperationControl;

		public $btnEqual;
		public $btnPoint;
		public $btnClear;

		public $btnUpdate;
		public $btnCancel;
		
		protected $intWidth = 240; 
		
		// Object Variables
		protected $strCloseCallback;
		protected $fltValue;
		
		// Default Overrides
		protected $blnMatteClickable = false;
		protected $strTemplate = 'CalculatorWidget.tpl.php';
		protected $strCssClass = 'calculator_widget';

		protected $fltInternalValue;
		protected $strCurrentOperation;
		protected $blnNextClears;

		public function __construct($objParentObject, $strControlId = null) {
			parent::__construct($objParentObject, $strControlId);
			$this->DialogClass = $this->strCssClass;
			
			// Define local child controls
			$this->pnlValueDisplay = new \QCubed\Control\Panel($this);
			//$this->pnlValueDisplay->Text = '0';
			$this->pnlValueDisplay->CssClass = 'calculator_display';

			// Define the Proxy
			$this->pxyNumberControl = new \QCubed\Control\Proxy($this);
			$this->pxyNumberControl->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\AjaxControl($this, 'pxyNumber_Click'));

			$this->pxyOperationControl = new \QCubed\Control\Proxy($this);
			$this->pxyOperationControl->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\AjaxControl($this, 'pxyOperation_Click'));

			$this->btnEqual = new \QCubed\Project\Jqui\Button($this);
			$this->btnEqual->Text = '=';
			$this->btnEqual->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\AjaxControl($this, 'btnEqual_Click'));

			$this->btnPoint = new \QCubed\Project\Jqui\Button($this);
			$this->btnPoint->Text = '.';
			$this->btnPoint->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\AjaxControl($this, 'btnPoint_Click'));

			$this->btnClear = new \QCubed\Project\Jqui\Button($this);
			$this->btnClear->Text = 'C';
			$this->btnClear->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\AjaxControl($this, 'btnClear_Click'));
			
			$this->btnUpdate = new \QCubed\Project\Jqui\Button($this);
			$this->btnUpdate->Text = 'Save';
			$this->btnUpdate->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\AjaxControl($this, 'btnUpdate_Click'));
			
			$this->btnCancel = new \QCubed\Project\Jqui\Button($this);
			$this->btnCancel->Text = 'Cancel';
			$this->btnCancel->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\AjaxControl($this, 'btnCancel_Click'));
		}

		public function setCloseCallback($callback) {
            $this->strCloseCallback = $callback;
        }

		public function pxyNumber_Click($strFormId, $strControlId, $strParameter) {
			if ($this->blnNextClears) {
				$this->blnNextClears = false;
				$this->pnlValueDisplay->Text = $strParameter;
			} else if ($this->pnlValueDisplay->Text === '0') {
				$this->pnlValueDisplay->Text = $strParameter;
			} else if (strlen($this->pnlValueDisplay->Text) < 13)
				$this->pnlValueDisplay->Text .= $strParameter;
		}
		
		public function btnPoint_Click() {
			if ($this->blnNextClears) {
				$this->pnlValueDisplay->Text = '0.';
				$this->blnNextClears = false;
			} else {
				if (strpos($this->pnlValueDisplay->Text, '.') === false)
					$this->pnlValueDisplay->Text .= '.';
			}
		}

		public function pxyOperation_Click($strFormId, $strControlId, $strParameter) {
			if ($this->strCurrentOperation && !$this->blnNextClears)
				$this->btnEqual_Click();
			$this->strCurrentOperation = $strParameter;
			$this->blnNextClears = true;
			if (strpos($this->pnlValueDisplay->Text, '.') !== false)
				$this->pnlValueDisplay->Text .= '0';

			$this->fltInternalValue = \QCubed\Type::Cast($this->pnlValueDisplay->Text, \QCubed\Type::FLOAT);
			try {
				$this->fltInternalValue = \QCubed\Type::Cast($this->pnlValueDisplay->Text, \QCubed\Type::INTEGER);
			} catch (\QCubed\Exception\InvalidCast $objExc) {}
			
			$this->pnlValueDisplay->Text = $this->fltInternalValue;
		}
		
		public function btnEqual_Click() {
			$this->blnNextClears = true;

			if (strpos($this->pnlValueDisplay->Text, '.') !== false)
				$this->pnlValueDisplay->Text .= '0';
			$fltOtherValue = \QCubed\Type::Cast($this->pnlValueDisplay->Text, \QCubed\Type::FLOAT);
			try {
				$fltOtherValue = \QCubed\Type::Cast($this->pnlValueDisplay->Text, \QCubed\Type::INTEGER);
			} catch (\QCubed\Exception\InvalidCast $objExc) {}

			switch ($this->strCurrentOperation) {
				case '+':
					$this->fltInternalValue = $this->fltInternalValue + $fltOtherValue;
					break;
				case '-':
					$this->fltInternalValue = $this->fltInternalValue - $fltOtherValue;
					break;
				case '*':
					$this->fltInternalValue = $this->fltInternalValue * $fltOtherValue;
					break;
				case '/':
					if ($fltOtherValue == 0)
						$this->fltInternalValue = 0;
					else
						$this->fltInternalValue = $this->fltInternalValue / $fltOtherValue;
					break;
			}

			$this->strCurrentOperation = null;
			$this->pnlValueDisplay->Text = substr('' . $this->fltInternalValue, 0, 13);
		}

		public function btnClear_Click() {
			$this->fltValue = 0;
			$this->pnlValueDisplay->Text = 0;

			$this->fltInternalValue = 0;
			$this->blnNextClears = true;
			$this->strCurrentOperation = null;
		}

		public function btnCancel_Click() {
			$this->Close();
		}
		
		public function btnUpdate_Click() {
			$this->fltValue = $this->pnlValueDisplay->Text;
			call_user_func(array($this->objForm, $this->strCloseCallback));
			$this->Close();
		}

		public function Open() {
			parent::Open();
			$this->pnlValueDisplay->Text = ($this->fltValue) ? $this->fltValue : '0';
			
			$this->fltInternalValue = 0;
			$this->blnNextClears = true;
			$this->strCurrentOperation = null;
		}

		public function __get($strName) {
			switch ($strName) {
				case "Value": return $this->fltValue;

				default:
					try {
						return parent::__get($strName);
					} catch (\QCubed\Exception\Caller $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}

		public function __set($strName, $mixValue) {
			$this->blnModified = true;

			switch ($strName) {
				case "Value":
					// Depending on the format of $mixValue, set $this->fltValue appropriately
					// It will try to cast to Integer if possible, otherwise Float, otherwise just 0
					$this->fltValue = 0;
					try {					
						$this->fltValue = \QCubed\Type::Cast($mixValue, \QCubed\Type::FLOAT);
						break;
					} catch (\QCubed\Exception\InvalidCast $objExc) {}
					try {
						$this->fltValue = \QCubed\Type::Cast($mixValue, \QCubed\Type::INTEGER);
						break;
					} catch (\QCubed\Exception\InvalidCast $objExc) {}
					break;

				default:
					try {
						parent::__set($strName, $mixValue);
					} catch (\QCubed\Exception\Caller $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
					break;
			}
		}
	}
?>