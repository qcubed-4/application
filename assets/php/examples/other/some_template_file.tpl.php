<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Hello World, Revisited... Again...</h1>

    <p>By default, the <strong>Form</strong> engine adds a <strong>.tpl</strong> to the PHP script file path, which is used as the template file path.
        For example, in the first example, the script with the form definition was named <strong>intro.php</strong>.
        Therefore, QCubed-4 used <strong>intro.tpl.php</strong> by default, since the HTML template
        includes the file ("tpl" indicates that it is an HTML template).</p>

    <p>There are several reasons why you might want to use a different file name or even specify a completely different file path.
        In fact, the QCubed-4 code generator does this when it generates the form draft template files in a separate folder
        from the form drafts themselves.</p>

	<p>The <strong>FormBase::run</strong> method takes in an optional second parameter where you can specify the exact
		filepath of the template file you wish to use, overriding the default "script_name.tpl.php".</p>
</div>

<div id="demoZone">
	<?php
	// We will override some visual attributes of the controls here -
	// the ForeColor, FontBold and the FontSize.
	?>
	<p><?php $this->lblMessage->Render('ForeColor=red', 'FontBold=true'); ?></p>
	<p><?php $this->btnButton->Render('FontSize=20'); ?></p>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>