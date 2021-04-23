<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Making Events Conditional</h1>

	<p>Sometimes we want events to trigger conditionally. Given our editable listbox, a good example
	of this is that we want the submitting of the new Item to only happen if the user has
	typed in something in the textbox.</p>

	<p>Basically, if the textbox is blank, no event should trigger.  (You can verify this now by
	clicking "Add Item" without while keeping the textbox completely blank.)</p>

	<p>QCubed supports this by allowing all events to have optional conditions. These conditions
	are written as custom javascript code into the Event constructor itself.</p>

	<p>In this example, we explicitly name the textbox's ControlId as "txtItem" so that we can
	write custom javascript as conditionals to the button's <strong>Click</strong> and the textbox's
	<strong>EnterKey</strong>.</p>
</div>

<div id="demoZone">
	<?php $this->lstListbox->renderWithName(); ?>

	<?php $this->txtItem->renderWithName(); ?>

	<?php $this->btnAdd->render(); ?>

	<?php $this->lblSelected->renderWithName(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>