<?php use QCubed\QString;
use QCubed\Query\QQ;

require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
	<h1>QQuery and Association Tables (Many-to-Many Relationships)</h1>

	<p>One key feature of <strong>QCubed Query (QQ)</strong> is its ability to treat relationships in Association tables just like
	any other foreign key relationship.  <strong>QQ</strong> has the ability to perform the full set of <strong>QQ</strong> functionality
	(including conditions, expansions, ordering, grouping, etc.) on tables related via association tables
	just as it would on tables related via a direct foreign key.</p>

	<p>Naming standards for the many to many relationship are the same as the naming standards for the public methods
	for associating/unassociating in the class, itself.  So just as <strong>$objPerson->get<span style="text-decoration: underline;">ProjectAsTeamMember</span>Array</strong> will
	retrieve an array of Project objects that are associated to this Person object as a "Team Member",
	<strong>QQN::person()->ProjectAsTeamMember</strong> will refer to the "team_member_project_assn" association table joined against
	the "person" table.</p>

	<p>And again, because all the <strong>\QCubed\Query\QQ Nodes</strong> are linked together, you can go from there to pull the project table, itself, as
	well as any columns from that project table.  In fact, the linkages can go indefinitely.
	<code>QQN::person()->ProjectAsTeamMember->Project->ManagerPerson->FirstName</code> refers to the "first name of the manager
	of any project that this person is a team member of."</p>

	<p>More importantly, when performing <strong>QCubed Queries</strong> across association tables, we can <strong>Expand</strong> on the many-to-many
	relationship, which would use a special virtual attribute to help describe the individual object, itself, which was involved for the join.
	In this case, if we were to do a query of the person table, expanding on any ProjectAsTeamMember objects, the actual project that is joined is available
	to the Person object as $objPerson->_ProjectAsTeamMember.</p>

	<p>And finally, on a similar note, you could instead use <strong>expandAsArray()</strong> which would do a similar expansion
	on the associated object, but store it as an array.  See below for the differences of each.</p>
</div>

<div id="demoZone">
	<h2>Get All People Who Are on a Project Managed by Karen Wolfe (Person ID #7)</h2>
	<ul>
<?php
	$objPersonArray = Person::queryArray(
		QQ::equal(QQN::person()->ProjectAsTeamMember->Project->ManagerPersonId, 7),
		// Because we are doing a join on a many-to-many relationship, we may end up with repeats (e.g. someone
		// who is a team member of more than one project that is managed by karen wolfe).  Therefore, we declare this as DISTINCT
		// to get rid of the redundant entries
		QQ::clause(
			QQ::distinct(),
			QQ::orderBy(QQN::person()->LastName, QQN::person()->FirstName)
		)
	);

	foreach ($objPersonArray as $objPerson) {
		_p('<li>'.$objPerson->FirstName . ' ' . $objPerson->LastName.'</li>', false);
	}
?>
	</ul>

	<h2>Get All People Who Are on a Project Managed by Karen Wolfe (Person ID #7)<br/>showing the Project which is involved in the JOIN via expand()</h2>
	<p><i>Notice how some people may be listed twice, once for each project which he or she is part of that is managed by Karen Wolfe.</i></p>
	<ul>
<?php
	$objPersonArray = Person::queryArray(
		QQ::equal(QQN::person()->ProjectAsTeamMember->Project->ManagerPersonId, 7),
		// Let's expand on the Project, itself
		QQ::clause(
			QQ::expand(QQN::person()->ProjectAsTeamMember->Project),
			QQ::orderBy(QQN::person()->LastName, QQN::person()->FirstName)
		)
	);

	foreach ($objPersonArray as $objPerson) {
		printf('<li>%s %s (via the "%s" project)</li>',
			QString::htmlEntities($objPerson->FirstName),
			QString::htmlEntities($objPerson->LastName),
			// Use the _ProjectAsTeamMember virtual attribute, which gives us the Project object
			QString::htmlEntities($objPerson->_ProjectAsTeamMember->Name));
	}
?>
	</ul>

	<h2>Same as above, but this time, use expandAsArray()</h2>
	<p><i>Notice how each person is only listed once... but each person has an internal/virtual <strong>_ProjectAsTeamMemberArray</strong> which may list more than one project.</i></p>
<?php
	$objPersonArray = Person::queryArray(
		QQ::equal(QQN::person()->ProjectAsTeamMember->Project->ManagerPersonId, 7),
		QQ::clause(
			// Let's ExpandArray on the Association Table, itself
			QQ::expandAsArray(QQN::person()->ProjectAsTeamMember),
			// ExpandArray dictates that the PRIMARY sort MUST be on the root object (in this case, QQN::person())
			// Any secondary sort can follow
			QQ::orderBy(QQN::person()->LastName, QQN::person()->FirstName, QQN::person()->ProjectAsTeamMember->Project->Name)
		)
	);

	foreach ($objPersonArray as $objPerson) {
		_p('<li>'.$objPerson->FirstName . ' ' . $objPerson->LastName, false);

		// Now, instead of using the _ProjectAsTeamMember virtual attribute, we will use
		// the _ProjectAsTeamMemberArray virtual attribute, which gives us an array of Project objects
		$strProjectNameArray = array();
		foreach ($objPerson->_ProjectAsTeamMemberArray as $objProject){
			array_push($strProjectNameArray, QString::htmlEntities($objProject->Name));
		}
		printf(' via: %s</li>', implode(', ', $strProjectNameArray));
	}
?>
</div>

<?php require('../includes/footer.inc.php'); ?>