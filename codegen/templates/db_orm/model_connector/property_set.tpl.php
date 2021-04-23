<?php
	/**
	 * @var QSqlTable $objTable
	 * @var QCodeGenBase $objCodeGen
	 */
?>

    /**
     * Override method to perform a property "Set"
     * This will set the property $strName to be $mixValue
     *
     * @param string $strName Name of the property to set
     * @param mixed $mixValue New value of the property
     * @return void
     * @throws Caller
     */
    public function __set($strName, $mixValue)
    {
        try {
            switch ($strName) {
                case 'Parent':
                    $this->objParentObject = $mixValue;
                    break;

                // Controls that point to <?= $objTable->ClassName ?> fields
<?php foreach ($objTable->ColumnArray as $objColumn) {
	if (isset($objColumn->Options['FormGen']) && $objColumn->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;
	$strControlVarName = $objCodeGen->modelConnectorVariableName($objColumn);
	$strPropertyName = $objColumn->PropertyName;

	$objControlCodeGenerator = $objCodeGen->getControlCodeGenerator($objColumn);
	$strClassName = $objControlCodeGenerator->getControlClass();
	$strLabelVarName = $objCodeGen->modelConnectorLabelVariableName($objColumn);
	include("property_set_case.tpl.php");
	print($objControlCodeGenerator->connectorSet($objCodeGen, $objTable, $objColumn));
} ?>
<?php foreach ($objTable->ReverseReferenceArray as $objReverseReference) { ?><?php if ($objReverseReference->Unique) { ?><?php
	if (isset($objReverseReference->Options['FormGen']) && $objReverseReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;
	$strControlVarName = $objCodeGen->modelConnectorVariableName($objReverseReference);
	$strPropertyName = $objReverseReference->ObjectDescription;
	$strClassName = $objCodeGen->getControlCodeGenerator($objReverseReference)->getControlClass();
	$strLabelVarName = $objCodeGen->modelConnectorLabelVariableName($objReverseReference);
?><?php include("property_set_case.tpl.php"); ?>

<?php } ?><?php } ?>
<?php foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) { ?><?php
	if (isset($objManyToManyReference->Options['FormGen']) && $objManyToManyReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;
	$strControlVarName = $objCodeGen->modelConnectorVariableName($objManyToManyReference);
	$strPropertyName = $objManyToManyReference->ObjectDescription;
	$strClassName = $objCodeGen->getControlCodeGenerator($objManyToManyReference)->getControlClass();
	$strLabelVarName = $objCodeGen->modelConnectorLabelVariableName($objManyToManyReference);
?><?php include("property_set_case.tpl.php"); ?>

<?php } ?>
                default:
                    parent::__set($strName, $mixValue);
                    break;
            }
        } catch (Caller $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
    }