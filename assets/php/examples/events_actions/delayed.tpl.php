<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Triggering Events after a Delay</h1>
	
	<p>Sometimes, you may want events to trigger their assigned actions after a delay.  A good example
	of this here is the <strong>KeyPress</strong> we added below.  As the user enters in data into the textbox,
	we make an AJAX call to update the label.  However, in order to make the system a bit more usable
	and streamlined, we have added a half-second (500 ms) delay on the <strong>KeyPress</strong>, so that
	we are not making too many AJAX calls as the user is still entering in data.</p>
	
	<p>Basically, this allows the action to be triggered only after the user is done typing in the data.</p>

	<p>Note that we maybe could have used a Change on the textbox to achieve a similar effect.  But
	realize that Change (which utilizes a JavaScript <strong>onchange</strong> event handler) will only be
	triggered after the control <em>loses focus</em> and has been changed—it won't be triggered purely
	by the fact that the text in the textbox has been changed.</p>
</div>

<div id="demoZone">
	<p><?php $this->txtItem->renderWithName(); ?></p>

	<p><?php $this->lblSelected->renderWithName(); ?></p>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>