<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Generating Html Lists</h1>

	<p><strong>HList</strong> lets you create ordered or unordered hierarchical HTML lists of arbitrary depth.
		Its interface is very similar to the ListControl controls, but also adds the ability to use a data binder
	    to get the list of items to display.</p>

	<p>Using the data binder is optional, and in some cases it is advantageous to not use the databinder. If you don't
	   use a databinder, you set the HList's data when you construct it, and all the list's items are stored in the Formstate object,
        which means that the data is serialized
	   and restored every time the user does any kind of action on the screen that requires a response from the server.
	   If you have a long list, that can take a lot of space and time. </p>

	<p>By using a databinder, the items are only created when the list is drawn and then are immediately deleted.
		This saves space but means the items are no longer available to be queried later if you need to know
	    information about the list. Depending on your application, this might be OK. Also, depending on your application,
	    there may be other ways of getting the data you need without storing the entire item list.</p>

	<p>A plain HTML list may not be all that exciting, but many 3rd party JavaScript and CSS widgets use HTML lists
	   as the foundation to create menus, navigation bars, collapsible trees, and more, making the <strong>HList</strong>
	   an excellent candidate for the base of such a control. See some of our plugin controls for examples.</p>

	<p>In this example we create a <strong>HList</strong> using an expanded project list. The expansion lets
	   us add project members to each project item, and then add the project item to the main list.</p>

</div>

<div id="demoZone">

	<?php $this->lstProjects->render(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>