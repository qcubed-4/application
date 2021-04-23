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

/**
 * Class RadioButtonList
 *
 * @package QCubed\Codegen\Generator
 * @was QRadioButtonListBase_CodeGenerator
 */
class RadioButtonList extends ListControl
{
    public function __construct($strControlClassName = 'QCubed\\Control\\RadioButtonList')
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
        DatabaseCodeGen $objCodeGen,
        SqlTable $objTable,
        $objColumn,
        $strControlVarName
    ) {
        $strRet = parent::connectorCreateOptions($objCodeGen, $objTable, $objColumn, $strControlVarName);

        if ($objColumn instanceof ManyToManyReference) {
            $objCodeGen->reportError($objTable->Name . ':' . $objColumn->Name . ' is not compatible with a RadioButtonList.');
        }

        return $strRet;
    }

}
