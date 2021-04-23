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
use QCubed\Codegen\ManyToManyReference;
use QCubed\Codegen\ReverseReference;
use QCubed\Codegen\SqlColumn;
use QCubed\Codegen\SqlTable;

/**
 * Class Label
 *
 * @package QCubed\Codegen\Generator
 * @was QLabel_CodeGenerator
 */
class Label extends Control
{
    private static $instance = null;

    public function __construct($strControlClassName = 'QCubed\\Control\\Label')
    {
        parent::__construct($strControlClassName);
    }

    /**
     * @return Label
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new Label();
        }
        return self::$instance;
    }

    /**
     * @param string $strPropName
     * @return string
     */
    public function varName($strPropName)
    {
        return 'lbl' . $strPropName;
    }

    /**
     * Generate code that will be inserted into the ModelConnector to connect a database object with this control.
     * This is called during the codegen process. This is very similar to the QListControl code, but there are
     * some differences. In particular, this control does not support ManyToMany references.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @throws \Exception
     * @return string
     */
    public function connectorCreate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        $strLabelName = addslashes(DatabaseCodeGen::modelConnectorControlName($objColumn));
        $strControlType = $this->strControlClassName;

        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);

        $strDateTimeExtra = '';
        $strDateTimeParamExtra = '';
        if ($objColumn->VariableType == 'QDateTime') {
            $strDateTimeExtra = ', $strDateTimeFormat = null';
            $strDateTimeParamExtra = "\n\t\t * @param string \$strDateTimeFormat";
        }

        $strRet = <<<TMPL
    /**
     * Create and setup $strControlType $strControlVarName
     *
     * @param string \$strControlId optional ControlId to use{$strDateTimeParamExtra}
     * @return $strControlType
     */
    public function {$strControlVarName}_Create(\$strControlId = null{$strDateTimeExtra}) 
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
        \$this->{$strControlVarName} = new \\{$strControlType}(\$this->objParentObject, \$strControlId);
        \$this->{$strControlVarName}->Name = t('{$strLabelName}');

TMPL;
        if ($objColumn->VariableType == 'QDateTime') {
            $strRet .= <<<TMPL
        \$this->str{$strPropName}DateTimeFormat = \$strDateTimeFormat;

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
     * @param DatabaseCodeGen $objCodeGen
     * @param ColumnInterface $objColumn
     * @throws \Exception
     * @return string
     */
    public function connectorVariableDeclaration(DatabaseCodeGen $objCodeGen, ColumnInterface $objColumn)
    {
        $strPropName = $objCodeGen->modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);

        $strRet = <<<TMPL
    /**
     * @var Label
     * @access protected
     */
    protected \${$strControlVarName};


TMPL;

        if ($objColumn->VariableType == 'QDateTime') {
            $strRet .= <<<TMPL
    /**
    * @var string
    * @access protected
    */
    protected \$str{$strPropName}DateTimeFormat;

TMPL;
        }
        return $strRet;
    }

    /**
     * Returns code to refresh the control from the saved object.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @param bool $blnInit
     * @throws \Exception
     * @return string
     */
    public function connectorRefresh(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn, $blnInit = false)
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);

        // Preamble with an if test if not initializing
        $strRet = '';
        if ($objColumn instanceof SqlColumn) {
            if ($objColumn->Identity ||
                $objColumn->Timestamp
            ) {
                $strRet = "\$this->{$strControlVarName}->Text =  \$this->blnEditMode ? \$this->{$strObjectName}->{$strPropName} : t('N\\A');";
            } else {
                if ($objColumn->Reference) {
                    if ($objColumn->Reference->IsType) {
                        $strRet = "\$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$objColumn->PropertyName} ? {$objColumn->Reference->VariableType}::toString(\$this->{$strObjectName}->{$objColumn->PropertyName}) : null;";
                    } else {
                        $strRet = "\$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$strPropName} ? \$this->{$strObjectName}->{$strPropName}->__toString() : null;";
                    }
                } else {
                    switch ($objColumn->VariableType) {
                        case "boolean":
                            $strRet = "\$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$strPropName} ? t('Yes') : t('No');";
                            break;

                        case "QDateTime":
                            $strRet = "\$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$strPropName} ? \$this->{$strObjectName}->{$strPropName}->qFormat(\$this->str{$strPropName}DateTimeFormat) : null;";
                            break;

                        default:
                            $strRet = "\$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$strPropName};";
                    }
                }
            }
        } elseif ($objColumn instanceof ReverseReference) {
            if ($objColumn->Unique) {
                $strRet = "\$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$objColumn->ObjectPropertyName} ? \$this->{$strObjectName}->{$objColumn->ObjectPropertyName}->__toString() : null;";
            }
        } elseif ($objColumn instanceof ManyToManyReference) {
            $strRet = "\$this->{$strControlVarName}->Text = implode(\$this->str{$objColumn->ObjectDescription}Glue, \$this->{$strObjectName}->Get{$objColumn->ObjectDescription}Array());";
        } else {
            throw new \Exception('Unknown column type.');
        }

        if (!$blnInit) {
            $strRet = "\t\t\tif (\$this->{$strControlVarName}) " . $strRet;
        } else {
            $strRet = "\t\t\t" . $strRet;
        }

        return $strRet . "\n";
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param SqlColumn|ReverseReference $objColumn
     * @return string
     */
    public function connectorUpdate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        return '';
    }
}
