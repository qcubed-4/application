<?php

/** @var QSqlTable $objTable */
/** @var QDatabaseCodeGen $objCodeGen */
global $_TEMPLATE_SETTINGS;

use QCubed\Project\Codegen\CodegenBase as QCodegen;

$strPropertyName = QCodeGen::dataListPropertyName($objTable);
$strClassName = QCodeGen::dataListControlClass($objTable);
$listCodegenerator = $objCodeGen->getDataListCodeGenerator($objTable);
$strListVarName = $objCodeGen->dataListVarName($objTable);
$options = $objTable->Options;

$_TEMPLATE_SETTINGS = array(
    'OverwriteFlag' => true,
    'DirectorySuffix' => '',
    'TargetDirectory' => QCUBED_PROJECT_MODELCONNECTOR_GEN_DIR,
    'TargetFileName' => $objTable->ClassName . 'ListGen.php'
);

?>
<?php print("<?php\n"); ?>

<?= $listCodegenerator->dataListImports($objCodeGen, $objTable) ?>

/**
 * This is the generated connector class for the List functionality
 * of the <?= $objTable->ClassName ?> class.  This code-generated class
 * subclasses a <?= $strPropertyName ?> class and can be used to display
 * a collection of <?= $objTable->ClassName ?> objects.
 *
 * To take advantage of some (or all) of these control objects, you
 * must create an instance of this object in a QForm or QPanel.
 *
 * Any and all changes to this file will be overwritten with any subsequent re-
 * code generation.
 *
<?= $listCodegenerator->dataListConnectorComments($objCodeGen, $objTable); ?>
 *
 */
class <?= $objTable->ClassName ?>ListGen extends <?= $strClassName ?>

{
<?= $listCodegenerator->dataListConnector($objCodeGen, $objTable); ?>
}
