<?= '<?php' ?>

namespace QCubed\Jqui;

use QCubed;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class <?= $objJqDoc->strQcClass ?>Gen
 *
 * This is the <?= $objJqDoc->strQcClass ?>Gen class which is automatically generated
 * by scraping the JQuery UI documentation website. As such, it includes all the options
 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
 * the <?= $objJqDoc->strQcClass ?>Base class for any glue code to make this class more
 * usable in QCubed.
 *
 * @see <?= $objJqDoc->strQcClass ?>Base
 * @package QCubed\Jqui
<?php foreach ($objJqDoc->options as $option) { ?>
 * @property <?= $option->phpType ?> $<?= $option->propName ?>

<?= jq_indent($option->description, 0, true); ?>

 *
<?php } ?>
 * @was <?= $objJqDoc->strOldClass ?>Gen

 */

<?= $objJqDoc->strAbstract ?>class <?= $objJqDoc->strQcClass ?>Gen extends <?= $objJqDoc->strQcBaseClass ?>

{
    protected $strJavaScripts = QCUBED_JQUI_JS;
    protected $strStyleSheets = QCUBED_JQUI_CSS;
<?php foreach ($objJqDoc->options as $option) { ?>
    /** @var <?= $option->phpType ?> */
<?php 	if (!$option->defaultValue) { ?>
    protected $<?= $option->varName ?>;
<?php 	} ?>
<?php 	if ($option->defaultValue) { ?>
    protected $<?= $option->varName ?> = null;
<?php 	} ?>
<?php } ?>

    /**
     * Builds the option array to be sent to the widget constructor.
     *
     * @return array key=>value array of options
     */
    protected function makeJqOptions() {
<?php if (method_exists($objJqDoc->strQcBaseClass, 'MakeJqOptions')) { ?>
        $jqOptions = parent::MakeJqOptions();
<?php } ?>
<?php if (!method_exists($objJqDoc->strQcBaseClass, 'MakeJqOptions')) { ?>
        $jqOptions = null;
<?php } ?>
<?php foreach ($objJqDoc->options as $option) { ?>
        if (!is_null($val = $this-><?= $option->propName ?>)) {$jqOptions['<?= $option->name ?>'] = $val;}
<?php } ?>
        return $jqOptions;
    }

    /**
     * Return the JavaScript function to call to associate the widget with the control.
     *
     * @return string
     */
    public function getJqSetupFunction()
    {
        return '<?= $objJqDoc->strJqSetupFunc ?>';
    }


<?php foreach ($objJqDoc->methods as $method) { ?>
    /**
     * <?= str_replace("\n", "\n     * ", wordwrap(trim($method->description))) ?>

<?php foreach ($method->requiredArgs as $reqArg) { ?>
<?php 	if ($reqArg{0} != '"') { ?>
     * @param <?= $reqArg ?>

<?php 	} ?>
<?php } ?>
<?php foreach ($method->optionalArgs as $optArg) { ?>
     * @param <?= $optArg ?>

<?php } ?>
     */
    public function <?= $method->phpSignature ?>

    {
<?php
            $args = array();
            foreach ($method->requiredArgs as $reqArg) {
                $args[] = $reqArg;
            }
            foreach ($method->optionalArgs as $optArg) {
                $args[] = $optArg;
            }
            $strArgs = join(", ", $args);
?>
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), <?= $strArgs; ?>, Application::PRIORITY_LOW);
    }
<?php } ?>


    public function __get($strName)
    {
        switch ($strName) {
<?php foreach ($objJqDoc->options as $option) { ?>
            case '<?= $option->propName ?>': return $this-><?= $option->varName ?>;
<?php } ?>
            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
<?php 	foreach ($objJqDoc->options as $option) { ?>
            case '<?= $option->propName ?>':
<?php 		if (!$option->phpQType) { ?>
                $this-><?= $option->varName ?> = $mixValue;
<?php 			if (!($option instanceof Event)) { ?>
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', '<?= $option->name ?>', $mixValue);
                break;
<?php 			} ?>
<?php 		} else { ?>
                try {
                    $this-><?= $option->varName ?> = Type::Cast($mixValue, <?= $option->phpQType ?>);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', '<?= $option->name ?>', $this-><?= $option->varName ?>);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
<?php 		} ?>

<?php 	} ?>

<?php 	if ($objJqDoc->hasDisabledProperty) { ?>
            case 'Enabled':
                $this->Disabled = !$mixValue;	// Tie in standard QCubed functionality
                parent::__set($strName, $mixValue);
                break;

<?php 	} ?>
            default:
                try {
                    parent::__set($strName, $mixValue);
                    break;
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
    * If this control is attachable to a codegenerated control in a ModelConnector, this function will be
    * used by the ModelConnector designer dialog to display a list of options for the control.
    * @return QModelConnectorParam[]
    **/
    public static function getModelConnectorParams()
    {
        return array_merge(parent::GetModelConnectorParams(), array(
<?php foreach ($objJqDoc->options as $option) { ?>
<?php 	if ($option->phpQType) { ?>
            new QModelConnectorParam (get_called_class(), '<?= $option->propName ?>', '<?= addslashes(trim(str_replace(array("\n", "\r"), '', $option->description))) ?>', <?= $option->phpQType ?>),
<?php 	} ?>
<?php } ?>
        ));
    }
}
