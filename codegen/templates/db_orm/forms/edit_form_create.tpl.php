<?php
use QCubed\Project\Codegen\CodegenBase as QCodegen;
?>

    protected function formCreate() {
		parent::formCreate();

		$this->pnl<?= $strPropertyName ?> = new <?= $strPropertyName ?>EditPanel($this);
<?php
	$_INDEX = 0;
	foreach ($objTable->PrimaryKeyColumnArray as $objColumn) {
		if (QCodeGen::$CreateMethod === 'queryString') {
?>
		$<?= $objColumn->VariableName ?> = Application::instance()->context()->queryStringItem('<?= $objColumn->VariableName ?>');
<?php
		} else {
?>
		$<?= $objColumn->VariableName ?> = Application::instance()->context()->pathInfo(<?= $_INDEX ?>);
<?php 		$_INDEX++;
		}
?>
<?php
	}
?>
	    $this->pnl<?= $strPropertyName ?>->load(<?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>$<?= $objColumn->VariableName ?>, <?php } GO_BACK(2); ?>);
		$this->createButtons();
	}
