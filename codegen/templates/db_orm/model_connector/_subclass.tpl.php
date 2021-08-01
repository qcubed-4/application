<?php
	/** @var QSqlTable $objTable */
	/** @var \QCubed\Codegen\DatabaseCodeGen $objCodeGen */
	global $_TEMPLATE_SETTINGS;
	$_TEMPLATE_SETTINGS = array(
		'OverwriteFlag' => false,
		'DirectorySuffix' => '',
		'TargetDirectory' => QCUBED_PROJECT_MODELCONNECTOR_DIR,
		'TargetFileName' => $objTable->ClassName . 'Connector.php'
	);
?>
<?php print("<?php\n"); ?>

require(QCUBED_PROJECT_MODELCONNECTOR_GEN_DIR . '/<?= $objTable->ClassName ?>ConnectorGen.php');

/**
 * This is a ModelConnector customizable subclass, providing a Form or Panel access to event handlers
 * and QControls to perform the Create, Edit, and Delete functionality of the
 * <?= $objTable->ClassName ?> class.  This code-generated class extends from
 * the generated ModelConnector class, which contains all the basic elements to help a Panel or Form
 * display an HTML form that can manipulate a single <?= $objTable->ClassName ?> object.
 *
 * To take advantage of some (or all) of these control objects, you
 * must create a new Form or Panel which instantiates a <?= $objTable->ClassName ?>ModelConnector
 * class.
 *
 * This file is intended to be modified.  Subsequent code regenerations will NOT modify
 * or overwrite this file.
 */
class <?= $objTable->ClassName ?>Connector extends <?= $objTable->ClassName ?>ConnectorGen
{
    <?php include("example_initialization.tpl.php"); ?>
}
