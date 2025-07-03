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
 * subitems.
 *
 * @property string $Anchor If set, the anchor text to print in the href= string when drawing as an anchored item.
 * @property mixed|string|null $Tag
 * @package QCubed\Control
 */
class HListItem extends ListItemBase
{

    /** Allows items to have subitems and manipulate them with the same interface */
    use ListItemManagerTrait;

    ///////////////////////////
    // Private Member Variables
    ///////////////////////////
    //protected ?string $strId = null;
    /** @var  string|null if this has an anchor, what to redirect to. Could be JavaScript or a page. */
    protected ?string $strAnchor = null;
    /** @var  string|null  a custom tag to draw the item with. */
    protected ?string $strTag = null;
    /** @var  TagStyler|null for styling the subtag if needed. */
    protected ?TagStyler $objSubTagStyler = null;


    /////////////////////////
    // Methods
    /////////////////////////
    /**
     * Constructor to initialize the object with a name, value, and optional anchor.
     *
     * @param string $strName The name of the object.
     * @param string|null $strValue The value associated with the object, optional.
     * @param mixed|null $strAnchor An optional anchor parameter.
     *
     * @return void
     */
    public function __construct(string $strName, ?string $strValue = null, mixed $strAnchor = null)
    {
        parent::__construct($strName, $strValue);
        $this->strAnchor = $strAnchor;
    }

    /**
     * Adds an item to the list. The item can be an instance of HListItem or a string to
     * create a new HListItem with optional value and anchor.
     *
     * @param string|HListItem $mixListItemOrName Either an HListItem instance or a string for the item name.
     * @param string|null $strValue Optional value for the item if $mixListItemOrName is a string.
     * @param string|null $strAnchor An optional anchor for the item if $mixListItemOrName is a string.
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function addItem(string|HListItem $mixListItemOrName, ?string $strValue = null, ?string $strAnchor = null): void
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
     * @param array $objItemArray An array of HListItems or key=>val pairs to be sent to the contractor.
     * @throws Caller
     * @throws InvalidCast
     */
    public function addItems(array $objItemArray): void
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
     * Retrieves the sub-tag styler instance. If it does not already exist, a new
     * TagStyler instance is created and returned.
     *
     * @return TagStyler|null The TagStyler instance for styling sub-tags.
     */
    public function getSubTagStyler(): ?TagStyler
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
     * Retrieves the value of a property based on its name. If the property is not found,
     * it will attempt to fetch the value from the parent class or throw an exception.
     *
     * @param string $strName The name of the property to retrieve.
     * @return mixed The value of the requested property.
     * @throws Caller If the property does not exist.
     */
    public function __get(string $strName): mixed
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
     * @param mixed $mixValue
     *
     * @return void
     * @throws Caller|InvalidCast
     */
    public function __set(string $strName, mixed $mixValue): void
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
