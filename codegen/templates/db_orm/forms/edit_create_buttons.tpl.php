<?php
use QCubed\Project\Codegen\CodegenBase as QCodegen;
?>

    /**
	 * Create the buttons at the bottom of the dialog.
	 */
	protected function CreateButtons() {
		// Create Buttons and Actions on this Form
		$this->btnSave = new <?= QCodeGen::$DefaultButtonClass ?>($this);
		$this->btnSave->Text = t('Save');
		$this->btnSave->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnSave_Click'));
		$this->btnSave->CausesValidation = true;

		$this->btnCancel = new <?= QCodeGen::$DefaultButtonClass ?>($this);
		$this->btnCancel->Text = t('Cancel');
		$this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnCancel_Click'));

		$this->btnDelete = new <?= QCodeGen::$DefaultButtonClass ?>($this);
		$this->btnDelete->Text = t('Delete');
		$this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\Confirm(sprintf(t('Are you SURE you want to DELETE this %s?'), t('<?= $objTable->ClassName ?>'))));
		$this->btnDelete->addAction(new Q\Event\Click(), new Q\Action\Ajax('btnDelete_Click'));
		$this->btnDelete->Visible = $this->pnl<?= $strPropertyName ?>->mct<?= $objTable->ClassName ?>->EditMode;
	}
