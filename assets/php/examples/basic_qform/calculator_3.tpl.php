<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Custom Renderers and Control Properties</h1>

	<p>In our final Calculator example, we show how you can use custom renderers to affect layout,
		as well as use control properties to change the appearance of your QControls.</p>

	<p>The QCubed distribution includes a sample custom renderer, <strong>renderWithName()</strong>, which is
		defined in your Control custom class (which is at /project/qcubed/Control/ControlBase.php).
		We'll use this <strong>renderWithName()</strong> for our calculator's textboxes and listbox.  We've also
		made sure to assign <strong>Name</strong> properties to these Controls.</p>

	<p>Note how "Value 1" and "Value 2" are in all caps and boldfaced, while "Operation" is not.  This is
		because the textboxes are set to <strong>Required</strong> while the listbox is not.  And the sample
		<strong>renderWithName()</strong> method has code which will boldface/allcaps the names of any required controls.</p>

	<p>We've also made some changes to the styling and such to the various controls.  Note that you can
		programmatically make these changes in our form definition (in <strong>formCreate()</strong>), and you can
		also make these changes as "Attribute Overrides" in the HTML template itself (see the "Other Tidbits"
		section for more information on <strong>Attribute Overriding</strong>).</p>

	<p>And finally, in our HTML template, we are now using the <strong>renderWithName()</strong> calls.  Because of that,
		we no longer need to hard code the "Value 1" and "Value 2" HTML in the template.</p>
</div>

<div id="demoZone">
	<p><?php $this->txtValue1->renderWithName(); ?></p>

	<p><?php $this->txtValue2->renderWithName(); ?></p>

	<p><?php $this->lstOperation->renderWithName(); ?></p>

	<?php $this->btnCalculate->render(['Width'=>200, 'Height'=>100, 'FontNames'=>'Courier']); ?>
	<hr/>
	<?php $this->lblResult->render(['FontSize'=>20, 'FontItalic'=>true]); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>