<?php use QCubed\QString;
use QCubed\Query\QQ;

require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
	<h1>Early Binding: Using Object Expansion and SQL Joins</h1>

	<p>When you need to perform <strong>loadAll()</strong> or <strong>loadArrayBy...()</strong> calls, and would like to include related objects
	in order to reduce the number of database calls in an extended query,
     you can use QCubed's <strong>Object Expansion</strong> functionality to
	specify which foreign key columns that you want to expand immediately.</p>

	<p>The <strong>Object Expansion</strong> function, which is generated into each object in the ORM,
	will bind these related objects when the objects are initially created, thus the term
	"early binding".</p>

	<p>In our example here, we will perform the <em>exact same task</em> as the previous example, pulling
	all the <strong>Project</strong> objects and displaying each object's <strong>ManagerPerson</strong>.  Note
	that the <em>only difference</em> in our code is that we've added a <strong>QQ::expand()</strong> clause.
	There is <em>no other difference</em> with the way we access the restored objects and their related
	objects.</p>

	<p>The end result is that instead of displaying the data using 5 queries, we have now cut this down
	to just 1 query.  QCubed accomplishes this by using a LEFT JOIN in the SQL which is executed
	by the code generated ORM. If you are familiar with SQL, you can think of a <strong>QQ::expand()</strong>
    as the way to create JOINs in the resulting SQL.</p>

	<p>Since the way we access the objects is the exact same, this
	kind of optimization can be done <em>after</em> the page is functional and complete.  This
	follows the general philosophy of QCubed, which is to first focus on making your application
	functional, then focus on making your application more optimized.  Often engineers can get
        bogged down making an application as optimized as possible before knowing what they actually want
        the software to do or testing it with real people,
	and in doing so they can waste time over-engineer some pieces of functionality.
	If the focus is on getting the application functional, first, then after the application is in
	a usable state, you can profile the functionality that tends to get used more often and simply
	focus on optimizing this smaller subset of heavily-used functionality.</p>

	<p>You can expand from one table to another through foregin keys that reference a field in another table,
        through reverse references that have foregin keys that refer from the other table back to the table you
        are working with, or through association tables. Some of the other examples in this tutorial will teach
    you more about using each of these kinds of references.
    </p>
</div>

<div id="demoZone">
	<h2>List All Projects and its Manager</h2>
<?php
	// Enable Profiling (we're assuming the Examples Site Database is at index 1)
	// NOTE: Profiling should only be enabled when you are actively wanting to profile a specific PHP script.
	// Because of SIGNIFICANT performance degradation, it should otherwise always be off.
	Project::getDatabase()->enableProfiling();

	// Load the Project array
	// The following line of code is the ONLY line of code we will modify
	$objProjectArray = Project::loadAll(  QQ::clause(QQ::expand(QQN::project()->ManagerPerson))  );
	foreach ($objProjectArray as $objProject) {
		_p(QString::htmlEntities($objProject->Name) . ' is managed by ' . $objProject->ManagerPerson->FirstName . ' ' .
			$objProject->ManagerPerson->LastName);
		_p('<br/>', false);
	}
	_p('<br/>', false);

	// Output Profiling Data
    Project::getDatabase()->outputProfiling();
?>
</div>

<?php require('../includes/footer.inc.php'); ?>