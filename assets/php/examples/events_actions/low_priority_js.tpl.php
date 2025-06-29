<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Executing JavaScript with low/high priority</h1>
	
	<p>In this example, you learn about executing JavaScript with <strong>Application::executeJsFunction()</strong> and
		<strong>Application::executeSelectorFunction()</strong> with different priority levels.</p>

	<p>You can execute JavaScript using one of three priority-levels: <strong> Application::PRIORITY_LOW , Application::PRIORITY_STANDARD and Application::PRIORITY_HIGH</strong>
	Scripts with higher priority-level will be placed in the JavaScript execution-queue before scripts with lower ones
	and scripts with equal priority level are executed in the order you send them. </p>
	
	<h2>QCubed-4 task order:</h2>
	
	<ul>
		<li>Render/update html</li>
		<li>Execute JavaScript functions with <strong>Application::PRIORITY_HIGH</strong>
		<li>Execute QActions attached to controls with Events</li>
		<li>Execute JavaScript functions with <strong>Application::PRIORITY_STANDARD</strong>
		<li>Execute JavaScript functions with <strong>Application::PRIORITY_LOW</strong>
	</ul>

	<p>Take a look at the example below. By clicking on one of the buttons, the
	datagrid gets updated and an alert box will show up.
	Try clicking on buttons of both rows and look at the different update-behaviour.<br/>
	The interesting code resides in the methods <strong>renderButton_Click</strong> and <strong>renderLowPriorityButton_Click</strong></p>
	
	<p>In these methods, the datagrid is marked as modified (render it again, including all the buttons),
	some JavaScript alert boxes will show up, and the color of the button changes due to
	adding a CSS class via JavaScript.
	The parameter <strong>Application::PRIORITY_LOW</strong> forces the script to be executed after all scripts with higher priority.</p>
	
	
	<p>When the buttons are (re)rendered they get their standard color applied (and the JavaScript returned by GetEndScript is executed again).
	If you hit an <strong>update & low priority alert</strong> button, the alert boxes have low priority,
	the JavaScript for adding the new CSS class is executed before the alerts show up
	and the color is changed immediately.
	When hitting an <strong>update & alert</strong> button, the color will be changed after the alert boxes show up because
	all scripts are executed with standard priority.</p>

	<h2>Strategies for executing Javascript</h2>
	The <strong>Application::executeJsFunction()</strong>, <strong>Application::executeSelectorFunction()</strong> and
	<strong>Application::executeControlCommand()</strong> functions are available to use invoke JavaScript in a number of ways.
	If these are not adequate, we recommend you put your JavaScript in a file, and invoke that JavaScript using one of the
	above functions.
</div>

<div id="demoZone">
	<?php $this->dtgButtons->render(); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>