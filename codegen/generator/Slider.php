<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Codegen\Generator;

use QCubed\Codegen\ColumnInterface;
use QCubed\Codegen\SqlTable;
use QCubed\Codegen\DatabaseCodeGen;

/**
 * Class Slider
 * @package QCubed\Codegen\Generator
 */
class Slider extends Control
{
    public function __construct(string $strControlClassName = 'QCubed\\Project\\Jqui\\Slider')
    {
        parent::__construct($strControlClassName);
    }

    /**
     * @param string $strPropName
     * @return string
     */
    public function varName(string $strPropName): string
    {
        return 'sld' . $strPropName;
    }

    /**
     * Generate code that will be inserted into the ModelConnector to connect a database object with this control.
     * This is called during the codegen process. This is very similar to the QListControl code, but there are
     * some differences. In particular, this control does not support ManyToMany references.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @return string
     * @throws \QCubed\Exception\Caller
     * @throws \QCubed\Exception\InvalidCast
     */
    public function connectorCreate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn): string
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strClassName = $objTable->ClassName;
        $strControlVarName = $objCodeGen->modelConnectorVariableName($objColumn);
        $strLabelName = addslashes(DatabaseCodeGen::modelConnectorControlName($objColumn));

        // Read the control type in case we are generating code for a subclass of QTextBox
        $strControlType = $objCodeGen->getControlCodeGenerator($objColumn)->getControlClass();

        $strRet = <<<TMPL
    /**
     * Create and setup a $strControlType $strControlVarName
     * @param string \$strControlId optional ControlId to use
     * @return $strControlType
     */
    public function {$strControlVarName}_Create(\$strControlId = null) {

TMPL;
        $strControlIdOverride = $objCodeGen->generateControlId($objTable, $objColumn);

        if ($strControlIdOverride) {
            $strRet .= <<<TMPL
        if (!\$strControlId) {
            \$strControlId = '$strControlIdOverride';
        }

TMPL;
        }
        $strRet .= <<<TMPL
        \$this->{$strControlVarName} = new \\{$strControlType}(\$this->objParentObject, \$strControlId);
        \$this->{$strControlVarName}->Name = t('$strLabelName');

TMPL;

        if ($strMethod = DatabaseCodeGen::$PreferredRenderMethod) {
            $strRet .= <<<TMPL
        \$this->{$strControlVarName}->PreferredRenderMethod = '$strMethod';

TMPL;
        }
        $strRet .= $this->connectorCreateOptions($objCodeGen, $objTable, $objColumn, $strControlVarName);
        $strRet .= $this->connectorRefresh($objCodeGen, $objTable, $objColumn, true);

        $strRet .= <<<TMPL
        return \$this->{$strControlVarName};
    }


TMPL;

        return $strRet;
    }

    /**
     * Returns code to refresh the control from the saved object.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @param bool $blnInit
     * @return string
     */
    public function connectorRefresh(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn, ?bool $blnInit = false): string
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strPropName = $objColumn->Reference ? $objColumn->Reference->PropertyName : $objColumn->PropertyName;
        $strControlVarName = $this->varName($strPropName);

        if ($blnInit) {
            $strRet = "\t\t\t\$this->{$strControlVarName}->Value = \$this->{$strObjectName}->{$strPropName};";
        } else {
            $strRet = "\t\t\tif (\$this->{$strControlVarName}) \$this->{$strControlVarName}->Value = \$this->{$strObjectName}->{$strPropName};";
        }
        return $strRet . "\n";
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @return string
     */
    public function connectorUpdate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn): string
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strPropName = $objColumn->Reference ? $objColumn->Reference->PropertyName : $objColumn->PropertyName;
        $strControlVarName = $this->varName($strPropName);
        return <<<TMPL
				if (\$this->{$strControlVarName}) \$this->{$strObjectName}->{$strPropName} = \$this->{$strControlVarName}->Value;

TMPL;
    }
}