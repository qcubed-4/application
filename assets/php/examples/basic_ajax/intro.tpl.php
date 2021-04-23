<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Hello World, Revisited</h1>

	<p>This example revisits our original Hello World example to show how you can easily
	change a postback-based form and interactions into AJAX-postback based ones.</p>

	<p>Whereas before, we executed a <strong>Server</strong> on the button's click, we have now changed
	that to a <strong>Ajax</strong>.  Everything else remains the same.</p>

	<p>The result is the exact same interaction, but now performed Asynchronously via Ajax.  Note
	that after clicking the button, the entire page doesn't "refresh" -- but the label's content
	changes as defined in the PHP method <strong>btnButton_Click</strong>.</p>
</div>

<div id="demoZone">
	<p><?php $this->lblMessage->render(); ?></p>
	<p><?php $this->btnButton->render(); ?></p>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>