	/**
<?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>
	 * @param null|<?= $objColumn->VariableType ?> $<?= $objColumn->VariableName ?>

<?php } ?>
	 **/
	public function load (<?php
			foreach ($objTable->PrimaryKeyColumnArray as $objColumn) {
				echo '$'. $objColumn->VariableName . ' = null, ';
			} GO_BACK(2);?>) {
		if (!$this->mct<?php echo $objTable->ClassName  ?>->Load (<?php
			foreach ($objTable->PrimaryKeyColumnArray as $objColumn) {
				echo '$'. $objColumn->VariableName . ', ';
			} GO_BACK(2);?>)) {
			Application::displayAlert(t('Could not load the record. Perhaps it was deleted? Try again.'));
		}
	}

