<?php require(QCUBED_CONFIG_DIR . '/header.inc.php'); ?>
<?php $this->renderBegin(); ?>
<?php $this->auto1->RenderWithName(); ?>
<?php $this->auto2->RenderWithName(); ?>
<?php $this->btnServer->Render(); ?>
<?php $this->btnAjax->Render(); ?>
<?php $this->renderEnd(); ?>
<?php require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>