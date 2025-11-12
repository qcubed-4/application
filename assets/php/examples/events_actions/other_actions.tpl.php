<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Other Client-Side Action Types</h1>
	
	<p>Below is a sampling of just <em>some</em> of the other <strong>Action</strong> types that are available to you
	as part of the core QCubed-4 distribution.</p>
	
	<p>Notice that all of these <strong>Actions</strong> simply render out JavaScript to perform the action,
	so the interaction the user experiences is completely done on the client-side (e.g., no server/Ajax calls here).</p>

    <p>For details and additional information, or to see a list of all <em>actions</em></strong> and <strong>events</strong>,
            please a see the <strong>documentation</strong> section of the QCubed-4 website.</p>
</div>

<div id="demoZone">
	<style>
		.panelHover { background-color: #eeeeff; border:1px solid #000078; width: 400px; padding: 10px;}
		.panelHighlight { background-color: #ffeeee; border-color: #780000; cursor: pointer;}
	</style>
	
	<table>
		<tr>
			<td colspan="2"><b>Set the Focus / Select to the Textbox</b> (Note that Select only works on \QCubed\Project\Control\TextBox)</td>
		</tr>
		<tr>
			<td style="width:250px;"><?php $this->btnFocus->render(); ?> <?php $this->btnSelect->render(); ?></td>
			<td><?php $this->txtFocus->render(); ?></td>
		</tr>
		<tr>
			<td colspan="2"><br/><b>Set the Display on the Textbox</b></td>
		</tr>
		<tr>
			<td style="width:250px;"><?php $this->btnToggleDisplay->render(); ?></td>
			<td><?php $this->txtDisplay->render(); ?></td>
		</tr>
		<tr>
			<td colspan="2"><br/><b>Set the Enabled on the Textbox</b></td>
		</tr>
		<tr>
			<td style="width:250px;"><?php $this->btnToggleEnable->render(); ?></td>
			<td><?php $this->txtEnable->render(); ?></td>
		</tr>
	</table>

	<p><?php $this->pnlHover->render(); ?></p>
	<p>Override a single CSS property using <strong>CssAction</strong>:</p>
	<p><?php $this->btnCssAction->render(); ?></p>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>