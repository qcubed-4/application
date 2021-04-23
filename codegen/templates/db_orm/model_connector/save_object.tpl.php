<?php
/**
 * @var QSqlTable $objTable
 * @var QCodeGenBase $objCodeGen
 */
?>
    /**
    * This will update this object's <?= $objTable->ClassName; ?> instance,
    * updating only the fields which have had a control created for it.
    * @throws Caller
    */
    public function update<?= $objTable->ClassName; ?>()
    {
        try {
            // Update any fields for controls that have been created
<?php 	foreach ($objTable->ColumnArray as $objColumn) {
        if (isset($objColumn->Options['FormGen']) && $objColumn->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;
        $objControlCodeGenerator = $objCodeGen->getControlCodeGenerator($objColumn);
        echo $objControlCodeGenerator->connectorUpdate($objCodeGen, $objTable, $objColumn);
        echo "\n";
    }
?>

            // Update any UniqueReverseReferences for controls that have been created for it
<?php 	foreach ($objTable->ReverseReferenceArray as $objReverseReference) {
        if (!$objReverseReference->Unique) continue;
        if (isset($objReverseReference->Options['FormGen']) && $objReverseReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;

        $objControlCodeGenerator = $objCodeGen->getControlCodeGenerator($objReverseReference);
        echo $objControlCodeGenerator->connectorUpdate($objCodeGen, $objTable, $objReverseReference);
        echo "\n";
    }
?>

        } catch (Caller $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
    }

<?php
$blnNeedsTransaction = false;
foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) {
    if (isset ($objManyToManyReference->Options['FormGen']) && ($objManyToManyReference->Options['FormGen'] == 'none' || $objManyToManyReference->Options['FormGen'] == 'meta')) continue;
    $blnNeedsTransaction = true;
    break;
}
?>

    /**
     * This will save this object's <?= $objTable->ClassName; ?> instance,
     * updating only the fields which have had a control created for it.
     * @param bool $blnForceUpdate
     * @return mixed
     * @throws Caller
     */
    public function save<?= $objTable->ClassName; ?>($blnForceUpdate = false)
    {
        try {
            $this->update<?= $objTable->ClassName; ?>();
<?php if ($blnNeedsTransaction) { // no transaction needed ?>
            $objDatabase = <?= $objTable->ClassName; ?>::getDatabase();
            $objDatabase->transactionBegin();
<?php } ?>
            $id = $this-><?= $objCodeGen->modelVariableName($objTable->Name); ?>->save(false, $blnForceUpdate);

<?php foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) { ?>
<?php	if (isset ($objManyToManyReference->Options['FormGen']) && ($objManyToManyReference->Options['FormGen'] == 'none' || $objManyToManyReference->Options['FormGen'] == 'meta')) continue; ?>
            $this-><?= $objCodeGen->modelConnectorVariableName($objManyToManyReference); ?>_Update();
<?php } ?>
<?php if ($blnNeedsTransaction) { ?>
            $objDatabase->transactionCommit();
<?php } ?>
        } catch (Caller $objExc) {
<?php if ($blnNeedsTransaction) { ?>
            $objDatabase->transactionRollback();
<?php } ?>
            $objExc->incrementOffset();
            throw $objExc;
        }

        return $id;
    }