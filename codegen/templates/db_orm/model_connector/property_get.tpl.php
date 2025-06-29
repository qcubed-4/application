<?php
	/**
	 * @var QSqlTable $objTable
	 * @var QCodeGenBase $objCodeGen
	 */
?>

    /**
    * Magic method to retrieve the value of a property by its name.
    *
    * @param string $strName The name of the property to retrieve.
    * @return mixed The value of the requested property, or dynamically created controls if applicable.
    * @throws Caller If the property does not exist or is not accessible.
    * @throws DateMalformedStringException
    * @throws InvalidCast
    * @throws UndefinedProperty
    */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            // General ModelConnectorVariables
            case '<?= $objTable->ClassName ?>': return $this-><?= $objCodeGen->ModelVariableName($objTable->Name); ?>;
            case 'TitleVerb': return $this->strTitleVerb;
            case 'EditMode': return $this->blnEditMode;

            // Controls that point to <?= $objTable->ClassName ?> fields -- will be created dynamically if not yet created
<?php foreach ($objTable->ColumnArray as $objColumn) { ?><?php
    if ($objColumn->Options && isset($objColumn->Options['FormGen']) && $objColumn->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;
    $strControlVarName = $objCodeGen->ModelConnectorVariableName($objColumn);
    $strLabelVarName = $objCodeGen->ModelConnectorLabelVariableName($objColumn);
    $strPropertyName = $objColumn->PropertyName;
    $objControlCodeGenerator = $objCodeGen->GetControlCodeGenerator($objColumn);
    $strClassName = $objControlCodeGenerator->GetControlClass();
?>
<?php include("property_get_case.tpl.php"); ?>
<?php print($objControlCodeGenerator->ConnectorGet($objCodeGen, $objTable, $objColumn)); ?>
<?php } ?>
<?php foreach ($objTable->ReverseReferenceArray as $objReverseReference) { ?><?php if ($objReverseReference->Unique) { ?><?php
	if (isset($objReverseReference->Options['FormGen']) && $objReverseReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;
	$strControlVarName = $objCodeGen->ModelConnectorVariableName($objReverseReference);
	$strLabelVarName = $objCodeGen->ModelConnectorLabelVariableName($objReverseReference);
	$strPropertyName = $objReverseReference->ObjectDescription;
	$strClassName = $objCodeGen->GetControlCodeGenerator($objReverseReference)->GetControlClass();
?><?php include("property_get_case.tpl.php"); ?>
<?php } ?><?php } ?>
<?php foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) { ?><?php
	if (isset($objManyToManyReference->Options['FormGen']) && $objManyToManyReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;
	$strControlVarName = $objCodeGen->ModelConnectorVariableName($objManyToManyReference);
	$strLabelVarName = $objCodeGen->ModelConnectorLabelVariableName($objManyToManyReference);
	$strPropertyName = $objManyToManyReference->ObjectDescription;
	$strClassName = $objCodeGen->GetControlCodeGenerator($objManyToManyReference)->GetControlClass();
?><?php include("property_get_case.tpl.php");
?>
<?php } ?>

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }