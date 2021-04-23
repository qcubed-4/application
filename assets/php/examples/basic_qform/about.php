<?php require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions" class="full">
	<h1>About Sections 4 - 10</h1>

	<p>Sections 4 through 10 focus on the <strong>Form</strong> and the <strong>Control</strong> libraries.  In order
		to focus on just the view and controller layer functionality that Forms offers, the concepts of
		code generated objects and components are only discussed minimally.</p>

	<p>Any examples that utilize code generated objects (which subsequently is using the database)
		will be marked with a "*".  If you would like more information on how these objects are
		generated or how they work, we recommend that you check out sections 1 - 3 of the examples.</p>

    <h2>A Note about JQuery</h2>
    <p>
    QCubed currently requires the <a href="http://jquery.com">JQuery</a> javascript library to function.
    JQuery is a powerful addition to Javascript, makes it easy for us to implement the connection between the browser and the server,
    supports many browser versions and is well maintained. </p>
    <p>We put jQuery into a compatability mode to attempt to prevent it from clashing with other javascript libararies that
        developers might use. In particular, instead of the $ mapping to the jQuery library, we map $j to jQuery. In other
        words, if you are writing JQuery code, substitute $j any time you would normally use $.
    </p>
    <p>Also, Bootstrap is known to have some minor incompatibilties with jQuery UI. Our Bootstrap library implements
        a small javascript shim in order to fix these incompatibilities, so Bootstrap and JQuery UI should coexist just fine.
    </p>


</div>

<style>#viewSource { display: none; }</style>

<?php require('../includes/footer.inc.php'); ?>