<?php require('../includes/header.inc.php'); ?>
    <?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Event Propagation</h1>
	
	<p>Whenever an event fires on a control inside an HTML document, it "bubbles" up to the parents—to allow the
	parents to react to that event as well. This is a standard feature of all modern browsers; read more about it
	on <a href="http://en.wikipedia.org/wiki/DOM_events">Wikipedia</a>.</p>

	<p>In QCubed-4, we sometimes may want events to stop bubbling up the DOM tree. To do this, we use <strong>StopPropagation</strong>.</p>

	<p>Below are two examples. In each, you'll see a panel that responds to click events. In the first example, both the inside panel
	and the outside panel capture the click inside the innermost panel 2—event bubbling in action.</p>

	<p>The second example shows how to stop the bubbling effect using <strong>StopPropagation</strong>. When you click inside the innermost
	panel 4, only panel 4 will respond to the click, and the click handler will never be called for panel 3.</p>
</div>

<div id="demoZone">
    <style>
        .container {
            padding: 20px;
            background-color: #f6f6f6;
            border: 1px solid #dedede;
	        margin: 5px;
        }

	    .insidePanel {
		    background-color: #dedede;
	    }
    </style>

    <h2>Example with an event bubbling (default)</h2>
    <?php $this->objPanel1->render(); ?>

    <h2>Example without an event bubbling</h2>
    <?php $this->objPanel3->render(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>