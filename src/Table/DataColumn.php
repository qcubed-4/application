<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Table;

use QCubed\Control\FormBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use Exception;
use QCubed\Project\Control\ControlBase;
use QCubed\QDateTime;
use QCubed\Type;

/**
 * Class DataColumn
 *
 * An abstract column designed to work with DataGrid and other tables that require more than basic columns.
 * Supports post-processing of cell contents for further formatting and OrderBy clauses.
 *
 * @property mixed $OrderByClause        order by info for sorting the column in ascending order. Used by subclasses.
 *    Most often this is a \QCubed\Query\QQ::Clause, but can be any data needed.
 * @property mixed $ReverseOrderByClause order by info for sorting the column in descending order.
 * @property string $Format               the default format to use for FetchCellValueFormatted(). Used by QDataTables plugin.
 *    For date columns it should be a format accepted by \QCubed\QDateTime::qFormat()
 * @property-write string $PostMethod           after the cell object is retrieved, call this method on the obtained object
 * @property-write callback $PostCallback         after the cell object is retrieved, call this callback on the obtained object.
 *    If $PostMethod is also set, this will be called after that method call.
 * @package QCubed\Table
 */
abstract class DataColumn extends ColumnBase
{
    /** @var mixed Order By information. Can be a \QCubed\Query\QQ::Clause, or any kind of object depending on your need */
    protected mixed $objOrderByClause = null;
    /** @var mixed */
    protected mixed $objReverseOrderByClause = null;
    /** @var string|null */
    protected ?string $strFormat = null;
    /** @var string|null */
    protected ?string $strPostMethod = null;
    /** @var callback */
    protected $objPostCallback = null;

    /**
     * Return the raw string that represents the cell value.
     * This version uses a combination of post-processing strategies so that you can set
     * column options to format the raw data. If no
     * options are set, then $item will just pass through, or __toString() will be called
     * if it's an object. If none of these work for you, just override FetchCellObject and
     * return your formatted string from there.
     *
     * @param mixed $item
     *
     * @return string
     */
    public function fetchCellValue(mixed $item): string
    {
        $cellValue = $this->fetchCellObject($item);

        if ($cellValue !== null && $this->strPostMethod) {
            $strPostMethod = $this->strPostMethod;
            assert(is_callable([$cellValue, $strPostMethod]));    // Malformed post-method, or the item is not an object
            $cellValue = $cellValue->$strPostMethod();
        }
        if ($this->objPostCallback) {
            $cellValue = call_user_func($this->objPostCallback, $cellValue);
        }
        if ($cellValue === null) {
            return '';
        }

        if ($cellValue instanceof QDateTime) {
            return $cellValue->qFormat($this->strFormat);
        }
        if (is_object($cellValue)) {
            $cellValue = (string)$cellValue;
        }
        if ($this->strFormat) {
            return sprintf($this->strFormat, $cellValue);
        }

        return $cellValue;
    }

    /**
     * Return the value of the cell. FetchCellValue will process this more if needed.
     * Default returns an entire data row and relies on FetchCellValue to extract the necessary data.
     *
     * @param mixed $item
     */
    abstract public function fetchCellObject(mixed $item): mixed;

    /**
     * Fix up a possible embedded reference to the form.
     */
    public function sleep(): void
    {
        $this->objPostCallback = ControlBase::sleepHelper($this->objPostCallback);
        parent::sleep();
    }

    /**
     * The object has been unserialized, so fix up pointers to embedded objects.
     * @param FormBase $objForm
     */
    public function wakeup(FormBase $objForm): void
    {
        parent::wakeup($objForm);
        $this->objPostCallback = ControlBase::wakeupHelper($objForm, $this->objPostCallback);
    }

    /**
     * PHP magic method
     *
     * @param string $strName
     *
     * @return mixed
     * @throws Caller
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case "OrderByClause":
                return $this->objOrderByClause;
            case "ReverseOrderByClause":
                return $this->objReverseOrderByClause;
            case "Format":
                return $this->strFormat;

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
     * PHP magic method
     *
     * @param string $strName
     * @param mixed $mixValue
     *
     * @return void
     * @throws Exception
     * @throws Caller
     * @throws InvalidCast
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case "OrderByClause":
                $this->objOrderByClause = $mixValue;
                break;

            case "ReverseOrderByClause":
                $this->objReverseOrderByClause = $mixValue;
                break;

            case "Format":
                try {
                    $this->strFormat = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "PostMethod":
                try {
                    $this->strPostMethod = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "PostCallback":
                $this->objPostCallback = $mixValue;
                break;

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
