<?php
use QCubed\Project\Codegen\CodegenBase;

?>
    /**
    * Redirects to the edit page for a specific item based on the provided key.
    *
    * @param string|null $strKey An optional key used to generate the query string for the edit page URL. Defaults to null.
    * @return void
    * @throws Throwable
    */
    protected function editItem(?string $strKey = null): void
    {
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
<?php if (count($objTable->PrimaryKeyColumnArray) == 1) { ?>
            $strQuery =  '?<?php echo $objTable->PrimaryKeyColumnArray[0]->VariableName?>=' . $strKey;
<?php } else { ?>
            $keys = explode (':', $strKey);
<?php for($i = 0; $i < count($objTable->PrimaryKeyColumnArray); $i++) { ?>
            $params['<?=$objTable->PrimaryKeyColumnArray[$i]->VariableName?>'] = $keys[<?= $i ?>];
<?php } ?>
            $strQuery = '?' . http_build_query($params, '', '&');
<?php } ?>
        }

        $strEditPageUrl = QCUBED_FORMS_URL . '/<?php echo \QCubed\QString::underscoreFromCamelCase($strPropertyName) ?>_edit.php' . $strQuery;
        Application::redirect($strEditPageUrl);
<?php }
    else {	// pathinfo type request
?>
        $strQuery = '';
        if ($strKey) {
<?php if (count($objTable->PrimaryKeyColumnArray) == 1) { ?>
            $strQuery =  '/' . $strKey;
<?php } else { ?>
            $keys = explode (':', $strKey);
<?php for($i = 0; $i < count($objTable->PrimaryKeyColumnArray); $i++) { ?>
            $params['<?=$objTable->PrimaryKeyColumnArray[$i]->VariableName?>'] = $keys[<?= $i ?>];
<?php } ?>
        $strQuery = '/' . implode('/', $keys);
<?php } ?>
        }

        $strEditPageUrl = QCUBED_FORMS_URL . '/<?php echo \QCubed\QString::underscoreFromCamelCase($strPropertyName) ?>_edit.php' . $strQuery;
        Application::redirect($strEditPageUrl);
<?php }?>
    }