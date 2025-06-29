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
use QCubed\Codegen\DatabaseCodeGen;
use QCubed\Codegen\SqlTable;
use QCubed\Database\FieldType;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;

/**
 * Class DateTimePicker
 *
 * @package QCubed\Codegen\Generator
 */
class DateTimePicker extends Control
{
    public function __construct(string $strControlClassName = 'QCubed\\Control\\DateTimePicker')
    {
        parent::__construct($strControlClassName);
    }

    /**
     * @param string $strPropName
     * @return string
     */
    public function varName(string $strPropName): string
    {
        return 'cal' . $strPropName;
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
     * @throws Caller
     * @throws InvalidCast
     */
    public function connectorCreate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn): string
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strControlVarName = $objCodeGen->modelConnectorVariableName($objColumn);
        $strLabelName = addslashes(DatabaseCodeGen::modelConnectorControlName($objColumn));

        // Read the control type in case we are generating code for a subclass
        $strControlType = $objCodeGen->getControlCodeGenerator($objColumn)->getControlClass();

        $displayType = $strControlType;
        if ($strControlType === 'QCubed\Control\DateTimePicker') {
           $displayType = 'DateTimePicker';
        }

        $strRet = <<<TMPL
    /**
     * Create and set up a $displayType $strControlVarName
     * @param string|null \$strControlId optional ControlId to use
     * @return $displayType
     * @throws Caller
     */
    public function {$strControlVarName}_Create(?string \$strControlId = null): $displayType 
    {

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
        \$this->{$strControlVarName} = new {$displayType}(\$this->objParentObject, \$strControlId);
        \$this->{$strControlVarName}->Name = t('$strLabelName');
        \$this->{$strControlVarName}->DateTime = \$this->{$strObjectName}->{$objColumn->PropertyName};

TMPL;
        $strRet .= match ($objColumn->DbType) {
            FieldType::DATE_TIME => "       \$this->{$strControlVarName}->DateTimePickerType = DateTimePicker::SHOW_DATE_TIME;\n",
            FieldType::TIME => "        \$this->{$strControlVarName}->DateTimePickerType = DateTimePicker::SHOW_TIME;\n",
            default => "        \$this->{$strControlVarName}->DateTimePickerType = DateTimePicker::SHOW_DATE;\n",
        };

        if ($strMethod = DatabaseCodeGen::$PreferredRenderMethod) {
            $strRet .= <<<TMPL
        \$this->{$strControlVarName}->PreferredRenderMethod = '$strMethod';

TMPL;
        }

        $strRet .= $this->connectorCreateOptions($objCodeGen, $objTable, $objColumn, $strControlVarName);

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
            $strRet = "        $this->{$strControlVarName}->DateTime = \$this->{$strObjectName}->{$strPropName};";
        } else {
            $strRet = "        \$this->{$strControlVarName}->DateTime = \$this->{$strObjectName}->{$strPropName};";
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
            \$this->{$strObjectName}->{$objColumn->PropertyName} = \$this->{$strControlVarName}->DateTime;

TMPL;
    }
}