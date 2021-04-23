<?php require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
	<h1>Late Binding</h1>

    <p>The QCubed ORM (object-relation model) is the system that populates PHP objects with information from the
    database. This provides a level of decoupling of your application from the actual database implementation.
    Instead of writing SQL code, you can use PHP classes and functions to create database queries, and the result is
    a typed PHP object populated with the information from the query.
    </p>

	<p>By default, any object with related objects will perform
	"late binding" on that related object. This means that when you ask for a related object from a QCubed ORM object,
     a database call is made at that moment and the object is created from the information returned by the database.
    <p>

        In our <strong>Examples Site Database</strong>, an example
        of this is how the <strong>Project</strong> object has a related <strong>ManagerPerson</strong> object.
	When you load a given <strong>Project</strong> object, the <strong>$objManagerPerson</strong>
	member variable inside the object is initially NULL.  But when you ask for the <strong>ManagerPerson</strong> property,
	the object first checks to see if the <strong>$objManagerPerson</strong> is null, and if it is, it will
	call the appropriate <strong>load()</strong> method to then query the database to pull that <strong>Person</strong>
	object into memory, and then bind it to this <strong>Project</strong> object.  Note that any <i>subsequent</i>
	calls to the <strong>ManagerPerson</strong> property will simply return the already bound <strong>Person</strong>
	object (no additional query to the database is needed).  This <strong>Person</strong> is
	essentially bound, as late as possible, to the <strong>Project</strong>, thus the term "late binding".</p>


	<p>In some cases, late binding can help limit traffic between the application and the database, but in others,
        it can increase it and slow the application down.
        The advantage of late binding is that you get just the data that you need, when you need it,
	and nothing else.  And fortunately, because the QCubed generated code does the binding for you
	behind the scenes, there is nothing that you would need to manually code to check, enforce or
	execute this binding functionality.</p>

	<p>The disadvantage, however, is that for some situations where you are performing <strong>loadAll()</strong>
	or <strong>loadArrayBy()</strong>, and you need to use all the related objects within those arrays, you end up with
	lots of calls to the database, one for each item in the resulting array.</p>

	<p>In the example on this page, we call <strong>loadAll()</strong> to get all the <strong>Project</strong> objects,
        and view each object's
	<strong>ManagerPerson</strong>.  Using the built in QCubed Database Profiler, you can see that
	five database calls are made: One call to get all the projects (four rows in all), and then four calls
	to <strong>Person::Load</strong> (one for each of those projects).</p>
</div>

<div id="demoZone">
	<h2>List All Projects and its Manager</h2>
	<ul>
<?php
	// Enable Profiling (we're assuming the Examples Site Database is at index 1)
	// NOTE: Profiling should only be enabled when you are actively wanting to profile a specific PHP script.
	// Because of SIGNIFICANT performance degradation, it should otherwise always be off.
	Project::getDatabase()->enableProfiling();

	// Load the Project array
	// Note how even though we make two calls to ManagerPerson PER project, only ONE call to
	// Person::Load is made per project -- this is because ManagerPerson is bound to the
	// Project during the first call.  So the second call is using the ManagerPerson that's
	// already bound to that project object.
	$objProjectArray = Project::loadAll();
	foreach ($objProjectArray as $objProject) {
		_p('<li>'.$objProject->Name . ' is managed by ' . $objProject->ManagerPerson->FirstName . ' ' . 
			$objProject->ManagerPerson->LastName.'</li>', false);
	}
	_p('</ul>', false);
	// Output Profiling Data
	Project::getDatabase()->outputProfiling();
?>
</div>

<?php require('../includes/footer.inc.php'); ?>