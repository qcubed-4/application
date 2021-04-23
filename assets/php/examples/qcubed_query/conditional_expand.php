<?php use QCubed\Query\QQ;

require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
	<h1>Conditional Joins with QQ::expand() and QQ::expandAsArray()</h1>

	<p>Sometimes, you find yourself in a situation when you want to issue a
	query for ALL items in a given table, and only some information in
	another table.</p>

	<p>For example, let's say you have a list of persons, and a related list of
	logins. Only some of the persons have logins; some of the logins are
	disabled. Your task is to show the name of every person, and next to it,
	show their login information, but only if their login is actually enabled.</p>

	<p>Before you found out about conditional joins, you had several options:</p>
	<ol>
		<li>Do a LEFT JOIN on the <strong>login</strong> table; write a database-specific,
			somewhat convoluted IF statement that might look like
			<strong>IF(login.is_enabled = 1, login.username, "")</strong>. But what if you
			want to show more than just that one column? Write an IF statement for
			every single output column... Ehh. Plus, not portable across databases.
		</li>
		<li>Get a list of all <strong>persons</strong>, then also get a list of all
			<strong>logins</strong>, then merge the two using PHP. Works with QQuery, but
			incurs an overhead of extra processing.
		</li>
	</ol>

	<p>As you'd expect, there's a better way. When
	you use <strong>QQ::Expand</strong>, you can specify conditions on the table with
	which you want to join, and get only those values that you care about.
	<strong>QQ::Expand</strong> clauses produce a
	<a href="http://en.wikipedia.org/wiki/Join_(SQL)#Left_outer_join">left
	join</a> - so if a row of a table with which you are joining does not
	have a matching record, the left side of your join will still be there,
	and the right side will contain nulls.</p>

	<p>Conditional joins only impact how tables are joined together, and so the conditions
	   must apply only to the joined table.</p>
</div>

<div id="demoZone">
	<h2>Names of every person, their username, and open projects they are managing.</h2>
	<ul>
<?php
    Person::getDatabase()->enableProfiling();
	$objPersonArray = Person::queryArray(
		// We want *every single person*
		QQ::all(),
		array(
			QQ::expand(
				// We also want the login information for each person
				QQN::person()->Login,

				// But only the login information for folks that have
				// their logins ON; for everyone else, just the Person
				QQ::equal(QQN::person()->Login->IsEnabled, 1)
			),
			QQ::expandAsArray(
				// We also want the proejcts that are managed by each person
				QQN::person()->ProjectAsManager,
				// but only if the project is open
				QQ::equal(QQN::person()->ProjectAsManager->ProjectStatusTypeId, ProjectStatusType::Open)
			)
		)
	);

	foreach ($objPersonArray as $objPerson){
		_p('<li>', false);
		_p($objPerson->FirstName . ' ' . $objPerson->LastName . ': ');
		if ($objPerson->Login) {
			_p(" Login: ");
			_p("<strong>" . $objPerson->Login->Username . "</strong>", false);
		} else {
			_p("- no login -");
		}
		if ($objPerson->_ProjectAsManagerArray) {
			_p("; Managed Open Projects: ");
			$strProjects = [];
			foreach ($objPerson->_ProjectAsManagerArray as $objProject) {
				$strProjects[] = $objProject->Name;
			}
			$strProject = implode( ", ", $strProjects);
			_p($strProject);
		}
		_p('</li>', false);
	}

?>
	</ul>
	<p><?php Person::getDatabase()->outputProfiling(); ?></p>
</div>

<?php require('../includes/footer.inc.php'); ?>