<?php
namespace QCubed\Project\Control;

use QCubed\Control\PaginatorBase;
use QCubed\Exception\Caller;

/**
 * Class Paginator
 *
 * Class Paginator - The paginator control which can be attached to a DataRepeater or DataGrid
 * This class will take care of the number of pages, current page, next/previous links and so on
 * automatically.
 *
 * @package QCubed\Project\Control
 */
class Paginator extends PaginatorBase
{
    // APPEARANCE
    protected int $intIndexCount = 10;

    //////////
    // Methods
    //////////
    /**
     * Constructor
     * @param ControlBase|FormBase $objParentObject
     * @param string|null $strControlId
     * @throws Caller
     */
    public function __construct(ControlBase|FormBase $objParentObject, ?string $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->CssClass = 'paginator';
        //$this->strLabelForPrevious = t('<<');
        //$this->strLabelForNext = t('>>');
    }
}
