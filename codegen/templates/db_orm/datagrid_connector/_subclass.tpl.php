<?php

use QCubed\Project\Codegen\CodegenBase as QCodegen;

	/** @var QSqlTable $objTable */
	/** @var QDatabaseCodeGen $objCodeGen */
	global $_TEMPLATE_SETTINGS;
	$_TEMPLATE_SETTINGS = array(
		'OverwriteFlag' => false,
		'DirectorySuffix' => '',
		'TargetDirectory' => QCUBED_PROJECT_MODELCONNECTOR_DIR,
		'TargetFileName' => $objTable->ClassName . 'List.php'
	);
?>
<?php print("<?php\n"); ?>
	require(QCUBED_PROJECT_MODELCONNECTOR_GEN_DIR . '/<?= $objTable->ClassName ?>ListGen.php');

	/**
	 * This is the connector class for the List functionality
	 * of the <?= $objTable->ClassName ?> class.  This class extends
	 * from the generated <?= $objTable->ClassName ?>Gen class, which lists a collection
	 * of <?= $objTable->ClassName ?> objects from the database.
	 *
	 * This file is intended to be modified. In this file, you can override the functions in the
	 * <?= $objTable->ClassName ?>Gen class, and implement new functionality as need.
	 * Subsequent code regenerations will NOT modify or overwrite this file.
	 *
	 * @package <?= QCodeGen::$ApplicationName; ?>

	 * @subpackage ModelConnector
	 *
	 */
	class <?= $objTable->ClassName ?>List extends <?= $objTable->ClassName ?>ListGen {
	}