<?php
use QCubed\Project\Codegen\CodegenBase as QCodegen;
?>
    /**
     * Create the buttons at the bottom of the dialog.
     */
    protected function createButtons(): void
    {
        // Create Buttons and Actions on this Form
        $this->btnSave = new Button($this);
        $this->btnSave->Text = t('Save');
        $this->btnSave->addAction(new Click(), new Ajax('btnSave_Click'));
        $this->btnSave->CausesValidation = true;

        $this->btnCancel = new Button($this);
        $this->btnCancel->Text = t('Cancel');
        $this->btnCancel->addAction(new Click(), new Ajax('btnCancel_Click'));

        $this->btnDelete = new Button($this);
        $this->btnDelete->Text = t('Delete');
        $this->btnDelete->addAction(new Click(), new Ajax('btnDelete_Click'));
        $this->btnDelete->Visible = $this->pnl<?= $strPropertyName ?>->mct<?= $objTable->ClassName ?>->EditMode;
}
