<?php
use QCubed\Action\Ajax;
use QCubed\Control\Label;
use QCubed\Event\Click;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

if (!isset($this)) {

	// Define the \QCubed\Project\Control\FormBase with all our Qcontrols
	class ExampleSingleForm extends FormBase {

		// Local declarations of our Qcontrols
		protected $lblMessage;
		protected $btnButton;

		// Initialize our Controls during the Form Creation process
		protected function formCreate() {
			// Define the Label
			$this->lblMessage = new Label($this);
			$this->lblMessage->Text = 'Click the button to change my message.';

			// Definte the Button
			$this->btnButton = new Button($this);
			$this->btnButton->Text = 'Click Me!';

			// Add a Click event handler to the button -- the action to run is an AjaxAction.
			// The AjaxAction names a PHP method (which will be run asynchronously) called "btnButton_Click"
			$this->btnButton->addAction(new Click(), new Ajax('btnButton_Click'));
		}

		// The "btnButton_Click" Event handler
		protected function btnButton_Click($strFormId, $strControlId, $strParameter) {
			$this->lblMessage->Text = 'Hello, world!';
		}

	}

	// Run the Form we have defined
	// Note that we explicitly specify the PHP variable __FILE__ (e.g. THIS script)
	// as the template file to use, and that we call "return;" to ensure that the rest
	// of this script doesn't process on its initial run.  Instead, it will be processed
	// a second time by QCubed as the \QCubed\Project\Control\FormBase is being rendered.
	ExampleSingleForm::run('ExampleSingleForm', __FILE__);
	return;
}

// Specify the Template below
?>

<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Hello World, Revisited... Again... Again</h1>

	<p>We use the AJAX-enabled "Hello World" example to explain how you can make single file <strong>\QCubed\Project\Control\FormBase</strong> pages.
		Note that this approach is <em>not always recommended</em> -- keeping the display logic (.php) file separate
		from the presentation HTML template (.tpl.php) file helps to enforce the good design and separation of display
		logic from the presentation layer (e.g. keeping the V and C separate in MVC).</p>

	<p>However, there may be times when you want a simpler architecture of single-file forms, or you
		are making some very simple <strong>\QCubed\Project\Control\FormBase</strong> pages and you do not want to deal with the overhead
		of the dual-file format.  This example shows how you can use built-in PHP functionality to code your
		<strong>\QCubed\Project\Control\FormBase</strong> as a single .php file.</p>

	<p>Feel free to <strong>View Source</strong> (the button below) to see how this is done.</p>
</div>

<div id="demoZone">
	<p><?php $this->lblMessage->render(); ?></p>
	<p><?php $this->btnButton->render(); ?></p>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>