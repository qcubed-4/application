<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Using a Proxy to Receive Events</h1>

	<p>Sometimes you may want to create buttons, links or other HTML items which can "trigger" a Server or Ajax
		action without actually creating a QControl.  The typical example of this is if you want to dynamically
		create a large number of links or buttons (e.g. in a <strong>DataGrid</strong> or <strong>DataRepeater</strong>) which would trigger
		an action, but because the link/button doesn't have any other state (e.g. you'll never want to
		change its value or style, or you're comfortable doing this in pure javascript), you don't want to
		incur the overhead of creating a whole <strong>Control</strong> for each of these links or buttons.</p>

	<p>The way you can do this is by creating a <strong>Proxy</strong> on your <strong>Form</strong>, and attaching
		it to a link, button or other html item by rendering it specially.</p>

	<p>The example below illustrates the manual creation (see the code for more information) of a list of
		links which makes use of a single <strong>Proxy</strong> to trigger our event.  Notice that while there are many links
		and buttons which each trigger Ajax-based Actions, there is actually only 1 <strong>Proxy</strong>
		defined to handle all these events.</p>
</div>

<div id="demoZone">
	<p><em>Proxy</em>s can be rendered as links...</p>
	<p><?= $this->pxyExample->renderAsLink('Baz', 'Baz'); ?> |
		<?= $this->pxyExample->renderAsLink('Foo', 'Foo'); ?> |
		<?= $this->pxyExample->renderAsLink('Blah', 'Blah'); ?> |
		<?= $this->pxyExample->renderAsLink('Test', 'Test'); ?></p>
	<p>Or buttons...</p>
	<p><?= $this->pxyExample->renderAsButton('Baz', 'Baz'); ?> |
		<?= $this->pxyExample->renderAsButton('Foo', 'Foo'); ?> |
		<?= $this->pxyExample->renderAsButton('Blah', 'Blah'); ?> |
		<?= $this->pxyExample->renderAsButton('Test', 'Test'); ?></p>

	<p>Or embedded in any kind of tag.<p>
	<p><input type="checkbox" <?= $this->pxyExample->renderAttributes('Test 1') ?> > |
		<span <?= $this->pxyExample->renderAttributes('Test 2') ?> >Test 2</span>  |
		<input type="radio" <?= $this->pxyExample->renderAttributes('Test 3') ?> >

	<?php $this->lblMessage->render(); ?>
	<?php $this->pnlHover->render(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>