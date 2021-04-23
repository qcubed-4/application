<?php
/**
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Exception\Caller;
use QCubed as Q;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Table\ColumnBase;
use QCubed\Table\DataColumn;
use QCubed\Table\DataGridCheckboxColumn;
use QCubed\Type;


if (!defined('QCUBED_FONT_AWESOME_CSS')) {
    define('QCUBED_FONT_AWESOME_CSS', 'https://opensource.keycdn.com/fontawesome/4.6.3/font-awesome.min.css');
}

/**
 * Class DataGridBase
 *
 * This class is designed primarily to work alongside the code generator, but it can be independent as well. It creates
 * an html table that displays data from the database. The data can possibly be sorted by clicking on the header cell
 * of the sort column.
 *
 * This grid also has close ties to the QDataGrid_CheckboxColumn to easily enable the addition of a column or columns
 * of checkboxes.
 *
 * This class is NOT intended to support column filters, but a subclass could be created that could do so. Just don't
 * do that here.
 *
 * @property-read  QQClause $OrderByClause The sorting clause based on the selected headers.
 * @property  string $SortColumnId The id of the currently sorted column. Does not change if columns are re-ordered.
 * @property  int $SortColumnIndex The index of the currently sorted column.
 * @property  int $SortDirection SortAscending or SortDescending.
 * @property  array $SortInfo An array containing the sort data, so you can save and restore it later if needed.
 * @was QDataGridBase
 * @package QCubed\Control
 */
class DataGridBase extends TableBase
{
    /** Numbers than can be used to multiply against the results of comparison functions to reverse the order. */
    const SORT_ASCENDING = 1;
    const SORT_DESCENDING = -1;

    /** @var int Couter to generate column ids for columns that do not have them. */
    protected $intLastColumnId = 0;

    /** @var  string Keeps track of current sort column. We do it by id so that the table can add/hide/show or rearrange columns and maintain the sort column. */
    protected $strSortColumnId;

    /** @var int The direction of the currently sorted column. */
    protected $intSortDirection = self::SORT_ASCENDING;

    /** @var string Default class */
    protected $strCssClass = 'datagrid';


    /**
     * DataGridBase constructor.
     * @param ControlBase|FormBase $objParentObject
     * @param null|string $strControlId
     * @throws Caller
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);

            $this->addCssFile(QCUBED_FONT_AWESOME_CSS);

            $this->addActions();
        } catch (Caller  $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
    }

    /**
     * An override to add the paginator to the caption area.
     * @return string
     */
    protected function renderCaption()
    {
        return $this->renderPaginator();
    }

    /**
     * Renders the given paginator in a span in the caption. If a caption already exists, it will add the caption.
     * @return string
     * @throws Caller
     */
    protected function renderPaginator()
    {
        $objPaginator = $this->objPaginator;
        if (!$objPaginator) {
            return '';
        }

        $strHtml = $objPaginator->render(false);
        $strHtml = Q\Html::renderTag('span', ['class' => 'paginator-control'], $strHtml);
        if ($this->strCaption) {
            $strHtml = '<span>' . Q\QString::htmlEntities($this->strCaption) . '</span>' . $strHtml;
        }

        $strHtml = Q\Html::renderTag('caption', null, $strHtml);

        return $strHtml;
    }

    /**
     * Adds the actions for the table. Override to add additional actions. If you are detecting clicks
     * that need to cancel the default action, put those in front of this function.
     */
    public function addActions()
    {
        $this->addAction(new Q\Event\CheckboxColumnClick(), new Q\Action\AjaxControl($this, 'CheckClick'));
        $this->addAction(new Q\Event\CheckboxColumnClick(),
            new Q\Action\StopPropagation()); // prevent check click from bubbling as a row click.

        $this->addAction(new Q\Event\DataGridSort(), new Q\Action\AjaxControl($this, 'SortClick'));
        $this->addAction(new Q\Event\DataGridSort(), new Q\Action\StopPropagation());   // in case datagrid is nested
    }

    /**
     * An override to create an id for every column, since the id is what we use to track sorting.
     *
     * @param int $intColumnIndex
     * @param ColumnBase $objColumn
     * @throws InvalidCast
     */
    public function addColumnAt($intColumnIndex, ColumnBase $objColumn)
    {
        parent::addColumnAt($intColumnIndex, $objColumn);
        // Make sure the column has an Id, since we use that to track sorting.
        if (!$objColumn->Id) {
            $objColumn->Id = $this->ControlId . '_col_' . $this->intLastColumnId++;
        }
    }

    /**
     * Transfers clicks to any checkbox columns.
     *
     * @param $strFormId
     * @param $strControlId
     * @param $strParameter
     */
    protected function checkClick($strFormId, $strControlId, $strParameter)
    {
        $intColumnIndex = $strParameter['col'];
        $objColumn = $this->getColumn($intColumnIndex, true);

        if ($objColumn instanceof DataGridCheckboxColumn) {
            $objColumn->click($strParameter);
        }
    }

