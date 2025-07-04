<?php require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
	<h1>QCubed and Foreign Key Relationships</h1>

	<p>In addition to your basic CRUD functionality, QCubed-4 will also analyze the foreign key relationships
		in your database to generate relationships between your data model's objects.</p>

	<p>Whenever your table has a column which is a foreign key to another table, the dependent class
		(the table with the FK) will have an instance of the independent class (the table where the FK
		links to). So in our <strong>Example Site Database</strong>, we have a <strong>manager_person_id</strong> column in our
		<strong>project</strong> table.  This results in a <strong>ManagerPerson</strong> property (of type <strong>Person</strong>) in our
		<strong>Project</strong> class.</p>

	<p>Note that the <strong>ManagerPerson</strong> property is a read/write property.  It can be modified just like
		any other property, like <strong>Name</strong> and <strong>Description</strong>.</p>
</div>

<div id="demoZone">
	<h3>Load a Project Object and its ManagerPerson</h3>
<?php
	// Let's load a Project object -- let's select the Project with ID #3
	$objProject = Project::Load(3);
?>
	<ul class="project-list">
		<li>Project ID: <?php _p($objProject->getId()); ?></li>
		<li>Project Name: <?php _p($objProject->getName()); ?></li>
	</ul>

	<ul class="project-list">
		<li>Manager Person ID: <?php _p($objProject->ManagerPerson->getId()); ?></li>
		<li>Manager's First Name: <?php _p($objProject->ManagerPerson->getFirstName()); ?></li>
		<li>Manager's Last Name: <?php _p($objProject->ManagerPerson->getLastName()); ?></li>
	</ul>
</div>

<?php require('../includes/footer.inc.php'); ?>