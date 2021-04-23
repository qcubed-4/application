<?php
/** @var QSqlTable $objTable */
use QCubed\Project\Codegen\CodegenBase;

/** @var \QCubed\Codegen\DatabaseCodeGen $objCodeGen */
global $_TEMPLATE_SETTINGS;

$strPropertyName = CodegenBase::dataListPropertyName($objTable);

$_TEMPLATE_SETTINGS = array(
    'OverwriteFlag' => true,
    'DirectorySuffix' => '',
    'TargetDirectory' => QCUBED_PROJECT_DIALOG_GEN_DIR,
    'TargetFileName' => $strPropertyName . 'EditDlgGen.php'
);


?>
<?php print("<?php\n"); ?>

use QCubed as Q;
use QCubed\Exception\Caller;
use QCubed\Database\Exception\OptimisticLocking;
use QCubed\Project\Jqui\Dialog;


include (QCUBED_PROJECT_PANEL_DIR . '/<?= $strPropertyName ?>EditPanel.php');

/**
* This is the <?= $strPropertyName ?>EditDlgGen class.  It uses the code-generated
* <?= $strPropertyName ?>EditPanel class, which has all the controls for editing
* a record in the <?= $objTable->Name ?> table.
*
*
* @package <?php echo CodegenBase::$ApplicationName; ?>

*/
class <?= $strPropertyName ?>EditDlgGen extends \QCubed\Project\Control\Dialog
{

<?php include('dlg_protected_member_variables.tpl.php'); ?>

<?php include('dlg_constructor.tpl.php'); ?>

<?php include('dlg_create_buttons.tpl.php'); ?>

<?php include('dlg_load.tpl.php'); ?>

<?php include('dlg_button_click.tpl.php'); ?>

<?php include('dlg_save.tpl.php'); ?>

}
