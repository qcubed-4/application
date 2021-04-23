<?php require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
	<h1>Load Methods that Utilize Database Indexes</h1>

	<p>As you saw in the previous example, the Code Generator will always generate two load methods,
		<strong>load()</strong> and <strong>loadAll()</strong>, for every code generated class.
        <strong>load()</strong> takes in the primary
		key (or primary keys if you have multiple PKs defined on the table) as the parameter, while
		<strong>loadAll()</strong> simply returns all the rows in the table.</p>

	<p>Using database indexes, the code generator will also generate additional load-type methods
		given the way you have defined those indexes.  In our <strong>Examples Site Database</strong>, there are quite a
		few indexes defined, but we will highlight two:</p>
	<ul>
		<li>person.last_name</li>
		<li>login.username (UNIQUE)</li>
	</ul>

	<p>Given these two indexes, the code generator has generated <strong>loadArrayByLastName()</strong> in the
		<strong>Person</strong> object, and it has defined <strong>loadByUsername()</strong> in the <strong>Login</strong> object.</p>

	<p>The <strong>LastName</strong> load method returns an array while the <strong>Username</strong> load method
		returns just a single object.  The code generator has recognized the UNIQUE property on the column,
		and it generated code accordingly.</p>

	<p>You could also define indexes on multiple columns and the code generator will
		generate load methods based on those multi-column keys.</p>
</div>


<div id="demoZone">
	<h3>Using loadByUsername() to get a single Login object</h3>
<?php
	// Let's load a login object -- let's select the username 'jdoe'
	$objLogin = Login::loadByUsername('jdoe');
?>
	<p>Login ID: <?php _p($objLogin->getId()); ?><br/>
		Login Username: <?php _p($objLogin->getUsername()); ?><br/>
		Login Password: <?php _p($objLogin->getPassword()); ?></p>


	<h3>Using loadArrayByLastName() to get an array of Person objects</h3>
	<ul>
<?php
		// We'll load all the persons who has a last name of "Smith" into an array
		$objPersonArray = Person::loadArrayByLastName('Smith');

		// Use foreach to iterate through that array and output the first and last
		// name of each person
		foreach ($objPersonArray as $objPerson) {
			printf('<li>' . $objPerson->getFirstName() . ' ' . $objPerson->getLastName() . '</li>');
		}
?>
	</ul>
	<h3>Using countByLastName() to get a count of all "Smiths" in the database</h3>
	<p>There are <?php _p(Person::countByLastName('Smith')); ?> person(s) who have a last name of "Smith" in the system.</p>
</div>

<?php require('../includes/footer.inc.php'); ?>