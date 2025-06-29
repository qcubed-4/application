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
use QCubed\Codegen\SqlTable;
use QCubed\Codegen\SqlColumn;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;

/**
 * Class CheckboxList
 *
 * @package QCubed\Codegen\Generator
 */
class CheckboxList extends ListControl
{
    protected string $strControlClassName;
    protected SqlColumn $objColumn;

    public function __construct(string $strControlClassName = 'QCubed\\Control\\CheckboxList')
    {
        parent::__construct($strControlClassName);
    }

    /**
     * Reads the options from the special data file, and possibly the column
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface|null $objColumn
     * @param string $strControlVarName
     * @return string
     */
    public function connectorCreateOptions(
        DatabaseCodeGen  $objCodeGen,
        SqlTable         $objTable,
        ?ColumnInterface $objColumn,
        string $strControlVarName
    ): string
    {
        $strRet = parent::connectorCreateOptions($objCodeGen, $objTable, $objColumn, $strControlVarName);

        if (!$objColumn instanceof ManyToManyReference) {
            $objCodeGen->reportError($objTable->Name . ':' . $objColumn->Name . ' is not compatible with a CheckBoxList.');
        }

        return $strRet;
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @return string
     * @throws Caller
     * @throws InvalidCast
     */
    public function connectorUpdate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn): string
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strPropName = $objColumn->ObjectDescription;
        $strPropNames = $objColumn->ObjectDescriptionPlural;
        $strControlVarName = $objCodeGen->modelConnectorVariableName($objColumn);

        return <<<TMPL
        protected function {$strControlVarName}_Update() {
            if (\$this->{$strControlVarName}) {
                \$this->{$strObjectName}->unassociateAll{$strPropNames}();
                \$this->{$strObjectName}->associate{$strPropName}(\$this->{$strControlVarName}->SelectedValues);
            }
        }


TMPL;
    }
}
