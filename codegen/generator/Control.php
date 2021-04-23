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
use QCubed\Codegen\ManyToManyReference;
use QCubed\Codegen\ReverseReference;
use QCubed\Codegen\SqlTable;
use QCubed\Codegen\SqlColumn;
use QCubed\Codegen\DatabaseCodeGen;
use QCubed\Exception\Caller;

/**
 * Class Control
 * @package QCubed\Codegen\Generator
 * @was QControlBase_CodeGenerator
 */
abstract class Control extends GeneratorBase
{

    public function connectorImports(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        $a[] = ['class' => $this->strControlClassName];
        return $a;
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param ColumnInterface $objColumn
     * @return string
     */
    public function connectorVariableDeclaration(DatabaseCodeGen $objCodeGen, ColumnInterface $objColumn)
    {
        $strClassName = $this->getControlClass();
        $strControlVarName = $objCodeGen->modelConnectorVariableName($objColumn);

        $strRet = <<<TMPL
    /**
     * @var {$strClassName}

     * @access protected
     */
    protected \${$strControlVarName};


TMPL;
        return $strRet;
    }

    /**
     * Reads the options from the special data file, and possibly the column
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface|null $objColumn A null column means we want the table options
     * @param string $strControlVarName
     * @return string
     */
    public function connectorCreateOptions(DatabaseCodeGen $objCodeGen, SqlTable $objTable, $objColumn, $strControlVarName)
    {
        $strRet = '';

        if (!$objColumn) {
            $strRet .= <<<TMPL
        \$this->{$strControlVarName}->LinkedNode = QQN::{$objTable->ClassName}();

TMPL;
            $options = $objTable->Options;
        } else {
            $strClass = $objTable->ClassName;

            if ($objColumn instanceof SqlColumn) {
                if ($objColumn->Reference) {
                    $strPropName = $objColumn->Reference->PropertyName; // works for type tables too
                }
                else {
                    $strPropName = $objColumn->PropertyName;
                }
            }
            elseif ($objColumn instanceof ManyToManyReference ||
                $objColumn instanceof ReverseReference
            ) {
                $strPropName = $objColumn->ObjectDescription;
            }

            $strRet .= <<<TMPL
        \$this->{$strControlVarName}->LinkedNode = QQN::{$strClass}()->{$strPropName};

TMPL;
            $options = $objColumn->Options;
        }
        if (isset($options['Overrides'])) {
            foreach ($options['Overrides'] as $name => $val) {
                if (is_numeric($val)) {
                    // looks like a number
                    $strVal = $val;
                } elseif (is_string($val)) {
                    if (strpos($val, '::') !== false &&
                        strpos($val, ' ') === false
                    ) {
                        // looks like a constant
                        $strVal = $val;
                    } else {
                        $strVal = var_export($val, true);
                    }
                } elseif (is_array($val) && isset($val["translate"])) {
                    $strVal = var_export($val["value"], true);
                    $strVal = 't(' . $strVal . ')';
                }
                else {
                    $strVal = var_export($val, true);
                }
                $strRet .= <<<TMPL
        \$this->{$strControlVarName}->{$name} = {$strVal};

TMPL;
            }
        }
        return $strRet;
    }

    /**
     * @param string $strPropName
     * @throws Caller
     * @return string
     */
    public function varName($strPropName)
    {
        throw new Caller('VarName() method not implemented');
    }

    /**
     * Generate code that will be inserted into the ModelConnector to connect a database object with this control.
     * This is called during the codegen process.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @throws Caller
     * @return string
     */
    public function connectorCreate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        throw new Caller('ConnectorCreate() method not implemented');
    }

    /**
     * Returns code to refresh the control from the saved object.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @param bool $blnInit
     * @throws Caller
     * @return string
     */
    public function connectorRefresh(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn, $blnInit = false)
    {
        throw new Caller('ConnectorRefresh() method not implemented');
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @throws Caller
     * @return string
     */
    public function connectorUpdate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        throw new Caller('ConnectorUpdate() method not implemented');
    }

    /**
     * Generate helper functions for the update process.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     *
     * @throws Caller
     * @return string
     */
    public function connectorUpdateMethod(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        throw new Caller('ConnectorUpdateMethod() method not implemented');
    }

    /**
     * Generate extra set options for the connector.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @return string
     */
    public function connectorSet(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        return "";
    }

    /**
     * Generate extra set options for the connector.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @return string
     */
    public function connectorGet(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        return "";
    }

    /**
     * Generate extra property comments for the connector.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     *
     * @throws Caller
     * @return string
     */
    public function connectorPropertyComments(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        return "";
    }
}
