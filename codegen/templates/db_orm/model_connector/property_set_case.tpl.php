<?php
	if ($strClassName != 'QCubed\\Control\\Label' && (!isset($objColumn->Options['FormGen']) || $objColumn->Options['FormGen'] != \QCubed\ModelConnector\Options::FORMGEN_LABEL_ONLY)) { ?>
                case '<?= $strPropertyName ?>Control':
                    $this-><?= $strControlVarName ?> = Type::cast($mixValue, '\\<?= $strClassName ?>');
                    break;
<?php }
	if ($strClassName == 'QCubed\\Control\\Label' || !isset($objColumn->Options['FormGen']) || $objColumn->Options['FormGen'] != \QCubed\ModelConnector\Options::FORMGEN_CONTROL_ONLY) { ?>
                case '<?= $strPropertyName ?>Label':
                    $this-><?= $strLabelVarName ?> = Type::cast($mixValue, '\\QCubed\\Control\\Label');
                    break;
<?php }