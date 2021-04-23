<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Table;

use QCubed\Exception\Caller;

/**
 * Class Indexed
 *
 * A type of column that should be used when the DataSource items are arrays
 *
 * @property int|string $Index the index or key to use when accessing the arrays in the DataSource array
 * @was QHtmlTableIndexedColumn
 * @package QCubed\Table
 */
class IndexedColumn extends DataColumn
{
    protected $mixIndex;

    /**
     * @param string $strName name of the column
     * @param int|string $mixIndex the index or key to use when accessing the DataSource row array
     */
    public function __construct($strName, $mixIndex)
    {
        parent::__construct($strName);
        $this->mixIndex = $mixIndex;
    }

    /**
     * Returns the displayed value given an item in the data array.
     *
     * @param mixed $item
     * @return string
     */
    public function fetchCellObject($item)
    {
        if (is_array($this->mixIndex)) {
            foreach ($this->mixIndex as $i) {
                if (!isset($item[$i])) {
                    return '';
                }
                $item = $item[$i];
            }
            return $item;
        }
        elseif (isset($item[$this->mixIndex])) {
            return $item[$this->mixIndex];
        } else {
            return '';
        }
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
            case 'Index':
                return $this->mixIndex;
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
     * @param string $mixValue
     *
     * @return mixed|void
     * @throws Caller
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "Index":
                $this->mixIndex = $mixValue;
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
