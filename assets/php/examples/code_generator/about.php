<?php require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions" class="full">
	<h1>About Sections 1 - 3</h1>

	<p>Sections 1 through 3 are dedicated to just the <strong>Code Generator</strong>.  In order
		to focus on just the code generated functionality, no <strong>Form</strong> or <strong>Control</strong>
		components are included in these examples. Normally, a QCubed application has these, and they will be
        covered in later sections.
    </p>

	<p>In order to illustrate what is going on in these objects, many of the examples will
		be printing/outputting data directly from the objects themselves.
		If you <strong>View Source</strong> to view the PHP source on any of these examples, you will note
		that these scripts will have inline PHP calls throughout the HTML. Just know that this is not normally
        how a QCubed application is written, so do not follow these as examples of how to write your own software.
        Just note that
		this is done here for purposes of demonstrating the <strong>Object Relational Model</strong> <em class="warning">only</em>.</p>

	<p>For more information on how to better architect the control and view layers of a QCubed-based
		application, we recommend you check out sections 4 - 10 of the examples.</p>
</div>

<style>#viewSource { display: none; }</style>

<?php require('../includes/footer.inc.php'); ?>