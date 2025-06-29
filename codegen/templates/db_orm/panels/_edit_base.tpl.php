<?php
/** @var SqlTable $objTable */
use QCubed\Project\Codegen\CodegenBase;

/** @var \QCubed\Codegen\DatabaseCodeGen $objCodeGen */
global $_TEMPLATE_SETTINGS;
$_TEMPLATE_SETTINGS = array(
    'OverwriteFlag' => true,
    'DirectorySuffix' => '',
    'TargetDirectory' => QCUBED_PROJECT_PANEL_GEN_DIR,
    'TargetFileName' => $objTable->ClassName . 'EditPanelGen.php'
);

$strPropertyName = CodegenBase::dataListPropertyName($objTable);
?>
<?php print("<?php\n"); ?>

use QCubed\Control\Panel;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;

use QCubed\Control\Label;
use QCubed\Project\Control\TextBox;
use QCubed\Control\IntegerTextBox;
use QCubed\Control\FloatTextBox;
use QCubed\Project\Control\ListBox;
use QCubed\Control\CheckboxList;
use QCubed\Project\Control\Checkbox;
use QCubed\Control\DateTimePicker;

require (QCUBED_PROJECT_MODELCONNECTOR_DIR . '/<?= $strPropertyName ?>Connector.php');

/**
 * This is the base class for the <?php echo $objTable->ClassName  ?>EditPanel class. It uses the code-generated
 * <?php echo $objTable->ClassName  ?>ModelConnector class, which has methods to help with
 * easily creating/defining controls to modify the fields of a <?php echo $objTable->ClassName  ?> column.
 *
 * Implement your customizations in the <?php echo $objTable->ClassName  ?>EditPanel.php file, not here.
 * This file is overwritten every time you do a code generation, so any changes you make here will be lost.
 */
class <?= $strPropertyName ?>EditPanelGen extends Panel
{
<?php include("edit_protected_member_variables.tpl.php"); ?>
<?php include("edit_constructor.tpl.php"); ?>

<?php include("edit_create_objects.tpl.php"); ?>

<?php include("edit_load.tpl.php"); ?>

<?php include("edit_refresh.tpl.php"); ?>

<?php include("edit_save.tpl.php"); ?>

<?php include("edit_delete.tpl.php"); ?>

<?php include("edit_validate_unique.tpl.php"); ?>
}
