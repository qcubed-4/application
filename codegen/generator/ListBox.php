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
 * Class ListBox
 * @package QCubed\Codegen\Generator
 */
class ListBox extends ListControl
{
    public function __construct(string $strControlClassName = 'QCubed\\Project\\Control\\ListBox')
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
    public function connectorCreateOptions(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ?ColumnInterface $objColumn, string $strControlVarName): string
    {
        $strRet = parent::connectorCreateOptions($objCodeGen, $objTable, $objColumn, $strControlVarName);

        if ($objColumn instanceof ManyToManyReference) {
            $strRet .= <<<TMPL
            \$this->{$strControlVarName}->SelectionMode = ListBox::MULTIPLE;

TMPL;
        }
        return $strRet;
    }
}
