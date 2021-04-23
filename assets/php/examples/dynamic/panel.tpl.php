<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Introduction to Panel and Label</h1>
	
	<p>It may seem funny that we are "introducing" the <strong>Panel</strong> and <strong>Label</strong> controls
	now, especially since we have already been using them a lot throughout the examples.</p>

	<p>On the surface, it may seem that <strong>Label</strong> is very simple -- you specify the <strong>Text</strong>
	that you want it to display and maybe some styles around it, and then you can just <strong>Render</strong>
	it out.  And while <strong>Label</strong> and <strong>Panel</strong> controls should certainly be used for 
	that purpose, they also offer a lot more in functionality.</p>

	<p>Both the <strong>Label</strong> and <strong>Panel</strong> controls extend from the <strong>BlockControl</strong> class.
	The only difference between the two is that labels will render as a &lt;span&gt; and panels will render
	as a &lt;div&gt;.

	<p>In addition to defining the <strong>Text</strong> to display inside the control, these controls can also use a
        <strong>Template</strong> file to display contents, including child controls, or auto-render child controls.
	This offers
	a <em>lot</em> of power and flexibility, basically allowing you to render out an arbitrary number of dynamic
	 controls inside of them.</p>

	<p>The order of rendering for block controls are:</p>
	<ul>
		<li>Display the <strong>Text</strong> (if defined).</li>
		<li>Pull in the <strong>Template</strong> and render it (if defined).</li>
		<li>If <strong>AutoRenderChildren</strong> is set to true, then get all child controls and call <strong>render()</strong> on all of them
		that have not been rendered yet, in the order they were added to the parent control.</li>
	</ul>

	<p>In our example below, we define a <strong>Panel</strong> and assign textboxes as child controls.  We specify
	a <strong>Text</strong> value and also setup a <strong>Template</strong>.  Finally, we render that entire panel out (complete
	with the text, template and child controls) with a single <strong>render()</strong> call.</p>

	<p>Note that even though 10 textboxes are being rendered, we never explicitly code a <strong>$objTextBox->render()</strong>
	call <em>anywhere</em> in our code. Instead, we let <strong>AutoRenderChildren</strong> do that for us.</p>

	<p>Within the template file, the <em>$this</em> variable refers to the control being rendered.</em></p>

	<p>Another type of block control to mention here is the  <strong>Fieldset</strong>
		which draws a panel as an html fieldset, and has a legend. Otherwise, it is the same as a Panel.</p>


</div>

<div id="demoZone">
	<?php $this->pnlPanel->Render(); ?>
	<?php $this->pnlFieldset->Render(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>