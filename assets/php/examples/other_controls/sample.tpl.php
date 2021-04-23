<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Creating Your Own Control</h1>

	<p>Many developers may want to create their own, custom Control to perform a very specific interaction.
	Alternatively, developers may wish to utilize exernal JavaScript libraries like Dojo, Yahoo! YUI, etc.
	to create their own set of Controls.</p>

	<p>Whatever the case may be, QCubed makes it easy to implement custom controls, complete with javascript
	input and output hooks, within the QControl architecture.</p>

	<p>If possible, subclass your custom controls from similar controls in the core that are already created.
        Or, use controls that others have developed as a model for your own. See the Jqui library of controls
        for an example of creating JQuery based controls, and the Bootstrap library for an example of
        Twitter Bootstrap based controls. The Bootstrap library is also a good example of how to create a control
        that is installable using Composer and that is easily shared with other developers.
    </p>
</div>

<div id="demoZone">
	<style type="text/css">
		.image_canvas {border-width: 4px; border-style: solid; border-color: #a9f;}
	</style>
	<?php $this->ctlCustom->render(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>