<?php
/**
 * @var SqlTable $objTable
 * @var CodeGenBase $objCodeGen
 */
?>
    /**
    * Refresh this ModelConnector with Data from the local <?= $objTable->ClassName ?> object.
    * @param boolean $blnReload reload <?= $objTable->ClassName ?> from the database
    * @return void
    * @throws Caller
    * @throws DateMalformedStringException
    * @throws InvalidCast
    */
    public function refresh(?bool $blnReload = false): void
    {
        if ($blnReload) {
            $this-><?= $objCodeGen->ModelVariableName($objTable->Name); ?>->reload();
        }

<?php

        foreach ($objTable->ColumnArray as $objColumn) {
            if ($objColumn->Options && isset($objColumn->Options['FormGen']) && $objColumn->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;

            $objControlCodeGenerator = $objCodeGen->GetControlCodeGenerator($objColumn);
            echo $objControlCodeGenerator->ConnectorRefresh($objCodeGen, $objTable, $objColumn);

            if ($objControlCodeGenerator->GetControlClass() != 'QCubed\\Control\\Label' && (!isset($objColumn->Options['FormGen']) || $objColumn->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_BOTH)) {
                // also generate a QCubed\\Control\\Label for each control that is not defaulted as a label already
                echo \QCubed\Codegen\Generator\Label::Instance()->ConnectorRefresh($objCodeGen, $objTable, $objColumn);
            }
            echo "\n\n";
        }
        foreach ($objTable->ReverseReferenceArray as $objReverseReference) {
            if (isset($objReverseReference->Options['FormGen']) && $objReverseReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;
            if ($objReverseReference->Unique) {
                $objControlCodeGenerator = $objCodeGen->GetControlCodeGenerator($objReverseReference);
                echo $objControlCodeGenerator->ConnectorRefresh($objCodeGen, $objTable, $objReverseReference);
                if ($objControlCodeGenerator->GetControlClass() != 'QCubed\\Control\\Label' && (!isset($objReverseReference->Options['FormGen']) || $objReverseReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_BOTH)) {
                    // also generate a QCubed\\Control\\Label for each control that is not defaulted as a label already
                    echo \QCubed\Codegen\Generator\Label::Instance()->ConnectorRefresh($objCodeGen, $objTable, $objReverseReference);
                }
                echo "\n\n";
            }
        }
        foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) {
            if (isset($objManyToManyReference->Options['FormGen']) && $objManyToManyReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;

            $objControlCodeGenerator = $objCodeGen->GetControlCodeGenerator($objManyToManyReference);
            echo $objControlCodeGenerator->ConnectorRefresh($objCodeGen, $objTable, $objManyToManyReference);
            if ($objControlCodeGenerator->GetControlClass() != 'QCubed\\Control\\Label' && (!isset($objManyToManyReference->Options['FormGen']) || $objManyToManyReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_BOTH)) {
                // also generate a QCubed\\Control\\Label for each control that is not defaulted as a label already
                echo \QCubed\Codegen\Generator\Label::Instance()->ConnectorRefresh($objCodeGen, $objTable, $objManyToManyReference);
            }
            echo "\n\n";
        }
?>
    }

<?php
// All the parameterization, type detection, and verification logic together
$aParamTypes = [];
$aDocTypes = [];
$aChecks = [];

foreach ($objTable->PrimaryKeyColumnArray as $objColumn) {
    if ($objColumn->VariableType === 'string') {
        $typeHint = '?string';
        $docType = 'null|string';
        $check = '($'.$objColumn->VariableName.' !== null && $'.$objColumn->VariableName.' !== \'\')';
    } elseif (
        in_array($objColumn->VariableType, ['int', 'integer'])
    ) {
        $typeHint = '?int';
        $docType = 'null|int';
        $check = '$'.$objColumn->VariableName.' !== null';
    } elseif (
        in_array($objColumn->VariableType, ['float', 'double', 'real'])
    ) {
        $typeHint = '?float';
        $docType = 'null|float';
        $check = '$'.$objColumn->VariableName.' !== null';
    } elseif (
        in_array($objColumn->VariableType, ['bool', 'boolean'])
    ) {
        // Boolean: do you allow null or always bool? If the key is NOT nullable, you can replace "?bool" with "bool"
        $typeHint = '?bool';
        $docType = 'null|bool';
        $check = '$'.$objColumn->VariableName.' !== null';
    } else {
        $typeHint = '?'.$objColumn->VariableType;
        $docType = 'null|'.$objColumn->VariableType;
        $check = '$'.$objColumn->VariableName.' !== null';
    }
    $aParamTypes[] = $typeHint.' $'.$objColumn->VariableName.' = null';
    $aDocTypes[] = '    * @param '.$docType.' $'.$objColumn->VariableName;
    $aChecks[] = $check;
}
?>
    /**
    * Load this ModelConnector with a <?= $objTable->ClassName ?> object. Returns the object found, or null if not
    * successful. The primary reason for failure would be that the key given does not exist in the database. This
    * might happen due to a programming error, or in a multi-user environment, if the record was recently deleted.
<?= implode("\n", $aDocTypes)."\n"; ?>
    * @param array|null $objClauses
    * @return null|<?= $objCodeGen->ModelClassName($objTable->Name); ?>

    * @throws Caller
    * @throws DateMalformedStringException
    * @throws InvalidCast
    */
    public function load(<?= implode(", ", $aParamTypes); ?>, ?array $objClauses = null): ?<?= $objCodeGen->ModelClassName($objTable->Name); ?>

    {
        if (<?php foreach ($aChecks as $i => $check): ?><?= $check ?><?= $i < count($aChecks)-1 ? " &&" : "" ?><?php endforeach; ?>) {
            $this-><?= $objCodeGen->ModelVariableName($objTable->Name); ?> = <?= $objTable->ClassName ?>::load(<?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn): ?>$<?= $objColumn->VariableName ?>,<?php endforeach; ?> $objClauses);
            $this->strTitleVerb = t('Edit');
            $this->blnEditMode = true;
        } else {
            $this-><?= $objCodeGen->ModelVariableName($objTable->Name); ?> = new <?= $objTable->ClassName ?>();
            $this->strTitleVerb = t('Create');
            $this->blnEditMode = false;
        }

        $this->refresh();
        return $this-><?= $objCodeGen->ModelVariableName($objTable->Name); ?>;
    }