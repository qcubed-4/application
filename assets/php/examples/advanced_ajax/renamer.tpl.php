<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Making Renameable Labels</h1>

	<p>With the use of <strong>Label</strong> and <strong>TextBox</strong> controls that can show, hide and change
		<strong>CssClass</strong> names depending on what action we must execute, we use
		<strong>QAjaxActions</strong> and various events to create
		a list of "readable labels," where the interaction is similar to how files
		and folders can be selected and then renamed in the Finder or in Windows Explorer.</p>

	<p>To rename any of the labels below, click on it to highlight it. And then click it again to
		rename it. If you click elsewhere or hit return, the change will be saved. If you hit
		escape, the change will be canceled.</p>
</div>

<div id="demoZone">
<?php for ($intIndex = 0; $intIndex < 10; $intIndex++) { ?>
	<p>
		<?php $this->lblArray[$intIndex]->render(); ?>
		<?php $this->txtArray[$intIndex]->render(); ?>
	</p>
<?php } ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>