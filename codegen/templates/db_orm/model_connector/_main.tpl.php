<?php
	/** @var QSqlTable $objTable */
use QCubed\Project\Codegen\CodegenBase;

/** @var \QCubed\Codegen\DatabaseCodeGen $objCodeGen */
	global $_TEMPLATE_SETTINGS;
	$_TEMPLATE_SETTINGS = array(
		'OverwriteFlag' => true,
		'DirectorySuffix' => '',
		'TargetDirectory' => QCUBED_PROJECT_MODELCONNECTOR_GEN_DIR,
		'TargetFileName' => $objTable->ClassName . 'ConnectorGen.php'
	);
?>
<?php print("<?php\n"); ?>

<?php include ("imports.tpl.php"); ?>

/**
 * This is a ModelConnector class, providing a Form or Panel access to event handlers
 * and Controls to perform the Create, Edit, and Delete functionality
 * of the <?= $objTable->ClassName ?> class.  This code-generated class
 * contains all the basic elements to help a Panel or Form display an HTML form that can
 * manipulate a single <?= $objTable->ClassName ?> object.
 *
 * To take advantage of some (or all) of these control objects, you
 * must create a new Form or Panel which instantiates a <?= $objTable->ClassName ?>Connector
 * class.
 *
 * Any and all changes to this file will be overwritten with any subsequent
 * code re-generation.
 *
 * @package <?= CodegenBase::$ApplicationName; ?>

 * @subpackage ModelConnector
<?php include("property_comments.tpl.php"); ?>

 */

class <?= $objTable->ClassName ?>ConnectorGen extends \QCubed\ObjectBase
{
    <?php include("variable_declarations.tpl.php"); ?>


    <?php include("constructor.tpl.php"); ?>


<?php include("create_methods.tpl.php"); ?>


<?php include("refresh_methods.tpl.php"); ?>


<?php include("update_methods.tpl.php"); ?>


    <?php include("save_object.tpl.php"); ?>


    <?php include("delete_object.tpl.php"); ?>


    <?php include("property_get.tpl.php"); ?>


    <?php include("property_set.tpl.php"); ?>

}
