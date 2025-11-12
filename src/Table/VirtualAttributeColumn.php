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
use QCubed\Exception\InvalidCast;
use Exception;
use QCubed\Query\QQ;
use QCubed\Type;

/**
 * Class VirtualAttributeColumn
 *
 * A column to display a virtual attribute from a database record.
 *
 * @property string $Attribute
 * @package QCubed\Table
 */
class VirtualAttributeColumn extends DataColumn
{
    protected mixed $strAttribute;

    /**
     * Constructor method for initializing the object with a name and an optional attribute.
     *
     * @param string $strName The name of the object.
     * @param string|null $strAttribute An optional attribute to be associated with the object.
     * @throws Caller
     * @throws InvalidCast
     */
    public function __construct(string $strName, ?string $strAttribute = null)
    {
        parent::__construct($strName);
        if ($strAttribute) {
            $this->strAttribute = $strAttribute;
        }

        $this->OrderByClause = QQ::orderBy(QQ::virtual($strAttribute));
        $this->ReverseOrderByClause = QQ::orderBy(QQ::virtual($strAttribute), false);
    }

    /**
     * Fetches the virtual attribute associated with the specified item.
     *
     * @param mixed $item The item from which the virtual attribute will be retrieved.
     * @return mixed The value of the virtual attribute associated with the item.
     */
    public function fetchCellObject(mixed $item): mixed
    {
        return $item->getVirtualAttribute($this->strAttribute);
    }

    /**
     * PHP magic method
     *
     * @param string $strName
     *
     * @return mixed
     * @throws Exception
     * @throws Caller
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'Attribute':
                return $this->strAttribute;
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
            case "Attribute":
                $this->strAttribute = Type::cast($mixValue, Type::STRING);
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