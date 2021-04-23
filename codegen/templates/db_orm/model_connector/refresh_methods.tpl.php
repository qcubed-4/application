<?php
/**
 * @var QSqlTable $objTable
 * @var QCodeGenBase $objCodeGen
 */
?>

    /**
     * Refresh this ModelConnector with Data from the local <?= $objTable->ClassName ?> object.
     * @param boolean $blnReload reload <?= $objTable->ClassName ?> from the database
     * @return void
     */
    public function refresh($blnReload = false)
    {
        assert($this-><?= $objCodeGen->ModelVariableName($objTable->Name); ?>); // Notify in development version
        if (!($this-><?= $objCodeGen->ModelVariableName($objTable->Name); ?>)) return; // Quietly fail in production

        if ($blnReload) {
            $this-><?= $objCodeGen->ModelVariableName($objTable->Name); ?>->Reload();
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
    foreach ($objTable->PrimaryKeyColumnArray as $objColumn) {
        $aStrs[] = '$' . $objColumn->VariableName . ' = null';
    }
?>
    /**
     * Load this ModelConnector with a <?= $objTable->ClassName ?> object. Returns the object found, or null if not
     * successful. The primary reason for failure would be that the key given does not exist in the database. This
     * might happen due to a programming error, or in a multi-user environment, if the record was recently deleted.
<?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>
     * @param null|<?= $objColumn->VariableType ?> $<?= $objColumn->VariableName ?>
<?php } ?>

     * @param $objClauses
     * @return null|<?= $objCodeGen->ModelClassName($objTable->Name); ?>

     */
    public function load(<?= implode (',', $aStrs) ?>, $objClauses = null)
    {
        if (<?php
foreach ($objTable->PrimaryKeyColumnArray as $objColumn) {
if ($objColumn->VariableType == \QCubed\Type::STRING) {
    $strCheck = 'strlen';
} else {
    $strCheck = '!is_null';
}?><?= $strCheck ?>($<?= $objColumn->VariableName  ?>) && <?php } ?><?php GO_BACK(4); ?>) {
            $this-><?= $objCodeGen->ModelVariableName($objTable->Name); ?> = <?= $objTable->ClassName ?>::Load(<?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>$<?= $objColumn->VariableName ?>, <?php } ?><?php GO_BACK(2); ?>, $objClauses);
            $this->strTitleVerb = t('Edit');
            $this->blnEditMode = true;
        }
        else {
            $this-><?= $objCodeGen->ModelVariableName($objTable->Name); ?> = new <?= $objTable->ClassName ?>();
            $this->strTitleVerb = t('Create');
            $this->blnEditMode = false;
        }
        if ($this-><?= $objCodeGen->ModelVariableName($objTable->Name); ?>) {
            $this->refresh ();
        }
        return $this-><?= $objCodeGen->ModelVariableName($objTable->Name); ?>;
    }
