<?php
use QCubed\Action\Ajax;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');
	
	class ExampleForm extends FormBase
	{
		/** @var  Button */
		protected Button $btnRegular;
		/** @var  Button */
		protected Button $btnBlocking;
		protected int $intRegularCount = 0;
		protected int $intBlockingCount = 0;


		protected Label $lblRegular;
		protected Label $lblBlocking;

		protected function formCreate(): void
        {
			$this->btnRegular = new Button($this);
			$this->btnRegular->Text = "Regular Button";
			$this->btnRegular->addAction(new Click(), new Ajax('btnRegular_Click'));
			$this->btnBlocking = new Button($this);
			$this->btnBlocking->Text = "Blocking Button";
			$this->btnBlocking->addAction(new Click(0, null, null, true), new Ajax('btnBlocking_Click'));

			// Define a Message label
			$this->lblRegular = new Label($this);
			$this->lblRegular->Text = '0';
			$this->lblBlocking = new Label($this);
			$this->lblBlocking->Text = '0';
		}

		protected function btnRegular_Click(): void
        {
			$this->intRegularCount += 1;
			$this->lblRegular->Text = $this->intRegularCount;
			$this->btnRegular->Enabled = false;
		}

		protected function btnBlocking_Click(): void
        {
			$this->intBlockingCount += 1;
			$this->lblBlocking->Text = $this->intBlockingCount;
			$this->btnBlocking->Enabled = false;
		}
	}

ExampleForm::run('ExampleForm');

