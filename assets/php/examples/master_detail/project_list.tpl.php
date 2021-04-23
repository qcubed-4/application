<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Nested DataGrid - Drilling Into a Dataset</h1>
	<p>In this example, we will demonstrate how to create a nested <strong>DataGrid</strong>.</p>

	<p>In a top-level grid, we will list projects; for each of the projects, and through an expand/collapse button,
	we'll see the list of team members involved in the project. For each of those people, we'll be able to drill down to
	the list of addresses that we have on file.</p>

	<p>Moreover, you can enable inline editing for any of these datagrids - as demostrated in the example below
	for the addresses. If you want to learn the basics of inline editing for datagrids, go through <a href='../dynamic/inline_editing.php'>this example</a>.</p>

	<p>Some pieces to pay attention to:</p>
	<ul>
		<li>Master DataGrid for Project should go on the FORM.</li>
		<li>Children (Team Members and Addresses) must be wrapped in a Panel, that in turn contains a DataGrid.</li>
	</ul>
</div>

<div id="demoZone"><?php $this->dtgProjects->Render(); ?></div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>