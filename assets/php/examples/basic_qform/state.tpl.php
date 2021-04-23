<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Understanding State</h1>
	
	<p>When you clicked on the button in the previous example, the HTML form posted its information back to itself.  However,
		the state of the form was remembered from one webpage view to the next.  <strong>Form</strong> objects, in fact,
        are stateful objects that maintain state from one post to the next. QCubed
    does this by serializing the entire form object, together with all its controls and subcontrols, into one
    long string and saving this information. This is known as <strong>formstate</strong></p>. Before QCubed reads the
    information being submitted by the HTML form, it will unserialize the formstate to reconstruct the entire internal form structure,
    and then it will add the information being submitted from the HTML form.

	<p>In the example here, we have an <strong>$intCounter</strong> defined in the form.  And basically, whenever
		you click on the button, we will increment <strong>$intCounter</strong> by one.  Note that the HTML template
		file is displaying <strong>$intCounter</strong> directly via a standard PHP <strong>print</strong> statement.</p>

	<p>Also note that session variables, cookies, etc. are <i>not</i> being used here -- only <strong>FormState</strong>.  In fact,
		you can get an idea if you do <strong>View Source...</strong> in your browser of the HTML on this page.
		You will see a bunch of cryptic letters and numbers for the <strong>Qform__FormState</strong> hidden variable.
		Those letters and numbers actually represent the serialized version of this <strong>Form</strong> object.</p>

    <p>
        By default, the form state is stored in a hidden form input. This may be fine for initial development of an application,
        but it is not secure, and has some performance and size limitations. Fortunately, QCubed provides you with a number of
        other mechanisms that let you store the formstate in PHP's session variable, in your database, or in an external
        cache. See the section on <a href="../other/form_state.php">FormState Handlers</a> for more information on how to configure those.
    </p>
</div>

<div id="demoZone">
	<p>The current count is: <?php _p($this->intCounter); ?></p>
	<p><?php $this->btnButton->render(); ?></p>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>