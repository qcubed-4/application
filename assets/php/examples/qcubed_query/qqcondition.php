<?php use QCubed\Query\QQ;

require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

	<div id="instructions">
		<h1>QCubed Query Conditions</h1>
		
		<p>All <strong>QCubed Query</strong> method calls require a <strong>Condition</strong>. <strong>Conditions</strong> allow you
		to create a nested/hierarchical set of conditions to describe what essentially becomes your
		WHERE clause in a SQL query statement.</p>

		<p>The following is the list of Condition classes and what parameters they take:</p>
		<ul>
			<li>QQ::all()</li>
			<li>QQ::none()</li>
			<li>QQ::equal(NodeBase, Value)</li>
			<li>QQ::notEqual(NodeBase, Value)</li>
			<li>QQ::greaterThan(NodeBase, Value)</li>
			<li>QQ::lessThan(NodeBase, Value)</li>
			<li>QQ::greaterOrEqual(NodeBase, Value)</li>
			<li>QQ::lessOrEqual(NodeBase, Value)</li>
			<li>QQ::isNull(NodeBase)</li>
			<li>QQ::isNotNull(NodeBase)</li>
			<li>QQ::in(NodeBase, array of string/int/datetime)</li>
			<li>QQ::like(NodeBase, string)</li>
		</ul>
		
		<p>For almost all of the above <strong>Conditions</strong>, you are comparing a column with some value.  The <strong>Node</strong> parameter
		represents that column.  However, the value can be either a static value (like an integer, a string, a datetime, etc.)
		<i>or</i> it can be another <strong>Node</strong>.</p>
		
		<p>And finally, there are three special <strong>Condition</strong> classes which take in any number of additional <strong>Condition</strong> classes:</p>
		<ul>
			<li>QQ::andCondition()</li>
			<li>QQ::orCondition()</li>
			<li>QQ::not() - "Not" can only take in one <strong>Condition</strong> class</li>
		</ul>
		<p>(conditions can be passed in as parameters and/or as arrays)</p>
		
		<p>Because And/Or/Not conditions can take in <em>any</em> other condition, including other And/Or/Not conditions, you can
		embed these conditions into other conditions to create what ends up being a logic tree for your entire SQL Where clause.  See
		below for more information on this.</p>
	</div>

<div id="demoZone">
	<h2>Select all People where: the first name is alphabetically "greater than" the last name</h2>
	<ul>
<?php
	$objPersonArray = Person::queryArray(
		// Notice how we are comparing to QQ Column Nodes together
		QQ::greaterThan(QQN::person()->FirstName, QQN::person()->LastName)
	);

	foreach ($objPersonArray as $objPerson){
		_p('<li>'.$objPerson->FirstName . ' ' . $objPerson->LastName.'</li>', false);
	}
?>
	</ul>
	<h2>Select all Projects where: the manager's first name is alphabetically "greater than" the last name, or who's name contains "Website"</h2>
	<ul>
<?php
	$objProjectArray = Project::queryArray(
		QQ::orCondition(
			QQ::greaterThan(QQN::project()->ManagerPerson->FirstName, QQN::project()->ManagerPerson->LastName),
			QQ::like(QQN::project()->Name, '%Website%')
		)
	);

	foreach ($objProjectArray as $objProject) {
		_p(sprintf('<li>%s (managed by %s %s)</li>', $objProject->Name, $objProject->ManagerPerson->FirstName, $objProject->ManagerPerson->LastName), false);
	}
?>
	</ul>
	<h2>Select all Projects where: the Project ID <= 2 AND (the manager's first name is alphabetically "greater than" the last name, or who's name contains "Website")</h2>
	<ul>
<?php
	$objProjectArray = Project::queryArray(
		QQ::andCondition(
			QQ::orCondition(
				QQ::greaterThan(QQN::project()->ManagerPerson->FirstName, QQN::project()->ManagerPerson->LastName),
				QQ::like(QQN::project()->Name, '%Website%')
			),
			QQ::lessOrEqual(QQN::project()->Id, 2)
		)
	);

	foreach ($objProjectArray as $objProject) {
		_p(sprintf('<li>%s (managed by %s %s)</li>', $objProject->Name, $objProject->ManagerPerson->FirstName, $objProject->ManagerPerson->LastName), false);
	}
?>
	</ul>
</div>

<?php require('../includes/footer.inc.php'); ?>