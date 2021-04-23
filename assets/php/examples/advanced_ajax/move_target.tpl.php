<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Specifying Which Controls to Move</h1>

	<p>Hopefully this example shows why not all <strong>Control</strong> objects can be move handles.</p>

	<p>Below, we have rendered a <strong>Label</strong> and a <strong>TextBox</strong>.  We want the textbox to be moveable,
		but if we make the textbox a "move handle" to move itself, the user will no longer be able to click
		"into" the textbox to enter in data.  Therefore, we specify the label to be the "move handle",
		and we add the label (itself) and the textbox as targets to be moved by the label.</p>

	<p>This is done by making two calls to the label's <strong>AddControlToMove</strong> method.  The first call
		is made to add the label (itself), and the second call is made to add the textbox.</p>

	<p>Note how you will move both controls when you drag the label around, and also note how you can
		still click "into" the textbox to enter in data.</p>
</div>

<div id="demoZone">
	<?php $this->pnlParent->Render(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>