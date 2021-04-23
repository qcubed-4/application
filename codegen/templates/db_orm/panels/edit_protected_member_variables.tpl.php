<?php
use QCubed as Q;
?>
	/** @var <?= $strPropertyName ?>Connector */
	public $mct<?= $strPropertyName  ?>;

	// Controls for <?= $strPropertyName  ?>'s Data Fields
<?php foreach ($objTable->ColumnArray as $objColumn) { ?>
<?php	if (isset($objColumn->Options['FormGen']) && $objColumn->Options['FormGen'] == Q\ModelConnector\Options::FORMGEN_NONE) continue; ?>

	/** @var <?= $objCodeGen->ModelConnectorControlClass($objColumn) ?> */
	protected $<?= $objCodeGen->ModelConnectorVariableName($objColumn);  ?>;
<?php } ?>

<?php
	$blnHasUniqueReverse = false;
	foreach ($objTable->ReverseReferenceArray as $objReverseReference) {
		if ($objReverseReference->Unique) {
			$blnHasUniqueReverse = true;
			break;
		}
	}
	if ($blnHasUniqueReverse) {?>
	// Controls to edit unique reverse references

<?php } ?>
<?php foreach ($objTable->ReverseReferenceArray as $objReverseReference) { ?>
<?php if ($objReverseReference->Unique) { ?>
<?php	if (isset ($objReverseReference->Options['FormGen']) && ($objReverseReference->Options['FormGen'] == 'none' || $objReverseReference->Options['FormGen'] == 'meta')) continue; ?>
	/** @var <?= $objCodeGen->ModelConnectorControlClass($objReverseReference) ?> */
	protected $<?= $objCodeGen->ModelConnectorVariableName($objReverseReference);  ?>;
<?php } ?>
<?php } ?>
<?php if ($objTable->ManyToManyReferenceArray) {?>
	// Controls to edit many-to-many relationships

<?php } ?>
<?php foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) { ?>
<?php	if (isset ($objManyToManyReference->Options['FormGen']) && ($objManyToManyReference->Options['FormGen'] == 'none' || $objManyToManyReference->Options['FormGen'] == 'meta')) continue; ?>
	/**  @var <?= $objCodeGen->ModelConnectorControlClass($objManyToManyReference) ?>  */
	protected $<?= $objCodeGen->ModelConnectorVariableName($objManyToManyReference);  ?>;
<?php }