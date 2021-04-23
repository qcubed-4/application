<?php
	/**
	 * @var QSqlTable $objTable
	 * @var QCodeGenBase $objCodeGen
	 */

use QCubed\ModelConnector\Options;

foreach ($objTable->ColumnArray as $objColumn) {
		if ($objColumn->Options && isset($objColumn->Options['FormGen']) && $objColumn->Options['FormGen'] == Options::FORMGEN_NONE) continue;

		$objControlCodeGenerator = $objCodeGen->getControlCodeGenerator($objColumn);
		echo $objControlCodeGenerator->connectorCreate($objCodeGen, $objTable, $objColumn);
		if ($objControlCodeGenerator->getControlClass() != 'QCubed\\Control\\Label' && (!isset($objColumn->Options['FormGen']) || $objColumn->Options['FormGen'] == Options::FORMGEN_BOTH)) {
			// also generate a QCubed\\Control\\Label for each control that generates both
			echo \QCubed\Codegen\Generator\Label::instance()->connectorCreate($objCodeGen, $objTable, $objColumn);
		}
		echo "\n\n";
	}


	foreach ($objTable->ReverseReferenceArray as $objReverseReference) {
		if (!$objReverseReference->Unique) continue;
		if (isset($objReverseReference->Options['FormGen']) && $objReverseReference->Options['FormGen'] == Options::FORMGEN_NONE) continue;

		$objControlCodeGenerator = $objCodeGen->getControlCodeGenerator($objReverseReference);
		echo $objControlCodeGenerator->connectorCreate($objCodeGen, $objTable, $objReverseReference);

		if ($objControlCodeGenerator->getControlClass() != 'QCubed\\Control\\Label' && (!isset($objReverseReference->Options['FormGen']) || $objReverseReference->Options['FormGen'] == Options::FORMGEN_BOTH)) {
			// also generate a QCubed\\Control\\Label for each control that generates both
			echo \QCubed\Codegen\Generator\Label::instance()->connectorCreate($objCodeGen, $objTable, $objReverseReference);
		}
		echo "\n\n";
	}

	foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) {
		if (isset($objManyToManyReference->Options['FormGen']) && $objManyToManyReference->Options['FormGen'] == Options::FORMGEN_NONE) continue;

		$objControlCodeGenerator = $objCodeGen->getControlCodeGenerator($objManyToManyReference);
		echo $objControlCodeGenerator->connectorCreate($objCodeGen, $objTable, $objManyToManyReference);

		if ($objControlCodeGenerator->getControlClass() != 'QCubed\\Control\\Label' && (!isset($objManyToManyReference->Options['FormGen']) || $objManyToManyReference->Options['FormGen'] == Options::FORMGEN_BOTH)) {
			// also generate a QCubed\\Control\\Label for each control that generates both
			echo \QCubed\Codegen\Generator\Label::instance()->connectorCreate($objCodeGen, $objTable, $objManyToManyReference);
		}
		echo "\n\n";
	}