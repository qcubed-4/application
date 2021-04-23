<?php require(QCUBED_CONFIG_DIR . '/header.inc.php'); ?>
<?php $this->renderBegin(); ?>
<div>
<?php $this->txt1->RenderWithName(); ?>
<?php $this->txt2->RenderWithName(); ?>
<?php $this->panel1->Render(); ?>
<?php $this->panel2->Render(); ?>
</div>
<div>
<?php $this->btnServer1->Render(); ?>
<?php $this->btnServer2->Render(); ?>
<?php $this->btnAjax1->Render(); ?>
<?php $this->btnAjax2->Render(); ?>
</div>
<?php $this->renderEnd(); ?>
<?php require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>