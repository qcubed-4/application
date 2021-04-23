<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Table;

use QCubed as Q;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\ObjectBase;
use QCubed\Project\Application;
use QCubed\TagStyler;
use QCubed\Type;
use QCubed\Control\FormBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Control\TableBase;

/**
 * Class ColumnBase
 *
 * Represents a column for a Table control. Different subclasses (see below) allow accessing and fetching the data
 * for each cells in a variety of ways
 *
 * @property string                 $Name           name of the column
 * @property string                 $CssClass       CSS class of the column. This will be applied to every cell in the column. Use ColStyper
 * 													to set the class for the actual 'col' tag if using col tags.
 * @property string                 $HeaderCssClass CSS class of the column's cells when it's rendered in a table header
 * @property boolean                $HtmlEntities   if true, cell values will be converted using htmlentities()
 * @property boolean                $RenderAsHeader if true, all cells in the column will be rendered with a <<th>> tag instead of <<td>>
 * @property integer                $Id             HTML id attribute to put in the col tag
 * @property integer                $Span           HTML span attribute to put in the col tag
 * @property-read TableBase  $ParentTable    parent table of the column
 * @property-write TableBase $_ParentTable   Parent table of this column
 * @property-write callable $CellParamsCallback A callback to set the html parameters of a generated cell
 * @property boolean                $Visible        Whether the column will be drawn. Defaults to true.
 * @property-read TagStyler		$CellStyler		The tag styler for the cells in the column
 * @property-read TagStyler		$HeaderCellStyler		The tag styler for the header cells in the column
 * @property-read TagStyler		$ColStyler		The tag styler for the col tag in the column
 * @was QAbstractHtmlTableColumn
 * @package QCubed\Table
 */
abstract class ColumnBase extends ObjectBase
{
    /** @var string */
    protected $strName;
    /** @var string */
    protected $strCssClass = null;
    /** @var string */
    protected $strHeaderCssClass = null;
    /** @var boolean */
    protected $blnHtmlEntities = true;
    /** @var boolean */
    protected $blnRenderAsHeader = false;
    /** @var TableBase */
    protected $objParentTable = null;
    /** @var integer */
    protected $intSpan = 1;
    /** @var string optional id for column tag rendering and datatables */
    protected $strId = null;
    /** @var bool Easy way to hide a column without removing the column. */
    protected $blnVisible = true;
    /** @var callable Callback to modify the html attributes of the generated cell. */
    protected $cellParamsCallback = null;
    /** @var TagStyler Styles for each cell. Usually this should be done in css for efficient code generation. */
    protected $objCellStyler;
    /** @var TagStyler Styles for each header cell. Usually this should be done in css for efficient code generation. */
    protected $objHeaderCellStyler;
    /** @var TagStyler Styles for each col. Usually this should be done in css for efficient code generation. */
    protected $objColStyler;

    /**
     * @param string $strName Name of the column
     */
    public function __construct($strName)
    {
        $this->strName = $strName;
    }

    /**
     *
     * Render the header cell including opening and closing tags.
     *
     * This will be called by the data table if ShowHeader is on, and will only
     * be called for the top line item.
     *
     */
    public function renderHeaderCell()
    {
        if (!$this->blnVisible) {
            return '';
        }

        $cellValue = $this->fetchHeaderCellValue();
        if ($this->blnHtmlEntities) {
            $cellValue = Q\QString::htmlEntities($cellValue);
        }
        if ($cellValue == '' && Application::instance()->context()->isBrowser(Q\Context::INTERNET_EXPLORER)) {
            $cellValue = '&nbsp;';
        }

        return Q\Html::renderTag('th', $this->getHeaderCellParams(), $cellValue);
    }

    /**
     * Returns the text to print in the header cell, if one is to be drawn. Override if you want
     * something other than the default.
     */
    public function fetchHeaderCellValue()
    {
        return $this->strName;
    }

    /**
     * Returns an array of key/value pairs to insert as parameters in the header cell. Override and add
     * more if you need them.
     * @return array
     */
    public function getHeaderCellParams()
    {
        $aParams['scope'] = 'col';
        if ($this->strHeaderCssClass) {
            $aParams['class'] = $this->strHeaderCssClass;
        }
        if ($this->objHeaderCellStyler) {
            $aParams = $this->objHeaderCellStyler->getHtmlAttributes($aParams);
        }
        return $aParams;
    }

    /**
     * Render a cell.
     * Called by data table for each cell. Override and call with $blnHeader = true if you want
     * this individual cell to render with <<th>> tags instead of <<td>>.
     *
     * @param mixed   $item
     * @param boolean $blnAsHeader
     *
     * @return string
     */
    public function renderCell($item, $blnAsHeader = false)
    {
        if (!$this->blnVisible) {
            return '';
        }

        $cellValue = $this->fetchCellValue($item);
        if ($this->blnHtmlEntities) {
            $cellValue = Q\QString::htmlEntities($cellValue);
        }
        if ($cellValue == '' && Application::instance()->context()->isBrowser(Q\Context::INTERNET_EXPLORER)) {
            $cellValue = '&nbsp;';
        }

        if ($blnAsHeader || $this->blnRenderAsHeader) {
            $strTag = 'th';
        } else {
            $strTag = 'td';
        }

        return Q\Html::renderTag($strTag, $this->getCellParams($item), $cellValue);
    }

