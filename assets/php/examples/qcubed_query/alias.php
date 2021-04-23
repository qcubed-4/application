<?php use QCubed\Query\QQ;

require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
	<h1>SQL Aliases for QQuery</h1>

	<p>Up until now, we've only described doing simple joins with QQuery, where each
	table is only needed for a single related record. But what about when you need
	to check on multiple entries in the same joined table. In standard SQL, you
	would join the table a second time, giving it a new name. And that's exactly
	how you do it in QQuery as well, using <strong>QQ::alias()</strong>.</p>

	<p>In the example 1 below, we need to find who belongs to both the ACME Website
	Redesign and the State College HR System projects. We do this simply by joining
	the membership association table twice, each time with a different alias. This
	allows us to create a condition that says that one associated project has an
	ID of 1, while a <em>different</em> associated project also has an ID of 2.</p>

	<p>In a slightly more complex example 2 below, we are looking for all projects that
	are associated with two other projects (each is specified by name). We use the
	same technique with <b>QQ::alias()</b> as in example 1, except that we now
	mix it in with relationships expanded to other tables.</p>
</div>

<div id="demoZone">
	<h2>Example 1: Project members whose are in both project 1 and 2</h2>
<?php
    Person::getDatabase()->enableProfiling();

	$objPersonArray = Person::queryArray(
		QQ::andCondition(
				QQ::equal(QQ::alias(QQN::person()->ProjectAsTeamMember, 'pm1')->ProjectId, 1),
				QQ::equal(QQ::alias(QQN::person()->ProjectAsTeamMember, 'pm2')->ProjectId, 2)
			)
		);

	foreach ($objPersonArray as $objPerson){
		_p($objPerson->FirstName . ' ' . $objPerson->LastName);
		_p('<br/>', false);
	}
?>

	<h2>Example 2: Projects that are related to both 'Blueman Industrial Site Architecture' and 'ACME Payment System' projects</h2>
<?php
	$objProjectArray = Project::queryArray(
		QQ::andCondition(
				QQ::equal(QQ::alias(QQN::project()->ProjectAsRelated, 'related1')->Project->Name, 'Blueman Industrial Site Architecture'),
				QQ::equal(QQ::alias(QQN::project()->ProjectAsRelated, 'related2')->Project->Name, 'ACME Payment System')
			)
		);

	foreach ($objProjectArray as $objProject){
		_p($objProject->Name . " (" . $objProject->Description . ")");
		_p('<br/>', false);
	}
?>

	<h2>Example 3: Managers having one least a project with a conson milestone, and for each manager, the first voyel milestone and the first conson one</h2>
<?php
	$emptySelect = QQ::select();
	$emptySelect->setSkipPrimaryKey(true);
	$nVoyel = QQ::alias(QQN::person()->ProjectAsManager->Milestone, 'voyel');
	$nConson = QQ::alias(QQN::person()->ProjectAsManager->Milestone, 'conson');
	$objPersonArray = Person::queryArray(
		QQ::isNotNull($nConson->Id),
		QQ::clause(
			QQ::expand(QQN::person()->ProjectAsManager, null, $emptySelect),
			QQ::expand($nVoyel, QQ::in($nVoyel->Name, array('Milestone A', 'Milestone E', 'Milestone I')), $emptySelect),
			QQ::expand($nConson, QQ::notIn($nConson->Name, array('Milestone A', 'Milestone E', 'Milestone I')), $emptySelect),
			QQ::groupBy(QQN::person()->Id),
			QQ::minimum($nVoyel->Name, 'min_voyel'),
			QQ::minimum($nConson->Name, 'min_conson'),
			//*** only needed in PG-SQL.
			// Even with an empty select, id is selected;
			// Happily, PG doesn't complain if both id and MIN(id) are selected
			QQ::expand(QQN::person()->ProjectAsManager, null, $emptySelect),
			QQ::minimum(QQN::person()->ProjectAsManager->Id, 'dummy'),
			//***
			QQ::select(
				QQN::person()->FirstName,
				QQN::person()->LastName
			)
		)
	);

	foreach ($objPersonArray as $objManager){
		_p($objManager->FirstName.' '.$objManager->LastName. " (" . $objManager->getVirtualAttribute('min_voyel').', '. $objManager->getVirtualAttribute('min_conson'). ")");
		_p('<br/>', false);
	}
?>

	<h2>Example 4: Projects with, for each one, the "min" city from the addresses containing 'r' and the "min" city from the addresses NOT containing 'r' </h2>
<?php
	$nWithR = QQ::alias(QQN::project()->PersonAsTeamMember->Person->Address, 'with_r');
	$nWithoutR = QQ::alias(QQN::project()->PersonAsTeamMember->Person->Address, 'without_r');
	$objProjectArray = Project::queryArray(
		QQ::all(),
		QQ::clause(
			QQ::expand($nWithR, QQ::like($nWithR->Street, '%r%')),
			QQ::expand($nWithoutR, QQ::notLike($nWithoutR->Street, '%r%')),
			QQ::groupBy(QQN::project()->Id),
			QQ::minimum($nWithR->City, 'min_city_r'),
			QQ::minimum($nWithoutR->City, 'min_city_wor')
		)
	);

	foreach ($objProjectArray as $objProject){
		_p($objProject->Name . " (" . $objProject->getVirtualAttribute('min_city_r').', '. $objProject->getVirtualAttribute('min_city_wor'). ")");
		_p('<br/>', false);
	}


	Person::getDatabase()->outputProfiling();
?>
</div>

<?php require('../includes/footer.inc.php'); ?>