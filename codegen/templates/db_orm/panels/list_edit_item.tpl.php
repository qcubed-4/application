<?php
use QCubed\Project\Codegen\CodegenBase;

?>

protected function editItem($strKey = null) {
<?php

if ($blnUseDialog) { ?>
		$this->dlgEdit->load($strKey);
		$this->dlgEdit->open();
<?php
	}
	  elseif (CodegenBase::$CreateMethod == 'queryString') {
?>
		$strQuery = '';
		if ($strKey) {
<?php 	if (count($objTable->PrimaryKeyColumnArray) == 1) { ?>
			$strQuery =  '?<?php echo $objTable->PrimaryKeyColumnArray[0]->VariableName?>=' . $strKey;
<?php 	} else { ?>
			$keys = explode (':', $strKey);
<?php 		for($i = 0; $i < count($objTable->PrimaryKeyColumnArray); $i++) { ?>
			$params['<?=$objTable->PrimaryKeyColumnArray[$i]->VariableName?>'] = $keys[<?= $i ?>];
<?php 		} ?>
			$strQuery = '?' . http_build_query($params, '', '&');
<?php 	} ?>
		}
		$strEditPageUrl = QCUBED_FORMS_URL . '/<?php echo \QCubed\QString::underscoreFromCamelCase($strPropertyName) ?>_edit.php' . $strQuery;
		Application::redirect($strEditPageUrl);
<?php }
	else {	// pathinfo type request
?>
		$strQuery = '';
		if ($strKey) {
<?php 	if (count($objTable->PrimaryKeyColumnArray) == 1) { ?>
			$strQuery =  '/' . $strKey;
<?php 	} else { ?>
			$keys = explode (':', $strKey);
<?php 		for($i = 0; $i < count($objTable->PrimaryKeyColumnArray); $i++) { ?>
			$params['<?=$objTable->PrimaryKeyColumnArray[$i]->VariableName?>'] = $keys[<?= $i ?>];
<?php 		} ?>
		$strQuery = '/' . implode('/', $keys);
<?php 	} ?>
		}
		$strEditPageUrl = QCUBED_FORMS_URL . '/<?php echo \QCubed\QString::underscoreFromCamelCase($strPropertyName) ?>_edit.php' . $strQuery;
		Application::redirect($strEditPageUrl);
<?php }?>
	}
