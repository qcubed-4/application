<?php
	/**
	 * @var QSqlTable $objTable
	 * @var QCodeGenBase $objCodeGen
	 */
?>
 * @property-read <?= $objTable->ClassName ?> $<?= $objTable->ClassName ?> the actual <?= $objTable->ClassName ?> data class being edited
<?php 
	foreach ($objTable->ColumnArray as $objColumn) {
		if (isset($objColumn->Options['FormGen']) && $objColumn->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;
		$objGenerator = $objCodeGen->GetControlCodeGenerator($objColumn);
		$strClassName = $objGenerator->GetControlClass();
		$blnIsLabel = ($strClassName == 'QCubed\\Control\\Label');

		if (!$blnIsLabel && (!isset($objColumn->Options['FormGen']) || $objColumn->Options['FormGen'] != \QCubed\ModelConnector\Options::FORMGEN_LABEL_ONLY)) { ?>
 * @property <?= $strClassName; ?> $<?= $objColumn->PropertyName ?>Control
<?php 	}
		if ($blnIsLabel || !isset($objColumn->Options['FormGen']) || $objColumn->Options['FormGen'] != \QCubed\ModelConnector\Options::FORMGEN_CONTROL_ONLY) { ?>
 * @property-read QCubed\\Control\\Label $<?= $objColumn->PropertyName ?>Label
<?php 	}
		print ($objGenerator->ConnectorPropertyComments($objCodeGen, $objTable, $objColumn));
	}

	foreach ($objTable->ReverseReferenceArray as $objReverseReference) {
		if (!$objReverseReference->Unique || (isset($objReverseReference->Options['FormGen']) && $objReverseReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE)) continue;
		$strClassName = $objCodeGen->GetControlCodeGenerator($objReverseReference)->GetControlClass();
		$blnIsLabel = ($strClassName == 'QCubed\\Control\\Label');

		if (!$blnIsLabel && (!isset($objReverseReference->Options['FormGen']) || $objReverseReference->Options['FormGen'] != \QCubed\ModelConnector\Options::FORMGEN_LABEL_ONLY)) { ?>
 * @property <?= $strClassName; ?> $<?= $objReverseReference->ObjectDescription ?>Control
<?php 	}
		if ($blnIsLabel || !isset($objReverseReference->Options['FormGen']) || $objReverseReference->Options['FormGen'] != \QCubed\ModelConnector\Options::FORMGEN_CONTROL_ONLY) { ?>
 * @property-read QCubed\\Control\\Label $<?= $objReverseReference->ObjectDescription ?>Label
<?php
		} 
	} 
?>
<?php foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) {
		if (isset($objManyToManyReference->Options['FormGen']) && $objManyToManyReference->Options['FormGen'] == \QCubed\ModelConnector\Options::FORMGEN_NONE) continue;
		$strClassName = $objCodeGen->GetControlCodeGenerator($objManyToManyReference)->GetControlClass();
		$blnIsLabel = ($strClassName == 'QCubed\\Control\\Label');

		if (!$blnIsLabel && (!isset($objManyToManyReference->Options['FormGen']) || $objManyToManyReference->Options['FormGen'] != \QCubed\ModelConnector\Options::FORMGEN_LABEL_ONLY)) { ?>
 * @property <?= $strClassName; ?> $<?= $objManyToManyReference->ObjectDescription ?>Control
<?php 	}
		if ($blnIsLabel || !isset($objManyToManyReference->Options['FormGen']) || $objManyToManyReference->Options['FormGen'] != \QCubed\ModelConnector\Options::FORMGEN_CONTROL_ONLY) { ?>
 * @property-read QCubed\\Control\\Label $<?= $objManyToManyReference->ObjectDescription ?>Label
<?php
	 }
	}
?>
 * @property-read string $TitleVerb a verb indicating whether or not this is being edited or created
 * @property-read boolean $EditMode a boolean indicating whether or not this is being edited or created