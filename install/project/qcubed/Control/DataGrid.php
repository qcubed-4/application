<?php
namespace QCubed\Project\Control;

use QCubed\Codegen\Generator\Table;
use QCubed\Control\DataGridBase;
use QCubed\Exception\Caller;
use QCubed as Q;

/**
 * DataGrid can help generate tables automatically with pagination. It can also be used to
 * render data directly from a database by using a 'DataSource'. The code-generated search pages you get for
 * every table in your database are all QDataGrids
 *
 * @package QCubed\Project\Control
 */
class DataGrid extends DataGridBase
{
    // Feel free to specify global display preferences/defaults for all DataGrid controls

    /**
     * Constructor method for the class.
     *
     * @param mixed $objParentObject The parent object with which this control is associated.
     * @param string|null $strControlId Optional ID for the control. Defaults to null.
     *
     * @return void
     * @throws Caller
     */
    public function __construct(mixed $objParentObject, ?string $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller  $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }

        $this->CssClass = 'datagrid';
    }

    /**
     * Returns the generator corresponding to this control.
     *
     * @return Table
     */
    public static function getCodeGenerator(): Q\Codegen\Generator\Table
    {
        return new Q\Codegen\Generator\Table(__CLASS__); // reuse the Table generator
    }

}
