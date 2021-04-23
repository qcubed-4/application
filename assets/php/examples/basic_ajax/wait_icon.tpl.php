<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Spinners!</h1>

	<p>In this Hello World example, we add a <strong>WaitIcon</strong>, sometimes also known as "Spinners", or "Throbbers",
	which will be displayed during the entire Ajax call.</p>

	<p>To add the <strong>WaitIcon</strong>, you can define a <strong>DefaultWaitIcon</strong> in your form,
	passing in a <strong>WaitIcon</strong> object.  At this point forward, every <strong>Ajax</strong>
	will, by default, use the defined wait icon to be displayed during your Ajax call.</p>

	<p>This display can be overridden by either passing in <strong>null</strong> for the wait icon to your
	ajax action call, or alternatively you can pass in <i>another</i> <strong>WaitIcon</strong> object
	defined in your form.</p>

	<p>Be sure to remember to render your wait icon on your page!  (Note: artificial sleep/wait time
	has been added to the <strong>btnButton_Click</strong> method in order to illustrate the spinner in
	action)</p>
</div>

<div id="demoZone">
	<p><?php $this->lblMessage->render(); ?></p>
	<p><?php $this->btnButton->render(); ?> <?php $this->btnButton2->render(); ?></p>
	<p><?php $this->objDefaultWaitIcon->render('Position=absolute', 'Top=10px', 'Left=200px'); ?></p>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>