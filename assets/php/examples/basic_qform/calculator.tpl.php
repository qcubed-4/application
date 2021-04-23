<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>The Four-Function Calculator: Our First Simple Application</h1>

	<p>We can combine this understanding of statefulness and events to make our first simple
		QCubed Forms application.</p>

	<p>This calculator is just a collection of two <strong>TextBox</strong> objects (one for each operand), a
		<strong>ListBox</strong> object containing the four arithmetic functions, a <strong>Button</strong> object to execute
		the operation, and a <strong>Label</strong> to view the result.</p>

	<p>Note that there is no validation, checking, etc. currently in this Form.  Any string data
		will be parsed by PHP to see if there is any numeric data, and if not, it will be parsed as 0.  Dividing
		by zero will throw a PHP error.</p>
</div>

<div id="demoZone">
	<p>Value 1: <?php $this->txtValue1->render(); ?></p>

	<p>Value 2: <?php $this->txtValue2->render(); ?></p>

	<p>Operation: <?php $this->lstOperation->render(); ?></p>

	<?php $this->btnCalculate->render(); ?>
	<hr/>
	<?php $this->lblResult->render(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>