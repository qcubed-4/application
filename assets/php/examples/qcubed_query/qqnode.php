<?php require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions" class="full">
	<h1>QCubed Query Nodes</h1>
	
	<p><strong>Node</strong> objects represent entities that can be used to select or filter rows in SQL queries.
        <strong>Nodes</strong> can represent tables, columns, computed values, and custom SQL.
        <strong>Node</strong> classes for your data model are generated for you
	during the code generation process.</p>

	<p><strong>Nodes</strong> are linked together, representing the relationships between tables, columns, and
        foreign keys that you have defined in your database.
	</p>

	<p>To get at a specific <strong>Node</strong>, you create a chain of nodes that start with
        <strong>QQN::ClassName()</strong>, where "ClassName" is the name of the class
	for the type of object your want as the result of your query (e.g. "Person").
        From there, you can use property getters to get at a column or relationship.</p>

	<p>Naming standards for the columns are the same as the naming standards for the public getter/setter properties on the object, itself.
	So just as <strong>$objPerson->FirstName</strong> will get you the "First Name" property of a Person object,
	<strong>QQN::person()->FirstName</strong> will refer to the "person.first_name" column in the database.</p>

	<p>Naming standards for relationships are the same way.  The tokenization of the relationship reflected in a class's
        property and method names will also be reflected in the <strong>Nodes</strong>.  So just as <strong>$objProject->ManagerPerson</strong> will
	get you a Person object which is the manager of a given project, <strong>QQN::project()->ManagerPerson</strong> refers to the
	person table's row where person.id = project.manager_person_id.</p>

	<p>And of course, because <em>everything</em> that is linked together in the database is also linked together in your <strong>Nodes</strong>,
	<strong>QQN::project()->ManagerPerson->FirstName</strong> would of course refer to the person.first_name of the person who is the
	project manager of that particular row in the project table.</p>
</div>

<style>#viewSource { display: none; }</style>

<?php require('../includes/footer.inc.php'); ?>