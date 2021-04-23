<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Combining Multiple Actions on Events</h1>
	
	<p>We can combine mulitple actions together for events, and we can also use the same set of
	actions for on multiple events or controls.</p>
	
	<p>In this example, we have a listbox, and we allow the user to dynamically add items to that
	listbox.  On submitting, we want to perform the following actions:</p>
	<ul>
		<li>Disable the Listbox (via Javascript)</li>
		<li>Disable the Textbox (via Javascript)</li>
		<li>Disable the Button (via Javascript)</li>
		<li>Make an AJAX call to the PHP method <b>AddListItem</b></li>
	</ul>
	
	<p>The PHP method <strong>addListItem()</strong> will then proceed to add the item into the listbox, and re-enable all
	the controls that were disabled.</p>
	
	<p>Note that what we are doing is combining multiple actions together into an action array (e.g. <strong>ActionBase[]</strong>).
	Also note that this action array is defined on two different controls: the button (as a <strong>Click</strong>)
	and the textbox (as a <strong>EnterKey</strong>).</p>
	
	<p>Also note that we also add a <strong>Terminate</strong> action to the textbox in response to
	the <strong>EnterKey</strong>.  The reason for this is that on some browsers, hitting the enter
	key in a textbox would cause the form to do a traditional form.submit() call.  Given the way
	Forms operates with named actions, and especially given the fact that this Form is using AJAX-based
	actions, we do <em>not</em> want the browser to be haphazardly performing submits.</p>
	
	<p>Finally, while this example uses <strong>Ajax</strong> to make that an AJAX-based call to the PHP
	<strong>addListItem()</strong> method, note that this example can just as easily have made the call to
	<strong>AddListItem</strong> via a standard <strong>Server</strong>.  The concept of combining multiple actions
	together and the concept of reusing an array of actions on different controls/events remain the same.</p>
</div>

<div id="demoZone">
	<?php $this->lstListbox->renderWithName(); ?>
	
	<?php $this->txtItem->renderWithName(); ?>

	<?php $this->btnAdd->render(); ?>

	<?php $this->lblSelected->renderWithName(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>