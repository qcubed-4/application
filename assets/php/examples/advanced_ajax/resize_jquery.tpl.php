<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Resizing Block Controls</h1>
	<p>Any control can be resizeable by simply setting the <strong>Resizable</strong> attribute to true.
		As in draggable controls, when you set <strong>Resizable</strong> to true, you can then access
		the <strong>ResizeObj</strong> attribute to get access to the JQuery UI <strong>resizable</strong> functions.</p>
</div>

<style>
	.ui-resizable-helper { border: 2px dotted #780000; }
</style>

<div id="demoZone">
	<p><?php $this->pnlLeftTop->render(); ?></p>
	<p><?php $this->txtTextbox->render(); ?></p>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>