    /**
     * Clears all checkboxes in checkbox columns. If you have multiple checkbox columns, you can specify which column
     * to clear. Otherwise, it will clear all of them.
     *
     * @param string|null $strColId
     */
    public function clearCheckedItems($strColId = null)
    {
        foreach ($this->objColumnArray as $objColumn) {
            if ($objColumn instanceof DataGridCheckboxColumn) {
                if (is_null($strColId) || $objColumn->Id === $strColId) {
                    $objColumn->clearCheckedItems();
                }
            }
        }
    }

    /**
     * Returns the checked item ids if the data grid has a QDataGrid_CheckboxColumn column. If there is more than
     * one column, you can specify which column to want to query. If no id is specified, it
     * will return the ids from the first column found. If no column was found, then null is returned.
     *
     * @param mixed $strColId
     * @return array|null
     */
    public function getCheckedItemIds($strColId = null)
    {
        foreach ($this->objColumnArray as $objColumn) {
            if ($objColumn instanceof DataGridCheckboxColumn) {
                if (is_null($strColId) ||
                    $objColumn->Id === $strColId
                ) {
                    return $objColumn->getCheckedItemIds();
                }
            }
        }
        return null; // column not found
    }

    /**
     * Processes clicks on a sortable column head.
     *
     * @param string $strFormId
     * @param string $strControlId
     * @param mixed $mixParameter
     */
    protected function sortClick($strFormId, $strControlId, $mixParameter)
    {
        $intColumnIndex = Type::cast($mixParameter, Type::INTEGER);
        $objColumn = $this->getColumn($intColumnIndex, true);

        if (!$objColumn) {
            return;
        }
        assert($objColumn instanceof DataColumn);

        $this->blnModified = true;

        $strId = $objColumn->Id;

        if (!$objColumn) {
            return;
        }

        // Reset pagination (if applicable)
        if ($this->objPaginator) {
            $this->PageNumber = 1;
        }

        // Make sure the Column is Sortable
        if ($objColumn->OrderByClause) {
            // It is

            // Are we currently sorting by this column?
            if ($this->strSortColumnId === $strId) {
                // Yes we are currently sorting by this column

                // In Reverse?
                if ($this->intSortDirection == self::SORT_DESCENDING) {
                    // Yep -- unreverse the sort
                    $this->intSortDirection = self::SORT_ASCENDING;
                } else {
                    // Nope -- can we reverse?
                    if ($objColumn->ReverseOrderByClause) {
                        $this->intSortDirection = self::SORT_DESCENDING;
                    }
                }
            } else {
                // Nope -- so let's set it to this column
                $this->strSortColumnId = $strId;
                $this->intSortDirection = self::SORT_ASCENDING;
            }
        } else {
            // It isn't -- clear all sort properties
            $this->intSortDirection = self::SORT_ASCENDING;
            $this->strSortColumnId = null;
        }
    }

    /**
     * Override to return the header row to indicate when a column is sortable.
     *
     * @return string
     */
    protected function getHeaderRowHtml()
    {
        $strToReturn = '';
        for ($i = 0; $i < $this->intHeaderRowCount; $i++) {
            $this->intCurrentHeaderRowIndex = $i;

            $strCells = '';
            if ($this->objColumnArray) {
                foreach ($this->objColumnArray as $objColumn) {
                    assert ($objColumn instanceof DataColumn);
                    if ($objColumn->Visible) {
                        $strCellValue = $this->getHeaderCellContent($objColumn);
                        $aParams = $objColumn->getHeaderCellParams();
                        $aParams['id'] = $objColumn->Id;
                        if ($objColumn->OrderByClause) {
                            if (isset($aParams['class'])) {
                                $aParams['class'] .= ' ' . 'sortable';
                            } else {
                                $aParams['class'] = 'sortable';
                            }
                        }
                        $strCells .= Q\Html::renderTag('th', $aParams, $strCellValue);
                    }
                }
            }
            $strToReturn .= Q\Html::renderTag('tr', $this->getHeaderRowParams(), $strCells);
        }

        return $strToReturn;
    }

    /**
     * Override to return sortable column info.
     *
     * @param DataColumn $objColumn
     * @return string
     */
    protected function getHeaderCellContent(DataColumn $objColumn)
    {
        $blnSortable = false;
        $strCellValue = $objColumn->fetchHeaderCellValue();
        if ($objColumn->HtmlEntities) {
            $strCellValue = Q\QString::htmlEntities($strCellValue);
        }
        $strCellValue = Q\Html::renderTag('span', null, $strCellValue);    // wrap in a span for positioning

        if ($this->strSortColumnId === $objColumn->Id) {
            if ($this->intSortDirection == self::SORT_ASCENDING) {
                $strCellValue = $strCellValue . ' ' . Q\Html::renderTag('i', ['class' => 'fa fa-sort-desc fa-lg']);
            } else {
                $strCellValue = $strCellValue . ' ' . Q\Html::renderTag('i', ['class' => 'fa fa-sort-asc fa-lg']);
            }
            $blnSortable = true;
        } else {
            if ($objColumn->OrderByClause) {    // sortable, but not currently being sorted
                $strCellValue = $strCellValue . ' ' . Q\Html::renderTag('i',
                        ['class' => 'fa fa-sort fa-lg']);
                $blnSortable = true;
            }
        }

        if ($blnSortable) {
            // Wrap header cell in an html5 block-link to help with assistive technologies.
            $strCellValue = Q\Html::renderTag('div', null, $strCellValue);
            $strCellValue = Q\Html::renderTag('a', ['href' => 'javascript:;'],
                $strCellValue); // action will be handled by qcubed.js click handler in qcubed.datagrid2()
        }

        return $strCellValue;
    }

