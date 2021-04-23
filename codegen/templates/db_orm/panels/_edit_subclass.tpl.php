<?php


    /** @var QSqlTable $objTable */
use QCubed\Project\Codegen\CodegenBase;

/** @var \QCubed\Codegen\DatabaseCodeGen $objCodeGen */
    global $_TEMPLATE_SETTINGS;
    $_TEMPLATE_SETTINGS = array(
        'OverwriteFlag' => false,
        'DirectorySuffix' => '',
        'TargetDirectory' => QCUBED_PROJECT_PANEL_DIR,
        'TargetFileName' => $objTable->ClassName . 'EditPanel.php'
    );

$strPropertyName = CodegenBase::dataListPropertyName($objTable);

?>
<?php print("<?php\n"); ?>
require(QCUBED_PROJECT_PANEL_GEN_DIR . '/<?= $strPropertyName ?>EditPanelGen.php');

/**
 * This is the customizable subclass for the edit panel functionality
 * of the <?= $strPropertyName ?> class. This is where you should create your customizations to the edit
 * panel that edits a <?= $objTable->Name ?> record.
 *
 * This file is intended to be modified. Subsequent code regenerations will NOT modify
 * or overwrite this file.
 */
class <?= $strPropertyName ?>EditPanel extends <?= $strPropertyName ?>EditPanelGen
{
	public function __construct($objParent, $strControlId = null) {
		parent::__construct($objParent, $strControlId);

		// Set AutoRenderChildren in order to use the PreferredRenderMethod attribute in each control
		// to render the controls. If you want more control, you can use the generated template
		// instead in your superclass and modify the template.
		$this->AutoRenderChildren = true;

		//$this->Template = QCUBED_PROJECT_PANEL_GEN_DIR . '/<?php echo $strPropertyName  ?>EditPanel.tpl.php';
	}
}
