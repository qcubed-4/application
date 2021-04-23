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
 * GetId()    - returns the id
 * MarkAsModified() - marks the object as modified. Optional.
 *
 * @was QListItemManager
 * @package QCubed\Control
 */
trait ListItemManagerTrait
{
    ///////////////////////////
    // Private Member Variables
    ///////////////////////////
    /** @var ListItemBase[] an array of subitems if this is a recursive item. */
    protected $objListItemArray;

    /**
     * Add a base list item to the list.
     *
     * @param ListItemBase $objListItem
     */
    public function addListItem(ListItemBase $objListItem)
    {
        if ($strControlId = $this->getId()) {
            $num = 0;
            if ($this->objListItemArray) {
                $num = count($this->objListItemArray);
            }
            $objListItem->setId($strControlId . '_' . $num);    // auto assign the id based on parent id
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
     * @throws InvalidCast
     */
    public function addItemAt($intIndex, ListItemBase $objListItem)
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
     * Reindex the ids of the items based on the current item. We manage all the ids in the list internally
     * to be able to get to an item in the list quickly, and to make sure the ids are unique.
     */
    public function reindex()
    {
        if ($this->getId() && $this->objListItemArray) {
            for ($i = 0; $i < $this->getItemCount(); $i++) {
                $this->objListItemArray[$i]->setId($this->getId() . '_' . $i);    // assign the id based on parent id
                $this->objListItemArray[$i]->reindex();
            }
        }
    }

    /**
     * Stub function. The including function needs to implement this.
     */
    abstract public function markAsModified();

    /**
     * Returns the id of the item, however the item stores it.
     * @return string
     */
    abstract public function getId();

    /**
     * Adds an array of items,
     *
     * @param ListItemBase[]|array $objListItemArray Array of ListItems or key=>val pairs.
     * @throws InvalidCast
     * @throws Caller
     */
    public function addListItems(array $objListItemArray)
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
     * @throws IndexOutOfRange
     * @throws InvalidCast
     * @return ListItemBase
     */
    public function getItem($intIndex)
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
     * This will return an array of ALL the QListItems associated with this QListControl.
     * Please note that while each individual item can be altered, altering the array, itself,
     * will not affect any change on the QListControl.  So existing QListItems may be modified,
     * but to add / remove items from the QListControl, you should use AddItem() and RemoveItem().
     * @return ListItemBase[]
     */
    public function getAllItems()
    {
        return $this->objListItemArray;
    }

    /**
     * Removes all the items in objListItemArray
     */
    public function removeAllItems()
    {
        $this->markAsModified();
        $this->objListItemArray = null;
    }

    /**
     * Removes a ListItem at the specified index location
     *
     * @param integer $intIndex
     *
     * @throws IndexOutOfRange
     * @throws InvalidCast
     */
    public function removeItem($intIndex)
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
     * @throws InvalidCast
     */
    public function replaceItem($intIndex, ListItem $objListItem)
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
    public function getItemCount()
    {
        $count = 0;
        if ($this->objListItemArray) {
            $count = count($this->objListItemArray);
        }
        return $count;
    }

    /**
     * Finds the item by id recursively. Makes use of the fact that we maintain the ids in order to efficiently
     * find the item.
     *
     * @param string $strId If this is a sub-item, it will be an id fragment
     * @return null|ListItemBase
     */
    public function findItem($strId)
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
     * Returns the first tiem found with the given value.
     *
     * @param $strValue
     * @return null|ListItemBase
     */
    public function findItemByValue($strValue)
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
