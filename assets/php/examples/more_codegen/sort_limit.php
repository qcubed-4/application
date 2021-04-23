<?php

use QCubed\Query\QQ;

require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
	<h1>Manipulating loadAll and loadArrayBy Results</h1>



	<p>All code generated <strong>loadAll()</strong> and <strong>loadArrayBy...()</strong> methods accept an optional
	<strong>QCubed Query Clause Array</strong> parameter, where you can specify a <strong>Clause</strong>
	objects, including functionality that handles <strong>ORDER BY</strong>, <strong>LIMIT</strong>
	and <strong>Object Expansion</strong>.  We will
	discuss <strong>Object Expansion</strong> in the examples that deal with <strong>Late Binding</strong>
	and <strong>Early Binding</strong>.  But for this example, we'll focus on
	using <strong>QQ::orderBy()</strong> and <strong>QQ::limitInfo()</strong> to manipulate how the results come out of the database.</p>

	<p><strong>orderBy()</strong> and <strong>limitInfo()</strong> are straightforward to use.  OrderBy takes
	in any number of QCubed Query Node columns, followed by an optional boolean (to specify ascending/descending),
	which will be used in a SQL ORDER BY clause in the SELECT statement.  So you can simply say
	<strong>\QCubed\Query\QQ::OrderBy(QQN::Person()->LastName)</strong> to sort all the Person objects by last name.</p>

	<p><strong>limitInfo()</strong> accepts a maximum row count, followed by an optional offset.
	So if you specified "10, 4", the result set would contain at most 10 rows, starting with row #5
	(the offset is based on a 0 index).</p>

	<p>You can use either, both, more or none of these optional <strong>Clause</strong>
	parameters whenever you make your <strong>loadAll()</strong> or <strong>loadArrayBy()</strong> calls.
    In the section about DataGrids, you will learn how to take sort and limit information from a DataGrid to
        populate the DataGrid with the data the user is asking for, in the order of the sort column.
    </p>

    <p>For more information about "QQ::"-related classes (a.k.a. <strong>QCubed Query</strong>), please refer to section 3 of the
        Examples Site.</p>
</div>

<div id="demoZone">
	<h2>List All Persons, Ordered by LastName then FirstName</h2>

<?php
    // Load the Person array, sorted


    $objPersonArray = Person::LoadAll(
        [   // starts an array

            // Note the "use" at the top of this file that imports the QQ class
            // Also, the QQN class here is a "Node" class, that helps describe relationships in the database. More on these is coming in another example.
            QQ::orderBy(QQN::person()->LastName, QQN::person()->FirstName)
        ]
    );
    foreach ($objPersonArray as $objPerson) {
        _p($objPerson->getLastName() . ', ' . $objPerson->getFirstName() . ' (ID #' . $objPerson->getId() . ')');
        _p('<br/>', false);
    }
?>


	<h2>List Five People, Start with the Third from the Top, Ordered by Last Name then First Name</h2>
<?php
    // Load the Person array, sorted and limited
    // Note that because we want to start with row #3, we need to define "2" as the offset
    $objPersonArray = Person::loadAll(
        [
            QQ::orderBy(QQN::Person()->LastName, QQN::Person()->FirstName),
            QQ::limitInfo(5, 2)
        ]
    );
    foreach ($objPersonArray as $objPerson) {
        _p($objPerson->getLastName() . ', ' . $objPerson->getFirstName() . ' (ID #' . $objPerson->getId() . ')');
        _p('<br/>', false);
    }
?>
</div>

<?php require('../includes/footer.inc.php'); ?>