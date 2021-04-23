<?php use QCubed\Query\QQ;

require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
	<h1>QCubed Query Clauses</h1>

	<p>All <strong>QCubed Query</strong> method calls take in an optional set of <strong>Clauses</strong>. <strong>Clauses</strong> let you
	alter the result set by performing the equivalents of most of your major SQL clauses, including JOIN, ORDER BY,
	GROUP BY and DISTINCT.</p>

	<p>The following is the list of Clause classes and what parameters they take:</p>
	<ul>
		<li>QQ::orderBy(array/list of Nodes or Conditions)</li>
		<li>QQ::groupBy(array/list of Nodes)</li>
		<li>QQ::having(QQSubSqlNode)</li>
		<li>QQ::count(NodeBase, string)</li>
		<li>QQ::minimum(NodeBase, string)</li>
		<li>QQ::maximum(NodeBase, string)</li>
		<li>QQ::average(NodeBase, string)</li>
		<li>QQ::sum(NodeBase, string)</li>
		<li>QQ::expand(NodeBase)</li>
		<li>QQ::expandAsArray(NodeBase for an Association Table)</li>
		<li>QQ::limitInfo(integer[, integer = 0])</li>
		<li>QQ::distinct()</li>
	</ul>

	<p><strong>orderBy()</strong> and <strong>groupBy()</strong> follow the conventions of SQL ORDER BY and GROUP BY.  It takes in a
	list of one or more <strong>Column Nodes</strong>. This list could be a parameterized list and/or an array.</p>

	<p>Specifically for <strong>orderBy()</strong>, to specify a <strong>Node</strong> that you wish to order by in descending
	order, add a "false" after the Node.  So for example, <strong>QQ::orderBy(QQN::person()->LastName, false,
	QQN::person()->FirstName)</strong> will do the SQL equivalent of "ORDER BY last_name DESC, first_name ASC".</p>

	<p><strong>Count</strong>, <strong>Minimum</strong>, <strong>Maximum </strong>, <strong>Average</strong> and <strong>Sum</strong> are aggregation-related clauses, and
	only work when <strong>groupBy()</strong> is specified.  These methods take in an attribute name, which
	can then be retrieved using <strong>getVirtualAttribute()</strong> on the object.</p>

	<p><strong>having()</strong> adds a SQL Having clause, which allows you to filter the results of your query based
	on the results of the aggregation-related functions. <strong>having()</strong> requires a Subquery, which is a SQL code
	snippet you create to specify the criteria to filter on. (See the Subquery section
	later in this tutorial for more information on those).</p>

	<p><strong>expand()</strong> and <strong>exapandAsArray()</strong> deal with Object Expansion / Early Binding.  More on this
	can be seen in the <a href="../more_codegen/early_bind.php">Early Binding of Related Objects example</a>.</p>

	<p><strong>limitInfo()</strong> will limit the result set.  The first integer is the maximum number of rows
	you wish to limit the query to.  The <em>optional</em> second integer is the offset (if any).</p>

	<p><strong>Distinct</strong> will cause the query to be called with SELECT DISTINCT.</p>
</div>

<div id="demoZone">
	<h2>Select all People, Ordered by Last Name then First Name</h2>
	<p><em>Note now QQ::OrderBy gets two parameters here</em></p>
	<ul>
<?php
	$objPersonArray = Person::queryArray(
		QQ::all(),
		[
			QQ::orderBy(QQN::person()->LastName, QQN::person()->FirstName)
		]
	);

	foreach ($objPersonArray as $objPerson) {
		_p('<li>'.$objPerson->FirstName . ' ' . $objPerson->LastName.'</li>', false);
	}
?>
	</ul>
	<h2>Select all People, Ordered by Last Name then First Name, Limited to the first 4 results</h2>
	<p><em>Combining QQ::orderBy and QQ::limitInfo</em></p>
	<ul>
<?php
	$objPersonArray = Person::queryArray(
		QQ::all(),
		[
			QQ::orderBy(QQN::person()->LastName, QQN::person()->FirstName),
			QQ::limitInfo(4)
		]
	);

	foreach ($objPersonArray as $objPerson) {
		_p('<li>'.$objPerson->FirstName . ' ' . $objPerson->LastName.'</li>', false);
	}
?>
	</ul>
	<h2>Select all People, those with last name Smith first, then ordered by First Name</h2>
	<p><em>Using a QQ::condition as an ORDER BY clause</em></p>
	<ul>
<?php
	$objPersonArray = Person::queryArray(
		QQ::all(),
		[
			QQ::orderBy(QQ::notEqual(QQN::person()->LastName, 'Smith'), QQN::person()->FirstName)
		]
	);

	foreach ($objPersonArray as $objPerson) {
		_p('<li>'.$objPerson->FirstName . ' ' . $objPerson->LastName.'</li>', false);
	}
?>
	</ul>
	<h2>Select all Projects and the Count of Team Members (if applicable)</h2>
	<p><em>GROUP BY in action</em></p>
	<ul>
<?php
	$objProjectArray = Project::queryArray(
		QQ::all(),
		[
			QQ::groupBy(QQN::project()->Id),
			QQ::count(QQN::project()->PersonAsTeamMember->PersonId, 'team_member_count')
		]
	);

	foreach ($objProjectArray as $objProject) {
		_p('<li>'.$objProject->Name . ' (' . $objProject->getVirtualAttribute('team_member_count') . ' team members)'.'</li>', false);
	}
?>
	</ul>

	<h2>Select all Projects with more than 5 team members. </h2>
	<p><em>Using a Having clause to further limit group functions</em></p>
	<ul>
<?php
	$objProjectArray = Project::queryArray(
		QQ::all(),
		[
			QQ::groupBy(QQN::project()->Id),
			QQ::count(QQN::project()->PersonAsTeamMember->PersonId, 'team_member_count'),
			QQ::having(QQ::subSql('COUNT({1}) > 5', QQN::project()->PersonAsTeamMember->PersonId))
		]
	);

	foreach ($objProjectArray as $objProject) {
		_p($objProject->Name . ' (' . $objProject->getVirtualAttribute('team_member_count') . ' team members)');
		_p('<br/>', false);
	}
?>
	</ul>
</div>


<?php require('../includes/footer.inc.php'); ?>
