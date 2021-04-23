<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

	<div id="instructions">
		<h1>The TextBox Family of Controls</h1>

		<p><strong>TextBox</strong> controls handle basic user input.  Different flavors of controls
			are available for various forms of user input.</p>

		<p>The last few controls, the <strong>Email</strong>, <strong>Url</strong> and <strong>Custom</strong>
			items are based on the validation and filter routines introduced in PHP 5.3. The Custom field
			uses PHPs built-in ability to validate based on a Perl regular expression to only accept hex numbers.</p>


	</div>

	<div id="demoZone">
		<p>Basic (limited to 5 chars): <?php $this->txtBasic->renderWithError(); ?></p>
		<p>Integer (max value of 10): <?php $this->txtInt->renderWithError(); ?></p>
		<p>Float: <?php $this->txtFlt->renderWithError(); ?></p>
		<p>List (2 - 5 comma-separated items): <?php $this->txtList->renderWithError(); ?></p>
		<p>Email: <?php $this->txtEmail->renderWithError(); ?></p>
		<p>Url: <?php $this->txtUrl->renderWithError(); ?></p>
		<p>Custom (Only hex): <?php $this->txtCustom->renderWithError(); ?></p>
		<p><?php $this->btnValidate->render(); ?></p>
	</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>