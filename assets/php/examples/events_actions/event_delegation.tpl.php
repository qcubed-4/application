<?php require('../includes/header.inc.php'); ?>
<style>
#dtgPersons tr.selectedStyle, #dtgPersonsDelegated tr.selectedStyle {
	background-color: #ffaacc !important;
	cursor: pointer;
}

div.col {
   display: inline-block;
   width: 45%;
   padding:1%;
   vertical-align:top;
}

div.table, div.code {
	max-height: 400px;
	overflow: auto;
	width: auto;

}

#dtgPersons tr.newperson, #dtgPersonsDelegated tr.newperson {
    background-color: greenyellow;
}
</style>

<?php $this->renderBegin(); ?>

<div class="instructions">
    <h1 class="instruction_title">Event Delegation</h1>

    Event delegation is the process of binding actions to parent elements that trigger based on events
    that occur to child elements. Most JavaScript events bubble up through the HTML hierarchy and can be
    detected by parent objects, provided child objects don't prevent bubbling.
    Event delection is useful in QCubed-4 for the following reasons:

    <ul>
        <li>If you have a lot of repeating objects, this can reduce the amount of JavaScript code sent to the browser.
            By binding an action to the parent object, you only need to detect one event, rather than separate events for each repeating object.
            Most of the time, users won't notice the difference, but if you have a lot of repeating objects, it can make your web page more responsive.</li>
        <li>You can detect events coming from objects that are not initially in the form.
            That means that the delegation also works for child elements that get
            inserted into the parent element after the event/action was bound (delegated)
            to the parent.
        </li>
    </ul>
    <p>
        To create an event handler that is looking for bubbled events, you pass a 3rd parameter to any <strong>EventBase</strong>
        event detector class. This string is a <a href="#" onclick="window.open('http://api.jquery.com/category/selectors/','_newtab')">JQuery selector</a>,
        which is similar to a CSS selector, and acts as a kind of filter, specifying what types of HTML objects we
        will be listening to.
    </p>
    <p>
        Event delegation is automatically used by some aspects of QCubed. For example, the <strong>Proxy</strong>
        control uses event delegation to respond to proxied buttons and links by attaching an event handler to the
        form that is listening for bubbled events directed toward proxied controls.
    </p>
    <p>
        The following code renders 2 DataGrid tables that have an Edit button. The first data grid,
        called "dtgPersons," adds an edit button to every row, creating a new Button object each time and attaching
        a separate click event handler to each button. The second grid, called dtgPersonsDelegated, draws HTML for a
        button on each row, with a "data-id" attribute that is the record id. It also has a single action handler that
        looks for clicks on buttons inside itself that have a "data-id" attribute and passes this value to the action handler.
        The result is the same, but the second version generates much less JavaScript.
    </p>

    <p>
        All <strong>Event</strong>s can take a 3rd parameter which indicates it will use event delegation. There is also
        the <strong>On</strong> that allows you to specify any kind of JavaScript event to listen to.
    </p>

</div>

<div>

    <div class="col">
        <h2>The datagrid <strong>without</strong> event delegation</h2>
        <div class="table">
            <?php $this->dtgPersons->render(); ?>
        </div>
    </div>

    <div class="col">
        <h2>The datagrid <b>with</b> event delegation</h2>
        <div class="table">
            <?php $this->dtgPersonsDelegated->render(); ?>
        </div>
    </div>

</div>








    <?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>
