<?php require('../includes/header.inc.php'); ?>
<style>
	.header-row {
		color: #780000;
		background-color: #ffffff;
		font-size: 12pt;
	}
	.row {
		background-color: #efefff;
		font-size: 12pt;
	}
	.alt-row {
		background-color: #ffffff;
		font-size: 12pt;
	}
</style>

<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>An Introduction to the Table Class</h1>

	<p>The <strong>Table</strong> control is used to present a collection of objects or data in a grid-based
		(e.g. &lt;table&gt;) format.  All <strong>Table</strong> objects take in a <strong>DataSource</strong>, which can be an array
		of anything (or in our example, an array of Person objects).</p>

	<p>When creating a <strong>Table</strong>, you must create a table column for each column in your table.
		For each <strong>Column</strong> type you specify its name and how it should be rendered.
		In our example below, we create a <strong>CallableColumn</strong> column, which takes a
		PHP callable type, and lets you define a callback that will return the text of each cell in the column. The
		callback will be called repeatedly for each row in the table, and each time will be passed the data for the row
		it is to draw.

	<p>The <strong>Table</strong>'s style is fully customizable, at both the column level and the row level.
		You can style the whole table with css, or specify classes for individual parts of the table. You can even specify
		how individual cells are drawn.</p>
</div>

<div id="demoZone">
	<?php $this->dtgPersons->render(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>