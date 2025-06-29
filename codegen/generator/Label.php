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
use Exception;
use QCubed\Exception\Caller as Caller;

/**
 * Class Label
 *
 * @package QCubed\Codegen\Generator
 */
class Label extends Control
{
    private static ?Label $instance = null;

    /**
     * Constructor method for initializing the class.
     *
     * @param string $strControlClassName The name of the control class to initialize. Default is 'QCubed\\Control\\Label'.
     * @return void
     */
    public function __construct(string $strControlClassName = 'QCubed\\Control\\Label')
    {
        parent::__construct($strControlClassName);
    }

    /**
     * Retrieves the singleton instance of the Label class. If the instance does not exist, it initializes one.
     *
     * @return \QCubed\Codegen\Generator\Label|null The singleton instance of the Label class.
     */
    public static function instance(): ?Label
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
    public function varName(string $strPropName): string
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
     * @return string
     *@throws Exception
     */
    public function connectorCreate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn): string
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

        $displayType = $strControlType;
        if ($displayType === 'QCubed\Control\Label') {
            $displayType = 'Label';
        }
        // add more else-ifs if necessary!

        $strRet = <<<TMPL
    /**
     * Create and setup $displayType $strControlVarName
     *
     * @param string|null \$strControlId optional ControlId to use {$strDateTimeParamExtra}
     * @return $displayType|null
     * @throws Caller
     */
    public function {$strControlVarName}_Create(?string \$strControlId = null{$strDateTimeExtra}): ?$displayType
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
        \$this->{$strControlVarName} = new {$displayType }(\$this->objParentObject, \$strControlId);
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
     * @return string
     *@throws Exception
     */
    public function connectorVariableDeclaration(DatabaseCodeGen $objCodeGen, ColumnInterface $objColumn): string
    {
        $strPropName = $objCodeGen->modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);

        $strRet = <<<TMPL
    /**
     * @var Label|null
     * @access protected
     */
    protected ?Label \${$strControlVarName} = null;


TMPL;

        if ($objColumn->VariableType == 'QDateTime') {
            $strRet .= <<<TMPL
    /**
    * @var string
    * @access protected
    */
    protected string \$str{$strPropName}DateTimeFormat;

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
     * @return string
     * @throws Exception
     */
    public function connectorRefresh(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn, ?bool $blnInit = false): string
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);

        // Preamble with an if test is not initializing
        $strRet = '';
        if ($objColumn instanceof SqlColumn) {
            if ($objColumn->Identity ||
                $objColumn->Timestamp
            ) {
                $strRet = "\$this->{$strControlVarName}->Text = \$this->blnEditMode ? \$this->{$strObjectName}->{$strPropName} : t('N\\A');" . "\n";
            } else {
                if ($objColumn->Reference) {
                    if ($objColumn->Reference->IsType) {
                        $strRet = "\$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$objColumn->PropertyName} ? {$objColumn->Reference->VariableType}::toString(\$this->{$strObjectName}->{$objColumn->PropertyName}) : null;";
                    } else {
                        $strRet = "\$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$strPropName} ? \$this->{$strObjectName}->{$strPropName}->__toString() : null;";
                    }
                } else {
                    $strRet = match ($objColumn->VariableType) {
                        "boolean" => "\$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$strPropName} ? t('Yes') : t('No');",
                        "QDateTime" => "\$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$strPropName} ? \$this->{$strObjectName}->{$strPropName}->qFormat(\$this->str{$strPropName}DateTimeFormat) : null;",
                        default => "\$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$strPropName};",
                    };
                }
            }
        } elseif ($objColumn instanceof ReverseReference) {
            if ($objColumn->Unique) {
                $strRet = "\$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$objColumn->ObjectPropertyName} ? \$this->{$strObjectName}->{$objColumn->ObjectPropertyName}->__toString() : null;";
            }
        } elseif ($objColumn instanceof ManyToManyReference) {
            $strRet = "\$this->{$strControlVarName}->Text = implode(\$this->str{$objColumn->ObjectDescription}Glue, \$this->{$strObjectName}->Get{$objColumn->ObjectDescription}Array());";
        } else {
            throw new Exception('Unknown column type.');
        }

        if (!$blnInit) {
            $strRet = "        if (\$this->{$strControlVarName}) " . $strRet;
        } else {
            $strRet = "        " . $strRet . "\n";
        }

        return $strRet; // . "\n";
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param \QCubed\Codegen\ColumnInterface $objColumn
     * @return string
     */
    public function connectorUpdate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn): string
    {
        return '';
    }
}