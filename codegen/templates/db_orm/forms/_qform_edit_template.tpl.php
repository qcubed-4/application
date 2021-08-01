<?php
use QCubed\Project\Codegen\CodegenBase as QCodegen;
use QCubed\QString;

/** @var QSqlTable $objTable */
/** @var QDatabaseCodeGen $objCodeGen */
global $_TEMPLATE_SETTINGS;

$strPropertyName = QCodeGen::dataListPropertyName($objTable);
$strPropertyNamePlural = QCodeGen::dataListPropertyNamePlural($objTable);

$_TEMPLATE_SETTINGS = array(
    'OverwriteFlag' => true,
    'DirectorySuffix' => '',
    'TargetDirectory' => QCUBED_FORMS_DIR,
    'TargetFileName' => QString::underscoreFromCamelCase($objTable->ClassName) . '_edit.tpl.php'
);
?>
<?php print("<?php\n"); ?>
// This is the HTML template include file (.tpl.php) for the <?= QString::underscoreFromCamelCase($strPropertyName) ?>_edit.php
// Feel free to edit this as needed.
global $gObjectName;
global $gObjectNamePlural;

$gObjectName =  t('<?= $strPropertyName ?>');
$gObjectNamePlural =  t('<?= $strPropertyNamePlural ?>');

$strPageTitle = t('<?= $strPropertyName ?>');
require(QCUBED_CONFIG_DIR . '/header.inc.php');

?>
<?php print("<?php"); ?> $this->renderBegin() ?>

<h1><?php print("<?="); ?> t('<?= $strPropertyName ?>')?></h1>

<div class="form-controls">
	<?php print("<?="); ?> _r($this->pnl<?= $strPropertyName ?>); ?>
</div>

<div class="form-actions">
	<div class="form-save"><?php print("<?php"); ?> $this->btnSave->render(); ?></div>
	<div class="form-cancel"><?php print("<?php"); ?> $this->btnCancel->render(); ?></div>
	<div class="form-delete"><?php print("<?php"); ?> $this->btnDelete->render(); ?></div>
</div>

<?php print("<?php"); ?> $this->renderEnd() ?>

<?php print("<?php"); ?> require(QCUBED_CONFIG_DIR .'/footer.inc.php');