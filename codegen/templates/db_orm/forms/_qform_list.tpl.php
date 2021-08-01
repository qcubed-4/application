<?php
use QCubed\Project\Codegen\CodegenBase as QCodegen;
use QCubed\QString;

/** @var QSqlTable $objTable */
    /** @var QDatabaseCodeGen $objCodeGen */
    global $_TEMPLATE_SETTINGS;
    $_TEMPLATE_SETTINGS = array(
        'OverwriteFlag' => true,    // TODO: Change to false
        'DirectorySuffix' => '',
        'TargetDirectory' => QCUBED_FORMS_DIR,
        'TargetFileName' => QString::underscoreFromCamelCase($objTable->ClassName) . '_list.php'
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

require(QCUBED_PROJECT_PANEL_DIR . '/<?= $objTable->ClassName ?>ListPanel.php');

/**
 * This is a draft FormBase object to do the List All functionality
 * of the <?= $objTable->ClassName ?> class, and is a starting point for the form object.
 *
 * Any display customizations and presentation-tier logic can be implemented
 * here by overriding existing or implementing new methods, properties and variables.
 */
class <?= $objTable->ClassName ?>ListForm extends FormBase
{
    protected $pnlNav;
    protected $pnl<?= $objTable->ClassName ?>List;

    // Override Form Event Handlers as Needed
    protected function formRun() {
        parent::formRun();

        // If your app requires a login, or some other kind of authroization step, this is the place to do that
        Application::checkAuthorized();
    }

    protected function formCreate() {
        $this->pnlNav = new NavPanel($this);
        $this->pnl<?= $objTable->ClassName ?>List = new <?= $objTable->ClassName ?>ListPanel($this);
    }
}

// Go ahead and run this form object to generate the page and event handlers, implicitly using
// <?= QString::underscoreFromCamelCase($objTable->ClassName) ?>_list.tpl.php as the included HTML template file
<?= $objTable->ClassName ?>ListForm::run('<?= $objTable->ClassName ?>ListForm');