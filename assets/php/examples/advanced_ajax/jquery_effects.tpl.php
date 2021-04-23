<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<style>
	.ui-effects-transfer { border: 2px dotted #780000; }
</style>

<div id="instructions">
	<h1>JQuery UI Effects</h1>

	<p>QCubed comes with built-in support of jQuery UI effects.


	<p>To see this in action: in the example below, use the buttons to apply
		effects on the <strong>Label</strong> control. To make it happen, use the
		following actions in your code: </p>

	<h2>To control visibility:</h2>
	<ul>
		<li><strong>Show</strong>: show a control (if it's hidden)</li>
		<li><strong>ShowEffect</strong>: show a control using one of the additional effects</li>
		<li><strong>Hide</strong>: hide a control</li>
		<li><strong>HideEffect</strong>: hide a control using one of the additional effects</li>
		<li><strong>Toggle</strong>: toggle visibility of a control</li>
		<li><strong>ToggleEffect</strong>: toggle visibility of a control using one of the additional effects</li>
	</ul>

	<h2>To perform animations:</h2>
	<ul>
		<li><strong>Bounce</strong>: make a control bounce up and down</li>
		<li><strong>Shake</strong>: make a control shake left and right</li>
		<li><strong>Highlight</strong>: highlight a control</li>
		<li><strong>Pulsate</strong>: pulsate the contents of a control</li>
		<li><strong>Size</strong>: resize a control</li>
		<li><strong>Transfer</strong>: transfer the border of a control to another control</li>
	</ul>

	<p>More information on the parameters of each of the available animations
		can be found on the <a target="_blank" href="http://docs.jquery.com/UI/Effects">JQuery UI Effects</a> site.</p>
</div>

<div id="demoZone">
	<?php $this->btnToggle->render() ?>
	<?php $this->btnHide->render() ?>
	<?php $this->btnShow->render() ?>
	<?php $this->btnBounce->render() ?>
	<?php $this->btnHighlight->render() ?>
	<?php $this->btnShake->render() ?>
	<?php $this->btnPulsate->render() ?>
	<?php $this->btnSize->render() ?>
	<?php $this->btnTransfer->render() ?>

	<p><?php $this->txtTextbox->render(); ?></p>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>