    /**
     * Override to enable the datagrid2 javascript.
     */
    protected function makeJqWidget()
    {
        parent::makeJqWidget();
        Application::executeJsFunction('qcubed.datagrid2', $this->ControlId);
    }


    /**
     * Returns the current state of the control to be able to restore it later.
     * @return mixed
     */
    public function getState()
    {
        $state = array();
        if ($this->strSortColumnId !== null) {
            $state["c"] = $this->strSortColumnId;
            $state["d"] = $this->intSortDirection;
        }
        if ($this->Paginator || $this->PaginatorAlternate) {
            $state["p"] = $this->PageNumber;
        }
        return $state;
    }

    /**
     * Restore the state of the control.
     * @param mixed $state Previously saved state as returned by GetState above.
     */
    public function putState($state)
    {
        // use the name as the column key because columns might be added or removed for some reason
        if (isset($state["c"])) {
            $this->strSortColumnId = $state["c"];
        }
        if (isset($state["d"])) {
            $this->intSortDirection = $state["d"];
            if ($this->intSortDirection != self::SORT_DESCENDING) {
                $this->intSortDirection = self::SORT_ASCENDING;    // make sure its only one of two values
            }
        }
        if (isset($state["p"]) &&
            ($this->Paginator || $this->PaginatorAlternate)
        ) {
            $this->PageNumber = $state["p"];
        }
    }

    /**
     * Returns the index of the currently sorted column.
     * Returns false if nothing selected.
     *
     * @return bool|int
     */
    public function getSortColumnIndex()
    {
        if ($this->objColumnArray && ($count = count($this->objColumnArray))) {
            for ($i = 0; $i < $count; $i++) {
                if ($this->objColumnArray[$i]->Id == $this->SortColumnId) {
                    return $i;
                }
            }
        }
        return false;
    }

    /**
     * Return information on sorting the data. For SQL databases, this would be a QQClause. But since this just
     * gets the clause from the currently active column, it could be anything.
     *
     * This clause should not affect counting or limiting.
     *
     * @return mixed
     */
    public function getOrderByInfo()
    {
        if ($this->strSortColumnId !== null) {
            $objColumn = $this->getColumnById($this->strSortColumnId);
            assert($objColumn instanceof DataColumn);
            if ($objColumn && $objColumn->OrderByClause) {
                if ($this->intSortDirection == self::SORT_ASCENDING) {
                    return $objColumn->OrderByClause;
                } else {
                    if ($objColumn->ReverseOrderByClause) {
                        return $objColumn->ReverseOrderByClause;
                    } else {
                        return $objColumn->OrderByClause;
                    }
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * @param string $strName
     * @return bool|int|Keeps|mixed|null
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            // MISC
            case "OrderByClause":
                return $this->getOrderByInfo();

            case "SortColumnId":
                return $this->strSortColumnId;
            case "SortDirection":
                return $this->intSortDirection;

            case "SortColumnIndex":
                return $this->getSortColumnIndex();

            case "SortInfo":
                return ['id' => $this->strSortColumnId, 'dir' => $this->intSortDirection];

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * @param string $strName
     * @param string $mixValue
     * @throws Caller
     * @throws InvalidCast
     * @return void
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "SortColumnId":
                try {
                    $this->strSortColumnId = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "SortColumnIndex":
                try {
                    $intIndex = Type::cast($mixValue, Type::INTEGER);
                    if ($intIndex < 0) {
                        $intIndex = 0;
                    }
                    if ($intIndex < count($this->objColumnArray)) {
                        $objColumn = $this->objColumnArray[$intIndex];
                    } elseif (count($this->objColumnArray) > 0) {
                        $objColumn = end($this->objColumnArray);
                    } else {
                        // no columns
                        $objColumn = null;
                    }
                    if ($objColumn && $objColumn->OrderByClause) {
                        $this->strSortColumnId = $objColumn->Id;
                    }
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case "SortDirection":
                try {
                    $this->intSortDirection = Type::cast($mixValue, Type::INTEGER);
                    if ($this->intSortDirection != self::SORT_DESCENDING) {
                        $this->intSortDirection = self::SORT_ASCENDING;    // make sure its only one of two values
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "SortInfo":    // restore the SortInfo obtained from the getter
                try {
                    if (isset($mixValue['id']) && isset($mixValue['dir'])) {
                        $this->intSortDirection = Type::cast($mixValue['dir'], Type::INTEGER);
                        $this->strSortColumnId = Type::cast($mixValue['id'], Type::STRING);
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            default:
                try {
                    parent::__set($strName, $mixValue);
                    break;
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }
}
