<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Handling "Multiple Forms" on the Same Page</h1>

    <p style="color: red;">NOTE: Due to a complete and thorough update of the framework to the PHP 8.3+ standard, this and a single example
        from the others do not function correctly.</p>

    <p style="color: red;">This example will be updated with the new logic sooner or later!</p>

	<p>QCubed-4 only allows each front-end "web page" to only have a maximum of one <strong>Form</strong> class per a page.  Because of
		the many issues of managing and maintaining formstate across multiple <strong>Forms</strong>, QCubed-4 simply does not allow
		for the ability to have multiple <strong>Forms</strong> per a page.</p>

	<p>However, as the development of a QCubed-4 application matures, developers may find themselves wishing for this ability:</p>
	<ul>
		<li>As <strong>Forms</strong> are initially developed for simple, single-step tasks (e.g. "Post a Comment," "Edit a Project's Name," etc.),
			developers may want to be able to combine these simpler QForms together onto a single, larger, more cohesive Form,
			utilizing AJAX to provide for a more "Single-Page Web Application" type of architecture.</li>
		<li>Moreover, developers may end up with a library of these <strong>Forms</strong> that they would want to reuse in multiple locations,
			thus allowing for a much better, more modularized codebase.</li>
	</ul>

	<p>Fortunately, the <strong>Panel</strong> control was specifically designed to provide this kind of "Multiple <strong>Form</strong>" functionality.
		In the example below, we create a couple of custom <strong>Panels</strong> to help with the viewing and editing of a Project and its team members.  The
		comments in each of these custom controls explain how a custom <strong>Panel</strong> provides similar functionality to an independent, stand-alone
		<strong>Form</strong>, but also detail the small differences in how the certain events need to be coded.</p>

	<p>Next, to illustrate this point further, we create a <strong>PersonEditPanel</strong>, which is based on the code generated
		<strong>PersonEditFormBase</strong> class.</p>

	<p>Finally, we use a few <strong>Ajax</strong> and <strong>AjaxControl</strong> to tie them all together into a single-page web application.</p>
</div>

<div id="demoZone">
	<h2>View/Edit Example: Projects and Memberships</h2>

	<p>Please Select a Project: <?php $this->lstProjects->render(); ?> &nbsp;&nbsp; <?php $this->objDefaultWaitIcon->render(); ?></p>
	<?php $this->pnlLeft->render(); ?>
	<?php $this->pnlRight->render(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>