<?php
    if ($strClassName != 'QCubed\\Control\\Label' && (!isset($objColumn->Options['FormGen']) || $objColumn->Options['FormGen'] != \QCubed\ModelConnector\Options::FORMGEN_LABEL_ONLY)) { ?>
            case '<?= $strPropertyName ?>Control':
                return $this-><?= $strControlVarName ?>_Create();
<?php }
    if ($strClassName == 'QCubed\\Control\\Label' || !isset($objColumn->Options['FormGen']) || $objColumn->Options['FormGen'] != \QCubed\ModelConnector\Options::FORMGEN_CONTROL_ONLY) { ?>
            case '<?= $strPropertyName ?>Label':
                return $this-><?= $strLabelVarName ?>_Create();
<?php }