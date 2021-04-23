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
use QCubed\TagStyler;
use QCubed\Type;

/**
 * Class HListItem
 *
 * Represents an item in a hierarchical item list. Uses the QListItemManager trait to manage the interface for adding
 * sub-items.
 *
 * @property string $Anchor If set, the anchor text to print in the href= string when drawing as an anchored item.
 * @was HListItem
 * @package QCubed\Control
 */
class HListItem extends ListItemBase
{

    /** Allows items to have sub items, and manipulate them with the same interface */
    use ListItemManagerTrait;

    ///////////////////////////
    // Private Member Variables
    ///////////////////////////
    /** @var  string|null if this has an anchor, what to redirect to. Could be javascript or a page. */
    protected $strAnchor;
    /** @var  string|null  a custom tag to draw the item with. */
    protected $strTag;
    /** @var  TagStyler for styling the subtag if needed. */
    protected $objSubTagStyler;


    /////////////////////////
    // Methods
    /////////////////////////
    /**
     * Creates a QListItem
     *
     * @param string $strName is the displayed Name or Text of the Item
     * @param string|null $strValue is any text that represents the value of the ListItem (e.g. maybe a DB Id)
     * @param string|null $strAnchor is an href anchor that will be associated with item
     *
     * @throws Exception|Caller
     */
    public function __construct($strName, $strValue = null, $strAnchor = null)
    {
        parent::__construct($strName, $strValue);
        $this->strAnchor = $strAnchor;
    }

    /**
     * Add an item by a HListItem or a name,value pair
     * @param string|HListItem $mixListItemOrName
     * @param string|null $strValue
     * @param null|string $strAnchor
     */
    public function addItem($mixListItemOrName, $strValue = null, $strAnchor = null)
    {
        if (gettype($mixListItemOrName) == Type::OBJECT) {
            $objListItem = Type::cast($mixListItemOrName, "\QCubed\Control\HListItem");
        } else {
            $objListItem = new HListItem($mixListItemOrName, $strValue, $strAnchor);
        }

        $this->addListItem($objListItem);
    }

    /**
     * Adds an array of items, or an array of key=>value pairs.
     * @param array $objItemArray An array of HListItems or key=>val pairs to be sent to contructor.
     */
    public function addItems($objItemArray)
    {
        if (!$objItemArray) {
            return;
        }

        if (!is_object(reset($objItemArray))) {
            foreach ($objItemArray as $key => $val) {
                $this->addItem($key, $val);
            }
        } else {
            $this->addListItems($objItemArray);
        }
    }

    /**
     * Returns a TagStyler for styling the sub tag.
     * @return TagStyler
     */
    public function getSubTagStyler()
    {
        if (!$this->objSubTagStyler) {
            $this->objSubTagStyler = new TagStyler();
        }
        return $this->objSubTagStyler;
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
            case "Anchor":
                return $this->strAnchor;
            case "Tag":
                return $this->strTag;

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
     * @throws Caller|InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "Anchor":
                try {
                    $this->strAnchor = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Tag":
                try {
                    $this->strTag = Type::cast($mixValue, Type::STRING);
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
