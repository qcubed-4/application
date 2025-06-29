<?php
use QCubed as Q;
?>
    /** @var <?= $strPropertyName ?>Connector */
    public <?= $strPropertyName ?>Connector $mct<?= $strPropertyName  ?>;

    // Controls for <?= $strPropertyName  ?>'s Data Fields
<?php foreach ($objTable->ColumnArray as $objColumn) { ?>
<?php if (isset($objColumn->Options['FormGen']) && $objColumn->Options['FormGen'] == Q\ModelConnector\Options::FORMGEN_NONE) continue; ?>

<?php

    $displayType = $objCodeGen->ModelConnectorControlClass($objColumn);

    if ($objCodeGen->ModelConnectorControlClass($objColumn) === '\QCubed\Control\Label') {
        $displayType = 'Label';
    }
    if ($objCodeGen->ModelConnectorControlClass($objColumn) === '\QCubed\Project\Control\TextBox') {
        $displayType = 'TextBox';
    }
    if ($objCodeGen->ModelConnectorControlClass($objColumn) === '\QCubed\Project\Control\ListBox') {
        $displayType = 'ListBox';
    }
    if ($objCodeGen->ModelConnectorControlClass($objColumn) === '\QCubed\Control\CheckboxList') {
        $displayType = 'CheckboxList';
    }
    if ($objCodeGen->ModelConnectorControlClass($objColumn) === '\QCubed\Project\Control\Checkbox') {
        $displayType = 'Checkbox';
    }
    if ($objCodeGen->ModelConnectorControlClass($objColumn) === '\QCubed\Control\IntegerTextBox') {
        $displayType = 'IntegerTextBox';
    }
    if ($objCodeGen->ModelConnectorControlClass($objColumn) === '\QCubed\Control\DateTimePicker') {
        $displayType = 'DateTimePicker';
    }
    if ($objCodeGen->ModelConnectorControlClass($objColumn) === '\QCubed\Control\FloatTextBox') {
        $displayType = 'FloatTextBox';
    }

 ?>
    /** @var <?= $displayType ?? $objCodeGen->ModelConnectorControlClass($objColumn) ?> */
    protected <?= $displayType ?? $objCodeGen->ModelConnectorControlClass($objColumn) ?> $<?=  $objCodeGen->ModelConnectorVariableName($objColumn);  ?>;
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
<?php

$displayType = $objCodeGen->ModelConnectorControlClass($objReverseReference);

if ($objCodeGen->ModelConnectorControlClass($objReverseReference) === '\QCubed\Project\Control\ListBox') {
    $displayType = 'ListBox';
}
if ($objCodeGen->ModelConnectorControlClass($objReverseReference) === '\QCubed\Control\CheckboxList') {
    $displayType = 'CheckboxList';
}
?>
<?php if ($objReverseReference->Unique) { ?>
<?php if (isset ($objReverseReference->Options['FormGen']) && ($objReverseReference->Options['FormGen'] == 'none' || $objReverseReference->Options['FormGen'] == 'meta')) continue; ?>

    /** @var <?=  $displayType ?? $objCodeGen->ModelConnectorControlClass($objReverseReference) ?> */
    protected <?=  $displayType ?? $objCodeGen->ModelConnectorControlClass($objReverseReference) ?> $<?= $objCodeGen->ModelConnectorVariableName($objReverseReference);  ?>;

<?php } ?>
<?php } ?>
<?php if ($objTable->ManyToManyReferenceArray) {?>
    // Controls to edit many-to-many relationships
<?php } ?>
<?php foreach ($objTable->ManyToManyReferenceArray as $objManyToManyReference) { ?>
<?php if (isset ($objManyToManyReference->Options['FormGen']) && ($objManyToManyReference->Options['FormGen'] == 'none' || $objManyToManyReference->Options['FormGen'] == 'meta')) continue; ?>

    /** @var <?= $displayType ?? $objCodeGen->ModelConnectorControlClass($objManyToManyReference) ?>  */
    protected <?= $displayType ?? $objCodeGen->ModelConnectorControlClass($objManyToManyReference) ?> $<?= $objCodeGen->ModelConnectorVariableName($objManyToManyReference);  ?>;
<?php } ?>