<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>The DataGrid Class and Sorting by Columns</h1>

	<p><strong>Table</strong>, is great for drawing basic HTML tables, but can overload a web page
		when trying to display a large amount of information.</p>

	<p>The <strong>DataGrid</strong> class is a subclass of Table and has features that allow the table
		to be a view into a larger set of data that is stored in a database, without rendering all the data. The effect
		gives the user fast access to large amounts of data.</p>

	<p>One feature of <strong>DataGrid</strong> is it allows you to click on column headers to sort the data in the table.
		Sorting is provided by SQL, and each click causes the database to be queried with a sort clause corresponding to the
		column that was clicked.
	</p>

	<p>To enable this, you add the properties <strong>OrderByClause</strong> and <strong>ReverseOrderByClause</strong> to each column.
		(It is possible
		to also just define <strong>OrderByClause</strong>, and to leave <strong>ReverseOrderByClause</strong> undefined).  The <strong>QQ Clause</strong>
		you specify is given back to you when you call the <strong>OrderByClause</strong> property on the <strong>DataGrid</strong>
		when you query the database in the data binder. You then pass this clause to your class's <strong>loadAll()</strong> or <strong>loadArrayBy...()</strong>
		method as one of the optional <strong>QQ Clause</strong> parameters.  Note that all QCubed code generated <strong>loadAll()</strong> and <strong>loadArrayBy...()</strong>
		methods take in an optional <strong>$objOptionalClauses</strong> parameter which conveniently uses the clause returned by the <strong>DataGrid</strong>'s
		<strong>OrderByClause</strong> method.</p>

	<p>Convenient how they end up working together, isn't it? =)</p>

	<p>Click on the column headers in the table to see the sorting in action.</p>
</div>

<div id="demoZone">
	<?php $this->dtgPersons->Render(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>