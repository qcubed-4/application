<?php

    use QCubed\ModelConnector\Options;

    if ($strClassName != 'QCubed\\Control\\Label' && (!isset($objColumn->Options['FormGen']) || $objColumn->Options['FormGen'] != Options::FORMGEN_LABEL_ONLY)) { ?>
            case '<?= $strPropertyName ?>Control':
                return $this-><?= $strControlVarName ?>_Create();
<?php }
    if ($strClassName == 'QCubed\\Control\\Label' || !isset($objColumn->Options['FormGen']) || $objColumn->Options['FormGen'] != Options::FORMGEN_CONTROL_ONLY) { ?>
            case '<?= $strPropertyName ?>Label':
                return $this-><?= $strLabelVarName ?>_Create();
<?php }