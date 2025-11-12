<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Defining Drop Zones</h1>

	<p><strong>Controls</strong> can be Droppable, meaning that certain events will get triggered when a
		Moveable object is dropped on to it.</p>

	<p>You can set up a moveable control to revert to its original position after it is
		dropped. You can also tell it to revert only when dropped onto a Droppable control,
		or revert when it is NOT dropped on a Droppable control.</p>
</div>

<div id="demoZone">
<?php $this->pnlDropZone1->render(); ?>
<?php $this->pnlDropZone2->render(); ?>
<?php $this->pnlPanel->render(); ?>
</div>
<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>