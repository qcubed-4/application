<?php use QCubed\QString;

require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
	<h1>Virtual Attributes and Custom SQL</h1>

	<p> When generating custom SQL, if you prefix any additional or non-table bound columns with a double-underscore ("__"), the
	generated object will read in the column as a virtual attribute.  You can then use the generated
	<strong>getVirtualAttribute()</strong> method to retrieve the value of this data.</p>

	<p>In our example here, we create a custom SQL query which uses SQL's <strong>COUNT</strong> function and
	subselects to calculate the number of team member for each project.</p>

	<p>By utilizing <strong>Virtual Attributes</strong>, complex queries with calculated values,
	subselects, etc. can be retrieved in a single database query, and all the values can be
	stored in the data object, itself.</p>
</div>

<div id="demoZone">
<?php
	// Let's Define the Query
	$strQuery =
		'SELECT
			project.*,
			(
				SELECT
					COUNT(*)
				FROM
					team_member_project_assn
				WHERE
					project_id = project.id
			) AS __team_member_count
		FROM
			project';

	// Get the Database object from the Project table
	$objDatabase = Project::GetDatabase();
?>
	<h2>List All Projects and its Team Member Count</h2>
<?php

	// Query() the Database and Instantiate on the ResultSet into a Project[] array
	$objProjectArray = Project::instantiateDbResult($objDatabase->Query($strQuery));

	// Iterate through the Project array
	foreach ($objProjectArray as $objProject) {
		_p(QString::htmlEntities($objProject->Name) . ' has ' . $objProject->getVirtualAttribute('team_member_count') . ' team members.');
		_p('<br/>', false);
	}
?>
</div>

<?php require('../includes/footer.inc.php'); ?>