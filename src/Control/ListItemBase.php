<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed;
use QCubed\ObjectBase;
use QCubed\Type;

/**
 * Class ListItemBase
 *
 * This base class represents an item in some kind of html item list. There are many types of possible lists, including
 * checklists and hierarchical lists. This is the core functionality common to all of them.
 *
 * @package Controls
 * @property string $Name      Usually what gets displayed. Can be overridden by the Label attribute in certain situations.
 * @property string $Value     is any text that represents the value of the item (e.g. maybe a DB Id)
 * @property-read boolean $Empty     true when both $Name and $Value are null, in which case this item will be rendered with an empty value in the list control
 * @property ListItemStyle $ItemStyle Custom HTML attributes for this particular item.
 * @property string $Text      synonym of Name. Used to store longer text with the item.
 * @property string $Id    A place to save an id for the item. It is up to the corresponding list class to use this in the object.
 * @was QListItemBase
 * @package QCubed\Control
 */
class ListItemBase extends ObjectBase
{
    ///////////////////////////
    // Private Member Variables
    ///////////////////////////
    /** @var null|string Name of the Item */
    protected $strName = null;
    /** @var null|string Value of the Item */
    protected $strValue = null;
    /** @var null|ListItemStyle Custom attributes of the list item */
    protected $objItemStyle;
    /** @var  null|string the internal id */
    protected $strId;


    /////////////////////////
    // Methods
    /////////////////////////
    /**
     * Creates a ListItem
     *
     * @param string $strName is the displayed Name or Text of the Item
     * @param string|null $strValue is any text that represents the value of the ListItem (e.g. maybe a DB Id)
     * @param null|ListItemStyle $objItemStyle is the item style. If provided here, it is referenced and shared with other items.
     *
     * @throws Exception|Caller
     */
    public function __construct($strName, $strValue = null, $objItemStyle = null)
    {
        $this->strName = $strName;
        $this->strValue = $strValue;
        $this->objItemStyle = $objItemStyle;
    }

    /**
     * Returns the list item styler
     *
     * @return null|ListItemStyle
     */
    public function getStyle()
    {
        if (!$this->objItemStyle) {
            $this->objItemStyle = new ListItemStyle();
        }
        return $this->objItemStyle;
    }

    /**
     * Returns the css style of the list item
     * @deprecated
     *
     * @return string
     */
    /*
    public function getAttributes()
    {
        $strToReturn = $this->getStyle()->getAttributes();
        return $strToReturn;
    }*/

    /**
     * Stub functions required for QListItemManager trait support
     */
    public function markAsModified()
    {
    }

    /**
     *
     */
    public function reindex()
    {
    }

    /**
     * @param string $strId
     * @return null|ListItemBase
     */
    public function findItem($strId)
    {
        return null;
    }

    /**
     * Return the id. Used by trait.
     * @return string
     */
    public function getId()
    {
        return $this->strId;
    }

    /**
     * Set the Id. Used by trait.
     * @param $strId
     */
    public function setId($strId)
    {
        $this->strId = $strId;
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * PHP magic method
     * @param string $strName
     *
     * @return mixed
     * @throws Exception|Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case "Text":
            case "Name":
                return $this->strName;
            case "Value":
                return $this->strValue;
            case "ItemStyle":
                return $this->objItemStyle;
            case "Empty":
                return $this->strValue == null && $this->strName == null;
            case "Id":
                return $this->strId;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /////////////////////////
    // Public Properties: SET
    /////////////////////////
    /**
     * PHP magic method
     * @param string $strName
     * @param string $mixValue
     *
     * @return void
     * @throws Exception|Caller|InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "Text":
            case "Name":
                try {
                    $this->strName = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Value":
                try {
                    $this->strValue = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "ItemStyle":
                try {
                    $this->objItemStyle = Type::cast($mixValue, "\\QCubed\\Control\\ListItemStyle");
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
            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }
}
