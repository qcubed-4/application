<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Learning about Validation</h1>

	<p>In this example, we extend our calculator application to include validation.</p>

	<p>As we mentioned earlier, Forms will go through a validation process just before it executes
		any Actions, if needed.  If the Control that triggers the Action has its
		<strong>CausesValidation</strong> property set to "true", then before executing the Action, the Form will
		go through every visible control in the entire Form and call <strong>Validate()</strong>.  Only after ensuring
		that every control is valid, will the Form go ahead and execute the assigned Action.
		Otherwise, every Control that had its <strong>validate()</strong> fail will have its ValidationError property
		set with the appropriate error message.</p>

	<p><em>What</em> the validation checks for is dependent on the control you are using.  In general,
		Controls that have their <strong>Required</strong> property set to "true" will check to ensure that data
		was at least entered or selected.  Some controls have additional rules.  For example, we'll use
		<strong>IntegerTextBox</strong> here to have Forms ensure that the data entered in our two textboxes are
		valid integers.</p>

	<p>So we will utilize the FormBase's validation in our application by doing the following:</p>
	<ul>
		<li>Set our <strong>btnCalculate</strong> button's <strong>CausesValidation</strong> property to true</li>
		<li>Use <strong>IntegerTextBox</strong> classes</li>
		<li>For those textboxes, we will use <strong>renderWithError()</strong> instead of <strong>render()</strong> in the HTML
			template code.  This is because <strong>render()</strong> only renders the control, itself, with no
			other mark up or placeholders.  <strong>renderWithError()</strong> will be sure to render any error/warning
			messages for that control if needed.</li>
		<li>Lastly, we will add our first "business rule": ensure that the user does not divide by 0.
			This rule will be implemented as an <strong>if</strong> statement in the <strong>formValidate</strong> method.</li>
	</ul>

	<p>For more advanced uses, <strong>CausesValidation</strong> can also be set to <strong>ControlBase::CAUSES_VALIDATION_SIBLINGS_AND_CHILDREN</strong>
		or <strong>ControlBase::CAUSES_VALIDATION_SIBLINGS_ONLY</strong>.  This functionality is geared for developers who are creating more
		complex <strong>Forms</strong> with child controls (either dynamically created, via custom composite controls, custom <strong>Panels</strong>, etc.),
		and allows for more finely-tuned direction as to specify a specific subset of controls that should be validated, instead
		of validating against all controls on the form.</p>

	<p><strong>SiblingsAndChildren</strong> specifically validates all sibling controls and the children of the control that is triggering
		the action, while <strong>SiblingsOnly</strong> specifies to validate the triggering control's siblings, only. One place this is useful for
    is in dialogs. Since a dialog is an HTML object that overalys an HTML Form, you only want to validate the controls in the dialog when
    its showing, rather than all the controls in the entire form.</p>
</div>

<div id="demoZone">
	<p>Value 1: <?php $this->txtValue1->renderWithError(); ?></p>

	<p>Value 2: <?php $this->txtValue2->renderWithError(); ?></p>

	<p>Operation: <?php $this->lstOperation->render(); ?></p>

	<?php $this->btnCalculate->render(); ?>
	<hr/>
	<?php $this->lblResult->render(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>