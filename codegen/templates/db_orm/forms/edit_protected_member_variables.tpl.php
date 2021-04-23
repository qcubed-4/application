<?php
use QCubed\Project\Codegen\CodegenBase as QCodegen;
?>
    /** @var <?= $strPropertyName ?>EditPanel  */
	protected $pnl<?= $strPropertyName ?>;

	/** @var <?= QCodeGen::$DefaultButtonClass ?>  */
	protected $btnSave;
	/** @var <?= QCodeGen::$DefaultButtonClass ?>  */
	protected $btnCancel;
	/** @var <?= QCodeGen::$DefaultButtonClass ?>  */
	protected $btnDelete;
