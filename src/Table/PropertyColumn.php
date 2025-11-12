<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Table;

use Exception;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Query\QQ;
use QCubed\Type;

/**
 * Class PropertyColumn
 *
 * Column to display a property of an object, as in $object->Property
 * If your DataSource is an array of objects, use this column to display a particular property of each object.
 * Can search with depth too, as in $obj->Prop1->Prop2
 * @property string $Property Attribute to use when accessing objects in the DataSource array. Can be s
 *  a series of attributes separated by '->', i.e. 'Prop1->Prop2->Prop3' finds Prop3 within Prop2,
 *  within Prop1, within the current object.
 * @property boolean $NullSafe if true, the value fetcher will check for nulls before accessing the properties
 * @package QCubed\Table
 */
class PropertyColumn extends DataColumn
{
    protected string $strProperty;
    protected array $strPropertiesArray = [];
    /**
     * Indicates whether the operation should be performed in a null-safe manner.
     */
    protected bool $blnNullSafe = true;

    /**
     * Constructs a new instance of the class.
     *
     * @param string $strName The name to initialize the instance with.
     * @param string $strProperty A specific property related to the instance.
     * @param object|null $objBaseNode An optional base node to traverse and initialize order clauses.
     * @throws Caller
     * @throws InvalidCast
     */
    public function __construct(string $strName, string $strProperty, ?object $objBaseNode = null)
    {
        parent::__construct($strName);
        $this->Property = $strProperty;

        if ($objBaseNode != null) {
            foreach ($this->strPropertiesArray as $strProperty) {
                $objBaseNode = $objBaseNode->$strProperty;
            }

            $this->OrderByClause = QQ::orderBy($objBaseNode);
            $this->ReverseOrderByClause = QQ::orderBy($objBaseNode, 'desc');
        }
    }

    /**
     * Retrieves a cell object by traversing the properties of the provided item.
     *
     * @param mixed $item The item to traverse and fetch the cell object from. It can be a null or any object with properties.
     * @return mixed|null Returns the fetched cell object, or null if $item or a traversed property is null and null-safety is enabled.
     */
    public function fetchCellObject(mixed $item): mixed
    {
        if ($this->blnNullSafe && $item == null) {
            return null;
        }
        foreach ($this->strPropertiesArray as $strProperty) {
            $item = $item->$strProperty;
            if ($this->blnNullSafe && $item == null) {
                break;
            }
        }
        return $item;
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
            case 'Property':
                return $this->strProperty;
            case 'NullSafe':
                return $this->blnNullSafe;
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
     * @throws InvalidCast
     * @throws Exception
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case "Property":
                try {
                    $this->strProperty = Type::cast($mixValue, Type::STRING);
                    $this->strPropertiesArray = $this->strProperty ? explode('->', $this->strProperty) : array();
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "NullSafe":
                try {
                    $this->blnNullSafe = Type::cast($mixValue, Type::BOOLEAN);
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