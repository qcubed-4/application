<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
    <h1>Run arbitrary JavaScript, notifications, and confirmations</h1>
	
	<p>QCubed-4 includes several commonly used Javascript-based actions:</p>
	<ul>
		<li><b>Alert—</b>to display a JavaScript "alert" type of dialog box</li>
		<li><b>Confirm—</b>to display a JavaScript "confirm" type of dialog box and execute the following optional actions if the user hits "Ok"</li>
		<li><b>JavaScript—</b>to run any arbitrary javaScript command(s)</li>
	</ul>
	
	<p>This example shows three different <b>Button</b> controls which use all three of these action types.</p>
	
	<p>Specifically for the <b>JavaScript</b>, we've defined a simple <b>SomeArbitraryJavaScript()</b>
	javascript function on the page itself, so that the button has some JavaScript to perform.</p>
	
	<p>If you are interested in more advanced and flexible types of confirmation or prompts, see the examples at
		<a href="../advanced_ajax/dialog_box.php">Extending Panels to Create Modal "Dialog Boxes"</a>.
	</p>
</div>

<div id="demoZone">
	<p><?php $this->btnAlert->render(); ?></p>
	<p><?php $this->btnConfirm->render(); ?></p>
	<p><?php $this->btnJavaScript->render(); ?></p>
	<p><?php $this->lblMessage->render(); ?></p>
	
	<script type="text/javascript">
		function SomeArbitraryJavaScript() {
            const strName = prompt('What is your name?');
            if (strName){ alert('Hello, ' + strName + '!'); }
		}
	</script>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>