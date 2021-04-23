<?php
use QCubed\Project\Codegen\CodegenBase as QCodegen;
use QCubed\QString;

	/** @var QSqlTable $objTable */
	/** @var QDatabaseCodeGen $objCodeGen */
	global $_TEMPLATE_SETTINGS;

	$strPropertyName = QCodeGen::dataListPropertyName($objTable);

	$_TEMPLATE_SETTINGS = array(
		'OverwriteFlag' => true,	// TODO: Change to false
		'DirectorySuffix' => '',
        'TargetDirectory' => QCUBED_FORMS_DIR,
		'TargetFileName' => QString::underscoreFromCamelCase($objTable->ClassName) . '_edit.php'
	);
?>
<?php print("<?php\n"); ?>

use QCubed as Q;
use QCubed\Project\Application;
use QCubed\Project\Control\Dialog;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\ControlBase;

// Load the QCubed Development Framework
require('../qcubed.inc.php');

require(QCUBED_PROJECT_PANEL_DIR . '/<?= $objTable->ClassName ?>EditPanel.php');

/**
 * This is a draft FormBase object to do Create, Edit, and Delete functionality
 * of the <?= $objTable->ClassName ?> class.  It uses the code-generated
 * <?= $objTable->ClassName ?>Connector class, which has methods to help with
 * easily creating/defining controls to modify the fields of <?= $objTable->ClassName ?> columns.
 *
 * Any display customizations and presentation-tier logic can be implemented
 * here by overriding existing or implementing new methods, properties and variables.
 */
class <?= $objTable->ClassName ?>EditForm extends FormBase
{

<?php include ('edit_protected_member_variables.tpl.php'); ?>

	// Override Form Event Handlers as Needed
	protected function formRun() {
		parent::formRun();

        // If your app requires a login, or some other kind of authroization step, this is the place to do that
		Application::checkAuthorized();
	}

//	protected function formLoad() {}

<?php include ('edit_form_create.tpl.php'); ?>

<?php include ('edit_create_buttons.tpl.php'); ?>

<?php include ('edit_button_click.tpl.php'); ?>

}

// Go ahead and run this form object to render the page and its event handlers, implicitly using
// <?= QString::underscoreFromCamelCase($strPropertyName) ?>_edit.tpl.php as the included HTML template file
<?= $strPropertyName ?>EditForm::run('<?= $strPropertyName ?>EditForm');
