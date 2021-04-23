<?php
	$strPageTitle = 'Basic Test';
 	require(QCUBED_CONFIG_DIR . '/header.inc.php');

?>

<?php $this->renderBegin(); ?>
<?php $this->txtText->RenderWithName(); ?>
<?php $this->txtText2->RenderWithName(); ?>
<?php $this->chkCheck->RenderWithName(); ?>
<?php $this->lstSelect->RenderWithName(); ?>
<?php $this->lstSelect2->RenderWithName(); ?>
<?php $this->lstCheck->RenderWithName(); ?>
<?php $this->lstCheck2->RenderWithName(); ?>
<?php $this->lstRadio->RenderWithName(); ?>
<fieldset>
	<legend>Radio Group</legend>
	<?php $this->rdoRadio1->RenderWithName(); ?>
	<?php $this->rdoRadio2->RenderWithName(); ?>
	<?php $this->rdoRadio3->RenderWithName(); ?>

</fieldset>
<?php $this->btnImage->RenderWithName(); ?>
<?php $this->btnServer->Render(); ?>
<?php $this->btnAjax->Render(); ?>
<?php $this->btnSetItemsAjax->Render(); ?>
<?php $this->renderEnd(); ?>
<?php require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>