<?php require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
	<h1>Analyzing Reverse Relationships</h1>

	<p>Although it's a bit hard to understand at first, one of the unique and more powerful features of QCubed-4
		is its ability to generate code to handle reverse relationships as well.
		Given our previous example with the <strong>Project</strong> and <strong>ManagerPerson</strong>, we showed how
		QCubed-4 generated code in the <strong>Project</strong> class to handle the relationship. But QCubed-4 will also generate
		code in the <strong>Person</strong> class to handle the reverse aspects of this relationship.</p>

	<p>In this case, <strong>Person</strong> is on the "to Many" side of a "One-to-Many" relationship with <strong>Project</strong>.
		So QCubed-4 will generate the following methods in <strong>Person</strong> to deal with this reverse
		relationship:</p>
	<ul>
		<li>getProjectsAsManagerArray()</li>
		<li>countProjectsAsManager()</li>
		<li>associateProjectAsManager()</li>
		<li>unassociateProjectAsManager()</li>
		<li>unassociateAllProjectsAsManager()</li>
		<li>deleteAssociatedProjectAsManager()</li>
		<li>deleteAllProjectsAsManager()</li>
	</ul>

	<p>And in fact, QCubed-4 will generate the same seven methods for any "One-to-Many" reverse relationship
		(get, count all, associate, unassociate, and unassociate all, delete associated, and delete all associated).
		Note that the "AsManager" token in all these methods is there because we named the column in the
		<strong>project</strong> table <strong>manager_person_id</strong>. If we simply named it <strong>person_id</strong>,
		the methods would be named without the "AsManager" token (e.g., "getProjectsArray," "countProjects",
		etc.)</p>

	<p>Also note that <strong>getProjectsAsManagerArray()</strong> utilizes the <strong>loadArrayByManagerPersonId()</strong>
		method in the <strong>Project</strong> object. This was generated because <strong>manager_person_id</strong> is already
		an index (as well as a foreign key) in the <strong>project</strong> table.</p>

	<p>QCubed-4's Reverse Relationships functionality
		is dependent on the data model having indexes defined on all columns that are foreign keys. For many
		database platforms (e.g., MySQL Innodb) this should not be a problem b/c the index is created implicitly by the engine.
		But for some (e.g., SQL Server) platforms, make sure that you have indexes defined on your foreign key columns,
		or else you forgo being able to use the Reverse-Relationship functionality.</p>

	<h2>Unique Reverse Relationships (e.g., "One to One" Relationships)</h2>

	<p>QCubed-4 will generate a different set of code if it knows the reverse relationship to be a "Zero
		or One to One" type of relationship. This occurs in the relationship between
		our <strong>login</strong> and <strong>person</strong> tables. Note that <strong>login</strong>.<strong>person_id</strong> is a unique
		column. Therefore, QCubed-4 recognizes this as a "Zero- or One-to-One" relationship. So for the
		reverse relationship, QCubed-4 will not generate the five methods (listed above) in the <strong>Person</strong>
		table for the <strong>Login</strong> relationship. Instead, QCubed generates a <strong>Login</strong> property in
		<strong>Person</strong> object which can be set, modified, etc. just like the <strong>Person</strong> property in
		the <strong>Login</strong> object.</p>

	<h3>Self-Referential Tables</h3>

	<p>QCubed-4 also has full support for self-referential tables (e.g., a <strong>category</strong> table that
		contains a <strong>parent_category_id</strong> column which would foreign key back to itself).
		In this case, the QCubed-4 will generate the following seven methods to assist with the reverse
		relationship for this self-reference:</p>
	<ul>
		<li>getChildCategoryArray()</li>
		<li>countChildCategories()</li>
		<li>associateChildCategory()</li>
		<li>unassocaiteChildCategory()</li>
		<li>unassociateAllChildCategories()</li>
		<li>deleteChildCategory()</li>
		<li>deleteAllChildCategories()</li>
	</ul>

	<p>(Note that even though this is being documented here, self-referential tables aren't actually
		defined in the <strong>Example Site Database</strong>.)</p>
</div>

<div id="demoZone">

	<h2>Person's Reverse Relationships with Project (via project.manager_person_id) and Login (via a login.person_id)</h2>
<?php
	// Let's load a Person object -- let's select the Person with ID #7
	$objPerson = Person::load(7);
?>
	<ul class="person-list">
		<li>Person ID: <?php _p($objPerson->getId()); ?></li>
		<li>First Name: <?php _p($objPerson->getFirstName()); ?></li>
		<li>Last Name: <?php _p($objPerson->getLastName()); ?></li>
	</ul>

	<h3>Listing of the Project(s) that This Person Manages</h3>
	<ul class="project-list">
<?php
		foreach ($objPerson->getProjectAsManagerArray() as $objProject) {
			_p('<li>' . $objProject->getName() . '</li>', false);
		}
?>
	</ul>
	<p>There are <?php _p($objPerson->countProjectsAsManager()); ?> project(s) that this person manages.</p>

	<h3>This Person's Login Object</h3>
	<ul class="person-list">
		<li>Username: <?php _p($objPerson->Login->getUsername()); ?></li>
		<li>Password: <?php _p($objPerson->Login->getPassword()); ?></li>
	</ul>
</div>

<?php require('../includes/footer.inc.php'); ?>