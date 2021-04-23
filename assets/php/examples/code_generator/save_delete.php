<?php require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
	<h1>Saving and Deleting Objects</h1>
	<p>The C, U and D in CRUD is handled by the code generated <strong>save()</strong> and <strong>delete()</strong> methods in
		every object.</p>

	<strong>delete()</strong> should hopefully be self-explanatory.  <strong>save()</strong> will either call a SQL INSERT
		or a SQL UPDATE, depending on whether the object was created brand new or if it was restored via
        one of the <strong>load*</strong> methods. If you have the need,
        you can call <strong>save()</strong> passing in true for the optional <strong>$blnForceInsert</strong>
		or <strong>$blnForceUpdate</strong> parameters to force it to do an INSERT or UPDATE.  </p>
</div>

<div id="demoZone">
	<h2>Load a Person object, modify it, and save</h2>
<?php
	// Let's load a Person object -- let's select the Person with ID #3
	$objPerson = Person::Load(3);
?>
	<h3><em>Before the save</em></h3>
	<ul>
		<li>Person ID: <?php _p($objPerson->getId()); ?></li>
		<li>First Name: <?php _p($objPerson->getFirstName()); ?></li>
		<li>Last Name: <?php _p($objPerson->getLastName()); ?></li>
	</ul>
<?php
	// Update the field and save
	$objPerson->setFirstName('FooBar');
	$objPerson->save();

	// Restore the same person object just to make sure we
	// have a clean object in the database
	$objPerson = Person::load(3);
?>
	<h3><em>After the save</em></h3>
	<ul>
		<li>Person ID: <?php _p($objPerson->getId()); ?></li>
		<li>First Name: <?php _p($objPerson->getFirstName()); ?></li>
		<li>Last Name: <?php _p($objPerson->getLastName()); ?></li>
	</ul>

<?php
	// Let's clean up -- once again update the field and save
	$objPerson->setFirstName('Ben');
	$objPerson->save();
?>
	<h3><em>Cleaning up</em></h3>
	<ul>
		<li>Person ID: <?php _p($objPerson->getId()); ?></li>
		<li>First Name: <?php _p($objPerson->getFirstName()); ?></li>
		<li>Last Name: <?php _p($objPerson->getLastName()); ?></li>
	</ul>
</div>

<?php require('../includes/footer.inc.php'); ?>