    /**
     * Return a key/val array of items to insert inside the cell tag.
     * Handles class, style, and id already. Override to add additional items, like an onclick handler.
     *
     * @param mixed $item
     *
     * @return array
     */
    protected function getCellParams($item)
    {
        $aParams = array();

        if ($strClass = $this->getCellClass($item)) {
            $aParams['class'] = $strClass;
        }

        if ($strId = $this->getCellId($item)) {
            $aParams['id'] = $strId;
        }

        if ($this->blnRenderAsHeader) {
            // assume this means it is a row header
            $aParams['scope'] = 'row';
        }

        $strStyle = $this->getCellStyle($item);

        if ($this->objCellStyler) {
            $aStyles = null;
            if ($strStyle) {
                $aStyles = explode(';', $strStyle);
            }
            $aParams = $this->objCellStyler->getHtmlAttributes($aParams, $aStyles);
        } elseif ($strStyle) {
            $aParams['style'] = $strStyle;
        }

        if ($this->cellParamsCallback) {
            $a = call_user_func($this->cellParamsCallback, $item);
            $aParams = array_merge($aParams, $a);
        }

        return $aParams;
    }

    /**
     * Return the class of the cell.
     *
     * @param mixed $item
     *
     * @return string
     */
    protected function getCellClass($item)
    {
        if ($this->strCssClass) {
            return $this->strCssClass;
        }
        return '';
    }

    /**
     * Return the id of the cell.
     *
     * @param mixed $item
     *
     * @return string
     */
    protected function getCellId($item)
    {
        return '';
    }

    /**
     * Return the style string for the cell.
     *
     * @param mixed $item
     *
     * @return string
     */
    protected function getCellStyle($item)
    {
        return '';
    }

    /**
     * Return the raw string that represents the cell value.
     *
     * @param mixed $item
     */
    abstract public function fetchCellValue($item);

    /**
     * Render the column tag.
     * This special tag can control specific features of columns, but is generally optional on a table.
     *
     * @return string
     */
    public function renderColTag()
    {
        return Q\Html::renderTag('col', $this->getColParams(), null, true);
    }

    /**
     * Return a key/value array of parameters to put in the col tag.
     * Override to add parameters.
     */
    protected function getColParams()
    {
        $aParams = array();
        if ($this->intSpan > 1) {
            $aParams['span'] = $this->intSpan;
        }
        if ($this->strId) {
            $aParams['id'] = $this->strId;
        }

        if ($this->objColStyler) {
            $aParams = $this->objColStyler->getHtmlAttributes($aParams);
        }

        return $aParams;
    }

    /**
     * Prepare to serialize references to the form.
     */
    public function sleep()
    {
        $this->cellParamsCallback = ControlBase::sleepHelper($this->cellParamsCallback);
    }

    /**
     * The object has been unserialized, so fix up pointers to embedded objects.
     * @param FormBase $objForm
     */
    public function wakeup(FormBase $objForm)
    {
        $this->cellParamsCallback = ControlBase::wakeupHelper($objForm, $this->cellParamsCallback);
    }

    /**
     * Override to check for post data in your column if needed.
     */
    public function parsePostData()
    {
    }

    /**
     * PHP magic method
     *
     * @param string $strName
     *
     * @return mixed
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case 'Name':
                return $this->strName;
            case 'CssClass':
                return $this->strCssClass;
            case 'HeaderCssClass':
                return $this->strHeaderCssClass;
            case 'HtmlEntities':
                return $this->blnHtmlEntities;
            case 'RenderAsHeader':
                return $this->blnRenderAsHeader;
            case 'ParentTable':
                return $this->objParentTable;
            case 'Span':
                return $this->intSpan;
            case 'Id':
                return $this->strId;
            case 'Visible':
                return $this->blnVisible;
            case 'CellStyler':
                if (!$this->objCellStyler) {
                    $this->objCellStyler = new TagStyler();
                }
                return $this->objCellStyler;
            case 'HeaderCellStyler':
                if (!$this->objHeaderCellStyler) {
                    $this->objHeaderCellStyler = new TagStyler();
                }
                return $this->objHeaderCellStyler;
            case 'ColStyler':
                if (!$this->objColStyler) {
                    $this->objColStyler = new TagStyler();
                }
                return $this->objColStyler;


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
     * PHP Magic method
     *
     * @param string $strName
     * @param string $mixValue
     *
     * @return mixed|void
     * @throws Caller
     * @throws InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "Name":
                try {
                    $this->strName = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "CssClass":
                try {
                    $this->strCssClass = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "HeaderCssClass":
                try {
                    $this->strHeaderCssClass = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "HtmlEntities":
                try {
                    $this->blnHtmlEntities = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "RenderAsHeader":
                try {
                    $this->blnRenderAsHeader = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "Span":
                try {
                    $this->intSpan = Type::cast($mixValue, Type::INTEGER);
                    if ($this->intSpan < 1) {
                        throw new Caller("Span must be 1 or greater.");
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "Id":
                try {
                    $this->strId = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "Visible":
                try {
                    $this->blnVisible = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "CellParamsCallback":
                $this->cellParamsCallback = Type::cast($mixValue, Type::CALLABLE_TYPE);
                break;

            case "_ParentTable":
                try {
                    $this->objParentTable = Type::cast($mixValue, 'QCubed\Control\TableBase');
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
