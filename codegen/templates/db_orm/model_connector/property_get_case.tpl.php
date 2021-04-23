<?php
	if ($strClassName != 'QCubed\\Control\\Label' && (!isset($objColumn->Options['FormGen']) || $objColumn->Options['FormGen'] != \QCubed\ModelConnector\Options::FORMGEN_LABEL_ONLY)) { ?>
            case '<?= $strPropertyName ?>Control':
                if (!$this-><?= $strControlVarName ?>) return $this-><?= $strControlVarName ?>_Create();
                return $this-><?= $strControlVarName ?>;
<?php }
	if ($strClassName == 'QCubed\\Control\\Label' || !isset($objColumn->Options['FormGen']) || $objColumn->Options['FormGen'] != \QCubed\ModelConnector\Options::FORMGEN_CONTROL_ONLY) { ?>
            case '<?= $strPropertyName ?>Label':
                if (!$this-><?= $strLabelVarName ?>) return $this-><?= $strLabelVarName ?>_Create();
                return $this-><?= $strLabelVarName ?>;
<?php }