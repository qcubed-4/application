<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Moving Controls Between Panels</h1>

	<p>With the concept of a <strong>Label</strong> or <strong>Panel</strong> being able to have an arbitrary
	number of child controls, we use this example to show how you can dynamically
	change a control's parent, to essentially "move" a control from one panel to the next.</p>

	<p>The example below has two <strong>Panel</strong> controls, as well as ten <strong>TextBox</strong> controls
	who's parents are one of the panels.  The buttons have <strong>Ajax</strong> actions which will
	move the textboxes back and forth between the panels, or remove the textbox altogether.</p>

	<p>Again, note that we are not hard coding a <strong>$objTextBox->render()</strong> <em>anywhere</em> in our code.  We
	are simply using the concept of <strong>ParentControls</strong> and using the two <strong>BlockControl</strong> controls'
	<strong>AutoRenderChildren</strong> functionality to dynamically render the textboxes in the
	appropriate places.</p>

	<p>Finally, notice that while we are doing this using AJAX-based actions, you can just as easily use
	Server-based actions as well.</p>
</div>

<div id="demoZone">
	<table cellspacing="0" cellpadding="5" border="0">
		<tr>
			<td valign="top"><?php $this->pnlLeft->Render(); ?></td>
			<td valign="top"><?php $this->pnlRight->Render(); ?></td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<?php $this->btnMoveLeft->Render(); ?>
				<?php $this->btnMoveRight->Render(); ?><br/>
				<?php $this->btnDeleteLeft->Render(); ?>
			</td>
		</tr>
	</table>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>