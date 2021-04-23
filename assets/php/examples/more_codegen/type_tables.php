<?php use QCubed\Query\QQ;

require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
	<h1>Using Type Tables</h1>

	<p>Use <strong>Type Tables</strong> to create enumerated constant types for QCubed.
        While only some database vendors (e.g. MySQL) offer support for ENUM or SET column types,
	QCubed provides support for these enumerated column types for <em>all</em>
	database vendors through <strong>Type Tables</strong>.</p>

	<p>To tell QCubed that it should treat a particular SQL table as a <strong>Type Tables</strong>, add
        a "_type" suffix to the end of the name of the table. <strong>Type Tables</strong> must
	have at least 2 columns: a primary key ID named "id", and a unique VARCHAR column "name".
        Before running the code generator, add data
        to your type table to define the constants you want generated. </p>

	<p>The code generator will create a <strong>Type</strong> object for the table.
     Since a <strong>Type</strong> object should not change during application execution,
        this object will <em>not</em> have the CRUD functionality but instead will contain constants,
	one for each row in the <strong>Type Table</strong>. Whenever
	a new enumerated value needs to be added to the <strong>Type</strong> object, you will need to manually do the SQL INSERT
	into this <strong>Type Table</strong>, and then run the code generator.</p>

	<p>In our example here, we show the contents of <strong>ProjectStatusType</strong>.  Note how the <strong>Project</strong>
	class has a relationship with <strong>ProjectStatusType</strong>, and how we can display a <strong>Project</strong>
	object's status using the static methods of <strong>ProjectStatusType</strong>.</p>

	<p>You can, if you want, have more than two columns in a type table; QCubed will generate additional methods
	based on the names of the additional columns you define. In the example here, the Project Status Types table
	has the following columns: "id", "name" (unique), "description", and "guidelines". The QCubed code
	generator will create methods such as <strong>ProjectStatusType::toDescription()</strong> and <strong>ProjectStatusType::toGuidelines()</strong>
	for you.</p>		

	<p>You can also use an association table with a type table to create a many-to-many relationship with a type.
	This is similar to the SET type in MySQL, but is database independent.</p>

    <p>If you do want to use a "_type" suffix for some reason, you can specify a different suffix in the
        codegen_settings.xml file in the configuration directory.</p>

</div>

<div id="demoZone">
	<h2>List All the Project Status Types (Names and Descriptions)</h2>
<?php
    // All Enumerated Types should go from 1 to "MaxId"
    for ($intIndex = 1; $intIndex <= ProjectStatusType::MAX_ID; $intIndex++) {
        // We use the Code Generated ToString and ToDescription to output a constant's value
        _p(ProjectStatusType::toString($intIndex) . ' - ' . ProjectStatusType::toDescription($intIndex));

        // We can even use the Enums as PHP constants
        if ($intIndex == ProjectStatusType::Cancelled) {
            _p(' (sad!)');
        }

        _p('<br/>', false);
    }
?>
	<h2>Load a Project Object and View Its Project Status</h2>
<?php
    // Let's load a Project object -- let's select the Project with ID #3
    $objProject = Project::load(3);
?>
	Project ID: <?php _p($objProject->Id); ?><br/>
	Project Name: <?php _p($objProject->Name); ?><br/>
	Project Status: <?php _p(ProjectStatusType::toString($objProject->ProjectStatusTypeId)); ?>

	<h2>List the Employees and Their Options</h2>
<?php
    // Load all the people and expand the type array associated with the person table
    $objClauses[] = QQ::expandAsArray(QQN::person()->PersonType);
    $objPeople = Person::loadAll($objClauses);
    
    foreach ($objPeople as $objPerson) {
        _p($objPerson->FirstName . ' ' . $objPerson->LastName . ': ');
        $intTypeArray = $objPerson->_PersonTypeArray;   // The underscore prefix here indicates you cannot late bind this. You must use a expand clause to get this data.
        $strTypeArray = array();
        foreach ($intTypeArray as $intType) {
            $strTypeArray[] = PersonType::toString($intType);
        }
        _p(implode(', ', $strTypeArray));
        _p('<br/>', false);
    }
?>
</div>


<?php require('../includes/footer.inc.php'); ?>
