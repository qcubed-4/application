<?php
use QCubed\Project\Codegen\CodegenBase as QCodegen;
use QCubed\QString;

/** @var QSqlTable $objTable */
    /** @var QDatabaseCodeGen $objCodeGen */
    global $_TEMPLATE_SETTINGS;

    $strPropertyName = QCodeGen::dataListPropertyName($objTable);
    $strPropertyNamePlural = QCodeGen::dataListPropertyNamePlural($objTable);
    $strVarName = 'pnl' . $strPropertyName . 'List';

    $_TEMPLATE_SETTINGS = array(
        'OverwriteFlag' => true,
        'DirectorySuffix' => '',
        'TargetDirectory' => QCUBED_FORMS_DIR,
        'TargetFileName' => QString::underscoreFromCamelCase($objTable->ClassName) . '_list.tpl.php'
    );

    $codegenerator = $objCodeGen->getDataListCodeGenerator($objTable);

?>
<?php print("<?php\n"); ?>
// This is the HTML template include file (.tpl.php) for the <?= QString::underscoreFromCamelCase($objTable->ClassName) ?>_list.php
// form DRAFT page.  Remember that this is a DRAFT.  It is MEANT to be altered/modified.

// Be sure to move this file out of this directory before modifying to ensure that subsequent
// code re-generations do not overwrite your changes.

global $gObjectName;
global $gObjectNamePlural;

$gObjectName =  t('<?= $strPropertyName ?>');
$gObjectNamePlural =  t('<?= $strPropertyNamePlural ?>');

$strPageTitle = $gObjectName . ' ' . t('List');
require(QCUBED_CONFIG_DIR . '/header.inc.php');
?>

<?php print("<?php"); ?> $this->renderBegin() ?>

<?php print("<?php"); ?> $this->pnlNav->render(); ?>
<?php print("<?php"); ?> $this-><?= $strVarName ?>->render(); ?>


<?php print("<?php"); ?> $this->renderEnd() ?>

<?php print("<?php"); ?> require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>