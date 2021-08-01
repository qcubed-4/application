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

/**
 * Class ListBox
 * @package QCubed\Codegen\Generator
 * @was QListBoxBase_CodeGenerator
 */
class ListBox extends ListControl
{
    public function __construct($strControlClassName = 'QCubed\\Project\\Control\\ListBox')
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
    public function connectorCreateOptions(DatabaseCodeGen $objCodeGen, SqlTable $objTable, $objColumn, $strControlVarName)
    {
        $strRet = parent::connectorCreateOptions($objCodeGen, $objTable, $objColumn, $strControlVarName);

        if ($objColumn instanceof QManyToManyReference) {
            $strRet .= <<<TMPL
			\$this->{$strControlVarName}->SelectionMode = ListBox::MULTIPLE;

TMPL;
        }
        return $strRet;
    }
}
