<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Adding Pagination to Your DataGrid</h1>

	<p>The <strong>Paginator</strong> is a control that presents a list of page numbers, and a previous and next button,
		to let the user "scroll" the data a page at a time. It gives a limited view into a potentially very large
	data set.</p>

	<p>In order to enable pagination, we need to define a <strong>Paginator</strong> object and assign it to
		the <strong>DataGrid</strong>. The <strong>DataGrid</strong> will render the paginator inside a
		caption tag in the table, and therefore, we will set the <strong>DataGridw</strong> as the <strong>Paginator</strong>'s
		parent in the <strong>Paginator</strong> constructor call.</p>

	<p>In the locally defined <strong>dtgPersons_Bind</strong> method, in addition to setting the datagrid's <strong>DataSource</strong>,
		we also give the datagrid the <strong>TotalItemCount</strong> (via a <strong>Person::countAll()</strong> call).
		And finally, when we make the <strong>Person::loadAll()</strong> call, we make sure to
		pass in the datagrid's <strong>LimitClause</strong>, which will pass the paging information
		into our <strong>loadAll()</strong> call to only retrieve the items on the page we are
		currently viewing.</p>
</div>

<div id="demoZone">
	<?php $this->dtgPersons->render(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>