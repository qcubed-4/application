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
use QCubed\Type;
use QCubed\QString;

/**
 * Class TextBox
 *
 * @package QCubed\Codegen\Generator
 */
class TextBox extends Control
{
    public function __construct(string $strControlClassName = 'QCubed\\Project\\Control\\TextBox')
    {
        parent::__construct($strControlClassName);
    }

    /**
     * @param string $strPropName
     * @return string
     */
    public function varName(string $strPropName): string
    {
        return 'txt' . $strPropName;
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
        //$strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strClassName = $objTable->ClassName;
        $strControlVarName = $objCodeGen->modelConnectorVariableName($objColumn);
        $strLabelName = addslashes(DatabaseCodeGen::modelConnectorControlName($objColumn));

        // Read the control type in case we are generating code for a subclass of QTextBox
        $strControlType = $objCodeGen->getControlCodeGenerator($objColumn)->getControlClass();

        $displayType = $strControlType;
        if ($displayType === 'QCubed\Project\Control\TextBox') {
            $displayType = 'TextBox';
        }
        if ($displayType === 'QCubed\Control\IntegerTextBox') {
            $displayType = 'IntegerTextBox';
        }

        if ($displayType === 'QCubed\Control\FloatTextBox') {
            $displayType = 'FloatTextBox';
        }
        // add more else-ifs if necessary!

        $strRet = <<<TMPL
    /**
     * Create and set up a $displayType
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

TMPL;

        if ($objColumn->NotNull) {
            $strRet .= <<<TMPL
        \$this->{$strControlVarName}->Required = true;

TMPL;
        }

        if ($objColumn->DbType == FieldType::BLOB) {
            $strRet .= <<<TMPL
        \$this->{$strControlVarName}->TextMode = TextBox::MULTI_LINE;

TMPL;
        }

        if (($objColumn->VariableType == Type::STRING) && (is_numeric($objColumn->Length))) {

            $strConstName = strtoupper(QString::underscoreFromCamelCase($objColumn->PropertyName));
            $strRet .= <<<TMPL
        \$this->{$strControlVarName}->MaxLength = {$strClassName}::{$strConstName}_MAX_LENGTH;

TMPL;
        }

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
    public function connectorRefresh(
        DatabaseCodeGen $objCodeGen,
        SqlTable $objTable,
        ColumnInterface $objColumn,
        ?bool $blnInit = false
    ): string
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strPropName = $objColumn->Reference ? $objColumn->Reference->PropertyName : $objColumn->PropertyName;
        $strControlVarName = $this->varName($strPropName);

        if ($blnInit) {
            $strRet = "        \$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$strPropName};";
        } else {
            $strRet = "        \$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$strPropName};";
        }
        return $strRet . "\n";
    }

    /**
     * Generates code to update the object property from the control's value.
     *
     * @param DatabaseCodeGen $objCodeGen The code generator instance.
     * @param SqlTable $objTable The SQL table associated with the object.
     * @param ColumnInterface $objColumn The column interface representing the property to update.
     * @return string The generated code for updating the object property.
     */
    public function connectorUpdate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn): string
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strPropName = $objColumn->Reference ? $objColumn->Reference->PropertyName : $objColumn->PropertyName;
        $strControlVarName = $this->varName($strPropName);
        return <<<TMPL
            \$this->{$strObjectName}->{$strPropName} = \$this->{$strControlVarName}->Text;
TMPL;
    }
}