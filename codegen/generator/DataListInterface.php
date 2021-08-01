<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Codegen\Generator;

use QCubed\Codegen\SqlTable;
use QCubed\Codegen\DatabaseCodeGen;

/**
 * Interface DataListInterface
 *
 * This interface describes the minimum functions to implement in order to create a code generator for a data list.
 * See the HtmlTable generator for an example
 * @package QCubed\Codegen\Generator
 * @was QDataList_CodeGen_Interface
 */
interface DataListInterface
{

    // To create the gen subclass of the object
    public function dataListConnectorComments(DatabaseCodeGen $objCodeGen, SqlTable $objTable);
    public function dataListConnector(DatabaseCodeGen $objCodeGen, SqlTable $objTable);

    // to create the panel
    public function dataListInstantiate(DatabaseCodeGen $objCodeGen, SqlTable $objTable);            // Create a new list in the parent constructor
    public function dataListHelperMethods(DatabaseCodeGen $objCodeGen, SqlTable $objTable);  // Additional functions called by the list creator
    public function dataListRefresh(DatabaseCodeGen $objCodeGen, SqlTable $objTable);        // How to refresh the data in the control. Only needed if using the parent filter. Can just call MarkAsModified.
    public function dataListHasFilter();    // Returns boolean if the control has its own filter, and thus the parent should not create a filter

    // for the sub-panel
    public function dataListSubclassOverrides(DatabaseCodeGen $objCodeGen, SqlTable $objTable);
}
