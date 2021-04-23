<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Understanding the Form Process Flow</h1>

	<p>First of all, don't adjust your screen. =)</p>

	<p>The "form*** called" messages you see are
		showing up to illustrate how the <strong>Form</strong> process flow works. While you don't necessarily
    need to know all the details of the form engine, it can be helpful to have some knowledge when you are
    debugging problems with your form.</p>

	<p>As we mentioned earlier, <strong>Form</strong> objects are stateful, with the state persisting through
		all the user interactions (e.g. ServerActions, etc.).  But the <strong>Form</strong> objects are also
		event-driven.  This is why we state that <strong>Forms</strong> are a "stateful, event-driven architecture
        for web-based forms."</p>
    <p>To make this all work, QCubed outputs HTML Form objects so that their actions "post-back" to themselves. In
        other words, they submit all the form data to the same page the form is on. Similarly, all ajax calls also
        call the same page they are on. On every execution of a <strong>Form</strong>, the following actions happen:</p>

	<ol>
		<li>The form object will determine if we are:
            <ul>
            <li>Loading the page for the first time, or</li>
            <li>Loading the page after a form has submitted itself back to itself (called a post-back), or</li>
            <li>Loading the page as a result of a form submitting an Ajax call back to itself.</li>
            </ul>
        </li>

		<li>If it is a post-back or ajax call from a previously loaded form, then it will retrieve the form's state from
            the <strong>FormState</strong> in order to reconstruct the entire form object in its previous state from before
            the submit was processed.
			It will then go through all the controls and update their values according to the user-entered
			data submitted via the HTML Post or Ajax call.</li>
		<li>Next, the <strong>formRun()</strong> method will be
			triggered. This will be run regardless if we're viewing the page fresh or if we've
			re-posted back to the page. <strong>formRun()</strong> is a good place to put code that you need to run every
            time a form is loaded, like code that verifies that the user has permission to view the page.
        </li>
		<li>Next, if we are viewing the page fresh (e.g. not via a post back), the <strong>formCreate()</strong>
			method (if defined) will be run. <strong>formCreate()</strong> is typically where you would define and
			instantiate your various <strong>Form</strong> controls.  Otherwise, the <strong>formLoad()</strong> method will
			be run.</li>
		<li>Next, if we're posted back because of a <strong>Server</strong> or <strong>Ajax</strong> action that points to a
			specific PHP method, the following will happen:
			<ul>
				<li>First, if the control that triggered the event has its <strong>CausesValidation</strong> property set, then
					the form will go through validation.  The form will call <strong>validate()</strong> on the relevent controls,
					and then it will call <strong>formValidate</strong> on itself.  More information on validation can be seen in the upcoming Calculator examples.</li>
				<li>Next, if validation runs successfully <strong>or</strong> if no validation is requested
					(because <strong>CausesValidation</strong> was set to false), then the PHP method that the action points to will be run.
                    This is the typical way that QCubed responds to things like button clicks.</li>
			</ul>
			So in this repeat of the "Hello World" example, when you click on <strong>btnButton</strong>, the <strong>btnButton_Click</strong> method
			will be executed during this step.</li>
		<li>The <strong>formPreRender()</strong> method will then be run.</li>
		<li>The HTML include template file is included (to render out the HTML).</li>
		<li>And finally, the <strong>formExit()</strong> is run after the HTML has been completely outputted.</li>
	</ol>

	<p>So, a <strong>Form</strong> can have any combination of the five following methods defined to help
		customize <strong>Form</strong> and <strong>Control</strong> processing:</p>
	<ul>
		<li>formRun()</li>
		<li>formLoad()</li>
		<li>formCreate()</li>
		<li>formValidate()</li>
		<li>formPreRender()</li>
		<li>formExit()</li>
	</ul>

    <p>All are optional, and to intercept any of them, simply implement that method in your Form class.</p>
</div>

<div id="demoZone">
	<p><?php $this->lblMessage->Render(); ?></p>
	<p><?php $this->btnButton->Render(); ?></p>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>
