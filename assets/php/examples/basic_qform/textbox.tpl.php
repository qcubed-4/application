<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

	<div id="instructions">
		<h1>The TextBox Family of Controls</h1>

		<p><strong>TextBox</strong> controls handle basic user input.  Different flavors of controls
			are available for various forms of user input.</p>

        <p>The last controls, <strong>Email</strong>, <strong>URL</strong>, and <strong>Custom</strong>, are based on validation
            and filtering routines introduced in PHP 5.3. The custom field uses PHP's built-in ability to validate against
            a Perl regular expression to accept only hexadecimal numbers.</p>

        <h3>Multiple Email Entry and Flexible Separators</h3>

        <p>The EmailTextBox control now allows entering several email addresses at once. Addresses can be separated
            by a comma, semicolon, spaces, or line breaks, in any combination. The control automatically processes,
            trims and validates each address, grouping them as valid or invalid.</p>
        <p><strong>How it works</strong></p>
        <p>The control uses a regular expression to split the entered text. <br />Supported separators are:</p>
        <ul>
            <li>comma</li>
            <li>semicolon</li>
            <li>any whitespace (spaces, tabs, line breaks)</li>
        </ul>
        <p>Developers can access the result using the new `getGroupedEmails()` method, which provides two arrays:</p>
        <ul>
            <li>Valid: All recognized and valid e-mail addresses.</li>
            <li>Invalid: Any entries that failed validation</li>
        </ul>
        <p>This allows you to display custom warnings, prevent submission if there are errors, or handle inputs as needed
            before saving to your database.</p>
	</div>

	<div id="demoZone">
		<p>Basic (limited to 5 chars): <?php $this->txtBasic->renderWithError(); ?></p>
		<p>Integer (max value of 10): <?php $this->txtInt->renderWithError(); ?></p>
		<p>Float: <?php $this->txtFlt->renderWithError(); ?></p>
		<p>List (2–5 comma-separated items): <?php $this->txtList->renderWithError(); ?></p>
		<p>Email: <?php $this->txtEmail->renderWithError(); ?></p>
		<p>Url: <?php $this->txtUrl->renderWithError(); ?></p>
		<p>Custom (Only hex): <?php $this->txtCustom->renderWithError(); ?></p>
		<p><?php $this->btnValidate->render(); ?></p>
        <p>Multiple Emails: <?php $this->txtMultipleEmails->render(); ?></p>
        <p>Validate: <?php $this->btnEmailValidate->render(); ?></p>
	</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>