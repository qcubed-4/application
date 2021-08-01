<?php
	/**
	 * @var QSqlTable $objTable
	 * @var QCodeGenBase $objCodeGen
	 */
?>

    /**
     * @var <?= $objTable->ClassName; ?> <?= $objCodeGen->modelVariableName($objTable->Name); ?>

     * @access protected
     */
    protected $<?= $objCodeGen->modelVariableName($objTable->Name); ?>;
    /**
     * @var FormBase|ControlBase
     * @access protected
     */
    protected $objParentObject;
    /**
     * @var string strTitleVerb
     * @access protected
     */
    protected $strTitleVerb;
    /**
     * @var boolean blnEditMode
     * @access protected
     */
    protected $blnEditMode;

    // Controls that correspond to <?= $objTable->ClassName ?>'s individual data fields
<?php foreach ($objTable->ColumnArray as $objColumn) {
	if (isset($objColumn->Options['FormGen']) && $objColumn->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;

	$objControlCodeGenerator = $objCodeGen->getControlCodeGenerator($objColumn);
	echo $objControlCodeGenerator->connectorVariableDeclaration($objCodeGen, $objColumn);

	if ($objControlCodeGenerator->getControlClass() != 'QCubed\\Control\\Label' && (!isset($objColumn->Options['FormGen']) || $objColumn->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_BOTH)) {
		// also generate a QCubed\\Control\\Label for each control that is not defaulted as a label already
		echo \QCubed\Codegen\Generator\Label::instance()->connectorVariableDeclaration($objCodeGen, $objColumn);
	}
}
?>
<?php
	foreach ($objTable->ReverseReferenceArray as $objReverseReference) {
		if ($objReverseReference->Unique) $blnHasUnique = true;
	}
?>
<?php if (isset($blnHasUnique) || count($objTable->ManyToManyReferenceArray)) {?>

		// Controls to edit Unique ReverseReferences and ManyToMany References

<?php } ?>
<?php foreach ($objTable->ReverseReferenceArray as $objReverseReference) {
	if (!$objReverseReference->Unique) continue;
	if (isset($objReverseReference->Options['FormGen']) && $objReverseReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;
	$objControlCodeGenerator = $objCodeGen->getControlCodeGenerator($objReverseReference);
	echo $objControlCodeGenerator->connectorVariableDeclaration($objCodeGen, $objReverseReference);

	if ($objControlCodeGenerator->getControlClass() != 'QCubed\\Control\\Label' && (!isset($objReverseReference->Options['FormGen']) || $objReverseReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_BOTH)) {
		// also generate a QCubed\\Control\\Label for each control that is not defaulted as a label already
		echo \QCubed\Codegen\Generator\Label::instance()->connectorVariableDeclaration($objCodeGen, $objReverseReference);
	}
} ?>
<?php foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) {
	if (isset($objManyToManyReference->Options['FormGen']) && $objManyToManyReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;
	$objControlCodeGenerator = $objCodeGen->getControlCodeGenerator($objManyToManyReference);
	echo $objControlCodeGenerator->connectorVariableDeclaration($objCodeGen, $objManyToManyReference);

	if ($objControlCodeGenerator->getControlClass() != 'QCubed\\Control\\Label' && (!isset($objManyToManyReference->Options['FormGen']) || $objManyToManyReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_BOTH)) {
	// also generate a QCubed\\Control\\Label for each control that is not defaulted as a label already
		echo \QCubed\Codegen\Generator\Label::instance()->connectorVariableDeclaration($objCodeGen, $objManyToManyReference);
	}
?>
    protected $str<?= $objManyToManyReference->ObjectDescription; ?>Glue = ', ';
<?php }