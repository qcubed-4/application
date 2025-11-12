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
use Exception;

/**
 * Class Indexed
 *
 * A type of column that should be used when the DataSource items are arrays
 *
 * @property int|string $Index the index or key to use when accessing the arrays in the DataSource array
 * @package QCubed\Table
 */
class IndexedColumn extends DataColumn
{
    protected string|int $mixIndex;

    /**
     * @param string $strName name of the column
     * @param int|string $mixIndex the index or key to use when accessing the DataSource row array
     */
    public function __construct(string $strName, int|string $mixIndex)
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
    public function fetchCellObject(mixed $item): mixed
    {

        if (isset($item[$this->mixIndex])) {
            return $item[$this->mixIndex];
        }
        return '';

//        if (is_array($this->mixIndex)) {
//            foreach ($this->mixIndex as $i) {
//                if (!isset($item[$i])) {
//                    return '';
//                }
//                $item = $item[$i];
//            }
//            return $item;
//        }
//        elseif (isset($item[$this->mixIndex])) {
//            return $item[$this->mixIndex];
//        } else {
//            return '';
//        }
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
     * @param mixed $mixValue
     *
     * @return void
     * @throws Caller
     * @throws Exception
     */
    public function __set(string $strName, mixed $mixValue): void
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