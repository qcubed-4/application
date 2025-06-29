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
use QCubed\Exception\IndexOutOfRange;
use QCubed\Exception\InvalidCast;
use QCubed\Type;

/**
 * Class ListItemManagerTrait
 *
 * This is a trait that presents an interface for managing an item list. It is used by the QListControl, QHListControl,
 * and the HListItem classes, the latter because a HListItem can itself contain a list of other items.
 *
 * Note that some abstract methods are declared here that must be implemented by the using class:
 * GetId() - returns the id
 * MarkAsModified() - marks the object as modified. Optional.
 *
 * @package QCubed\Control
 */
trait ListItemManagerTrait
{
    ///////////////////////////
    // Private Member Variables
    ///////////////////////////
    /** @var ListItemBase[] an array of subitems if this is a recursive item. */
    protected array $objListItemArray = [];

    /**
     * Adds a new item to the list and assigns it a unique ID based on the parent control's ID.
     *
     * @param ListItemBase $objListItem The item to be added to the list.
     * @return void
     */
    public function addListItem(ListItemBase $objListItem): void
    {
        if ($strControlId = $this->getId()) {
            $num = 0;
            if ($this->objListItemArray) {
                $num = count($this->objListItemArray);
            }
            $objListItem->setId($strControlId . '_' . $num);    // auto assign the ID based on parent ID
            $objListItem->reindex();
        }
        $this->objListItemArray[] = $objListItem;
        $this->markAsModified();
    }

