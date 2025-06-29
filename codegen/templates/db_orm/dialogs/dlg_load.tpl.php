<?php
// Create a parameter list
foreach ($objTable->PrimaryKeyColumnArray as $objColumn) {
    $params[] = '$' . $objColumn->VariableName;
    $paramsWithNull[] = '$' . $objColumn->VariableName . ' = null';
}
$strParams = implode(', ', $params);
$strParamsWithNull = implode(', ', $paramsWithNull);

?>
<?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) {
    // Override type specification:
    $displayType = $objColumn->VariableType;
    if ($displayType === 'integer') {
        $displayType = 'int';
    }
}
?>
    /**
    * Loads a <?= strtolower($strPropertyName) ?>'s information based on the provided ID and updates the UI accordingly
    *
    * @param <?= $displayType ?? $objColumn->VariableType ?>|null $<?=  $objColumn->VariableName ?> The ID of the <?= strtolower($strPropertyName) ?> to be loaded. If null, a new <?= strtolower($strPropertyName) ?> is being created.
    * @return void
    * @throws Caller
    * @throws DateMalformedStringException
    * @throws InvalidCast
    */
    public function load(?<?= $displayType ?? $objColumn->VariableType ?> <?= $strParamsWithNull ?>): void
    {
        $this->pnl<?= $strPropertyName ?>->load(<?= $strParams ?>);
        $blnIsNew = is_null($<?= $objTable->PrimaryKeyColumnArray[0]->VariableName ?>);
        $this->showHideButton('delete', !$blnIsNew);    // show delete button if editing a previous record.

        if ($blnIsNew) {
            $strTitle = t('New') . ' ';
        } else {
            $strTitle = t('Edit') . ' ';
        }

        $strTitle .= '<?= $objCodeGen->dataListItemName($objTable) ?>';
        $this->Title = $strTitle;
    }
