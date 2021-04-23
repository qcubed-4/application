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
use QCubed\Query\QQ;
use QCubed\Type;

/**
 * Class PropertyColumn
 *
 * Column to dispay a  property of an object, as in $object->Property
 * If your DataSource is an array of objects, use this column to display a particular property of each object.
 * Can search with depth to, as in $obj->Prop1->Prop2.
 *
 * @property string $Property the property to use when accessing the objects in the DataSource array. Can be a s
 *  series of properties separated with '->', i.e. 'Prop1->Prop2->Prop3' will find the Prop3 item inside the Prop2 object,
 *  inside the Prop1 object, inside the current object.
 * @property boolean $NullSafe if true the value fetcher will check for nulls before accessing the properties
 * @was QHtmlTablePropertyColumn
 * @package QCubed\Table
 */
class PropertyColumn extends DataColumn
{
    protected $strProperty;
    protected $strPropertiesArray;
    protected $blnNullSafe = true;

    /**
     * @param string $strName name of the column
     * @param string|null $strProperty the property name to use when accessing the DataSource row object.
     *                                 Can be null, in which case object will have the ->__toString() function called on it.
     * @param \QCubed\Query\Node\NodeBase $objBaseNode if not null, the OrderBy and ReverseOrderBy clauses will be created using the property path and the given database node
     */
    public function __construct($strName, $strProperty, $objBaseNode = null)
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

    public function fetchCellObject($item)
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
    public function __get($strName)
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
     * @param string $mixValue
     *
     * @return mixed|void
     * @throws Exception
     * @throws Caller
     * @throws InvalidCast
     */
    public function __set($strName, $mixValue)
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
