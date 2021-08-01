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
use QCubed\Codegen\SqlColumn;
use QCubed\Codegen\SqlTable;

/**
 * Class GeneratorBase
 * @package QCubed\Codegen\Generator
 * @was AbstractControl_CodeGenerator
 */
abstract class GeneratorBase
{
    protected $strControlClassName;

    public function __construct($strControlClassName)
    {
        $this->strControlClassName = $strControlClassName;
    }

    public function getControlClass()
    {
        return $this->strControlClassName;
    }

    /**
     * @param string $strPropName
     * @return string
     */
    abstract public function varName($strPropName);

    /**
     * Generate code that will be inserted into the ModelConnector to connect a database object with this control.
     * This is called during the codegen process. This is very similar to the QListControl code, but there are
     * some differences. In particular, this control does not support ManyToMany references.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @return mixed
     */
    abstract public function connectorCreate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn);

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlColumn $objColumn
     * @return string
     */
    abstract public function connectorVariableDeclaration(DatabaseCodeGen $objCodeGen, ColumnInterface $objColumn);

    /**
     * Reads the options from the special data file, and possibly the column
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface|null $objColumn null means get table options
     * @param string $strControlVarName
     * @return string
     */
    abstract public function connectorCreateOptions(
        DatabaseCodeGen $objCodeGen,
        SqlTable $objTable,
        $objColumn,
        $strControlVarName
    );

    /**
     * Returns code to refresh the control from the saved object.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @param bool $blnInit
     * @return string
     */
    abstract public function connectorRefresh(
        DatabaseCodeGen $objCodeGen,
        SqlTable $objTable,
        ColumnInterface $objColumn,
        $blnInit = false
    );

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @return string
     */
    abstract public function connectorUpdate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn);

    /**
     * Generate helper functions for the update process.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     *
     * @return string
     */
    abstract public function connectorUpdateMethod(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn);
}
