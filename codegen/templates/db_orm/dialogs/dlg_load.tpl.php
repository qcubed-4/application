<?php
	// Create a parameter list
foreach ($objTable->PrimaryKeyColumnArray as $objColumn) {
	$params[] = '$' . $objColumn->VariableName;
	$paramsWithNull[] = '$' . $objColumn->VariableName . ' = null';
}
$strParams = implode(', ', $params);
$strParamsWithNull = implode(', ', $paramsWithNull);

?>
	/**
	 * Load the dialog using primary keys.
	 *
<?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>
	 * @param null|<?= $objColumn->VariableType ?> $<?= $objColumn->VariableName ?>

<?php } ?>
	 */
	public function load(<?= $strParamsWithNull ?>)
    {
		$this->pnl<?= $strPropertyName ?>->load(<?= $strParams ?>);
		$blnIsNew = is_null($<?= $objTable->PrimaryKeyColumnArray[0]->VariableName ?>);
		$this->showHideButton('delete', !$blnIsNew);	// show delete button if editing a previous record.

		if ($blnIsNew) {
			$strTitle = t('New') . ' ';
		} else {
			$strTitle = t('Edit') . ' ';
		};
		$strTitle .= '<?= $objCodeGen->dataListItemName($objTable) ?>';
		$this->Title = $strTitle;
	}