    /**
     * Allows you to add a ListItem at a certain index
     * Unlike AddItem, this will insert the ListItem at whatever index is passed to the function.  Additionally,
     * only a ListItem object can be passed (as opposed to an object or strings)
     *
     * @param integer $intIndex index at which the item should be inserted
     * @param ListItemBase $objListItem the ListItem which shall be inserted
     *
     * @throws IndexOutOfRange
     * @throws InvalidCast|Caller
     */
    public function addItemAt(int $intIndex, ListItemBase $objListItem): void
    {
        try {
            $intIndex = Type::cast($intIndex, Type::INTEGER);
        } catch (InvalidCast $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
        if ($intIndex >= 0 &&
            (!$this->objListItemArray && $intIndex == 0 ||
                $intIndex <= count($this->objListItemArray))
        ) {
            for ($intCount = count($this->objListItemArray); $intCount > $intIndex; $intCount--) {
                $this->objListItemArray[$intCount] = $this->objListItemArray[$intCount - 1];
            }
        } else {
            throw new IndexOutOfRange($intIndex, "AddItemAt()");
        }

        $this->objListItemArray[$intIndex] = $objListItem;
        $this->reindex();
    }

    /**
     * Reindex the IDs of the items based on the current item. We manage all the IDs in the list internally
     * to be able to get to an item in the list quickly and to make sure the IDs are unique.
     */
    public function reindex(): void
    {
        if ($this->getId() && $this->objListItemArray) {
            for ($i = 0; $i < $this->getItemCount(); $i++) {
                $this->objListItemArray[$i]->setId($this->getId() . '_' . $i);    // assign the ID based on parent ID
                $this->objListItemArray[$i]->reindex();
            }
        }
    }

    /**
     * Marks the current object as modified. This method should be implemented in derived classes to handle functionality
     * related to flagging the object state as changed or dirty, typically for persisting updates or tracking state changes.
     */
    abstract public function markAsModified(): void;

    /**
     * Retrieves the ID of the entity or object.
     *
     * @return string|null The unique identifier as a string.
     */
    abstract public function getId(): ?string;

    /**
     * Adds an array of ListItemBase objects to the current list.
     *
     * @param array $objListItemArray An array of ListItemBase objects to be added. The array must only contain instances of ListItemBase.
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function addListItems(array $objListItemArray): void
    {
        try {
            $objListItemArray = Type::cast($objListItemArray, Type::ARRAY_TYPE);
            if ($objListItemArray) {
                if (!reset($objListItemArray) instanceof ListItemBase) {
                    throw new Caller('Not an array of ListItemBase types');
                }
            }
        } catch (InvalidCast $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }

        if ($this->objListItemArray) {
            $this->objListItemArray = array_merge($this->objListItemArray, $objListItemArray);
        } else {
            $this->objListItemArray = $objListItemArray;
        }
        $this->reindex();
        $this->markAsModified();
    }

    /**
     * Retrieve the ListItem at the specified index location
     *
     * @param integer $intIndex
     *
     * @return ListItemBase
     * @throws InvalidCast
     * @throws IndexOutOfRange|Caller
     */
    public function getItem(int $intIndex): ListItemBase
    {
        try {
            $intIndex = Type::cast($intIndex, Type::INTEGER);
        } catch (InvalidCast $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
        if (($intIndex < 0) ||
            ($intIndex >= count($this->objListItemArray))
        ) {
            throw new IndexOutOfRange($intIndex, "GetItem()");
        }

        return $this->objListItemArray[$intIndex];
    }

    /**
     * Retrieves all items from the internal list.
     *
     * @return array Returns an array containing all items in the list. If the list is empty, an empty array is returned.
     */
    public function getAllItems(): array
    {
        return $this->objListItemArray;
    }

    /**
     * Removes all the items in objListItemArray
     */
    public function removeAllItems(): void
    {
        $this->markAsModified();
        $this->objListItemArray = [];
    }

    /**
     * Removes a ListItem at the specified index location
     *
     * @param integer $intIndex
     *
     * @throws IndexOutOfRange
     * @throws InvalidCast|Caller
     */
    public function removeItem(int $intIndex): void
    {
        try {
            $intIndex = Type::cast($intIndex, Type::INTEGER);
        } catch (InvalidCast $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
        if (($intIndex < 0) ||
            ($intIndex > (count($this->objListItemArray) - 1))
        ) {
            throw new IndexOutOfRange($intIndex, "RemoveItem()");
        }
        for ($intCount = $intIndex; $intCount < count($this->objListItemArray) - 1; $intCount++) {
            $this->objListItemArray[$intCount] = $this->objListItemArray[$intCount + 1];
        }

        $this->objListItemArray[$intCount] = null;
        unset($this->objListItemArray[$intCount]);
        $this->markAsModified();
        $this->reindex();
    }

    /**
     * Replaces a QListItem at $intIndex. This combines the RemoveItem() and AddItemAt() operations.
     *
     * @param integer $intIndex
     * @param ListItem $objListItem
     *
     * @throws InvalidCast|Caller
     */
    public function replaceItem(int $intIndex, ListItem $objListItem): void
    {
        try {
            $intIndex = Type::cast($intIndex, Type::INTEGER);
        } catch (InvalidCast $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
        $objListItem->setId($this->getId() . '_' . $intIndex);
        $this->objListItemArray[$intIndex] = $objListItem;
        $objListItem->reindex();
        $this->markAsModified();
    }

    /**
     * Return the count of the items.
     *
     * @return int
     */
    public function getItemCount(): int
    {
        $count = 0;
        if ($this->objListItemArray) {
            $count = count($this->objListItemArray);
        }
        return $count;
    }

    /**
     * Searches for and retrieves an item from the list based on the specified ID.
     *
     * @param string $strId The ID of the item to be found, formatted as a string with optional hierarchical components.
     * @return ListItemBase|null Returns the found item if it exists, or null if no item is found or the list is empty.
     */
    public function findItem(string $strId): ?ListItemBase
    {
        if (!$this->objListItemArray) {
            return null;
        }

        $objFoundItem = null;
        $a = explode('_', $strId, 3);
        if (isset($a[1]) &&
            $a[1] < count($this->objListItemArray)
        ) {    // just in case
            $objFoundItem = $this->objListItemArray[$a[1]];
        }
        if (isset($a[2])) { // a recursive list
            $objFoundItem = $objFoundItem->findItem($a[1] . '_' . $a[2]);
        }

        return $objFoundItem;
    }

    /**
     * Finds an item in the list that matches the specified value.
     *
     * @param mixed $strValue The value to search for in the list of items.
     * @return null|ListItemBase The item that matches the specified value, or null if not found.
     */
    public function findItemByValue(mixed $strValue): ?ListItemBase
    {
        if (!$this->objListItemArray) {
            return null;
        }

        foreach ($this->objListItemArray as $objItem) {
            if ($objItem->Value == $strValue) {
                return $objItem;
            }
        }
        return null;
    }
}
