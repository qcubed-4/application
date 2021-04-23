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
use QCubed\ModelConnector\Param as QModelConnectorParam;
use QCubed\Type;
use QCubed as Q;

/**
 * Class ListControl
 *
 * Abstract object which is extended by anything which involves lists of selectable items.
 * This object is the foundation for the ListBox, CheckBoxList, RadioButtonList
 * and TreeNav. Subclasses can be used as objects to specify one-to-many and many-to-many relationships.
 *
 * @property-read integer $ItemCount      the current count of ListItems in the control.
 * @property integer $SelectedIndex  is the index number of the control that is selected. "-1" means that nothing is selected. If multiple items are selected, it will return the lowest index number of all ListItems that are currently selected. Set functionality: selects that specific ListItem and will unselect all other currently selected ListItems.
 * @property-read array $SelectedIndexes  An array if index numbers corresponding to the selectiong in a multi-select situation.
 * @property string $SelectedName   simply returns ListControl::SelectedItem->Name, or null if nothing is selected.
 * @property-read ListItem $SelectedItem   (readonly!) returns the ListItem object, itself, that is selected (or the ListItem with the lowest index number of a ListItems that are currently selected if multiple items are selected). It will return null if nothing is selected.
 * @property-read array $SelectedItems  returns an array of selected ListItems (if any).
 * @property mixed $SelectedValue  simply returns ListControl::SelectedItem->Value, or null if nothing is selected.
 * @property array $SelectedNames  returns an array of all selected names
 * @property array $SelectedValues returns an array of all selected values
 * @property string $ItemStyle     {@link ListItemStyle}
 * @see     ListItemStyle
 * @package Controls
 * @was QListControl
 * @package QCubed\Control
 */
abstract class ListControl extends Q\Project\Control\ControlBase
{
    use ListItemManagerTrait;

    const REPEAT_HORIZONTAL = 'Horizontal';
    const REPEAT_VERTICAL = 'Vertical';


/** @var null|ListItemStyle The common style for all elements in the list */
    protected $objItemStyle = null;

    //////////
    // Methods
    //////////

    /**
     * Add an item to the list.
     *
     * @param ListItem|string $mixListItemOrName A full ListItem, in which case everything else is ignored, or the name of an item
     * @param null|string $strValue The value of the item
     * @param null|bool $blnSelected Is the item selected?
     * @param null|string $strItemGroup The name of the item group, if items should be grouped
     * @param null|array|string $mixOverrideParameters
     */
    public function addItem(
        $mixListItemOrName,
        $strValue = null,
        $blnSelected = null,
        $strItemGroup = null,
        $mixOverrideParameters = null
    ) {
        if (gettype($mixListItemOrName) == Type::OBJECT) {
            $objListItem = Type::cast($mixListItemOrName, '\\QCubed\\Control\\ListItem');
        } elseif ($mixOverrideParameters) {
            // The OverrideParameters can only be included if they are not null, because OverrideAttributes in \QCubed\Base can't accept a NULL Value
            $objListItem = new ListItem($mixListItemOrName, $strValue, $blnSelected, $strItemGroup,
                $mixOverrideParameters);
        } else {
            $objListItem = new ListItem($mixListItemOrName, $strValue, $blnSelected, $strItemGroup);
        }

        $this->addListItem($objListItem);
    }

    /**
     * Adds an array of items, or an array of key=>value pairs. Convenient for adding a list from a type table.
     * When passing key=>val pairs, mixSelectedValues can be an array, or just a single value to compare against to indicate what is selected.
     *
     * @param array $mixItemArray Array of QListItems or key=>val pairs.
     * @param mixed $mixSelectedValues Array of selected values, or value of one selection
     * @param string $strItemGroup allows you to apply grouping (<optgroup> tag)
     * @param string $mixOverrideParameters OverrideParameters for ListItemStyle
     *
     * @throws InvalidCast
     */
    public function addItems(
        array $mixItemArray,
        $mixSelectedValues = null,
        $strItemGroup = null,
        $mixOverrideParameters = null
    ) {
        try {
            $mixItemArray = Type::cast($mixItemArray, Type::ARRAY_TYPE);
        } catch (InvalidCast $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }

        foreach ($mixItemArray as $val => $item) {
            if ($val === '') {
                $val = null; // these are equivalent when specified as a key of an array
            }
            if ($mixSelectedValues && is_array($mixSelectedValues)) {
                $blnSelected = in_array($val, $mixSelectedValues);
            } else {
                $blnSelected = ($val === $mixSelectedValues);    // differentiate between null and 0 values
            }
            $this->addItem($item, $val, $blnSelected, $strItemGroup, $mixOverrideParameters);
        };
        $this->reindex();
        $this->markAsModified();
    }

    /**
     * Return the id. Used by QListItemManager trait.
     * @return string
     */
    public function getId()
    {
        return $this->strControlId;
    }

    /**
     * Recursively unselects all the items and subitems in the list.
     *
     * @param bool $blnRefresh True if we need to reflect the change in the html page. False if we are recording
     *   what the user has already done.
     */
    public function unselectAllItems($blnRefresh = true)
    {
        $intCount = $this->getItemCount();
        for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
            $objItem = $this->getItem($intIndex);
            $objItem->Selected = false;
        }
        if ($blnRefresh && $this->blnOnPage) {
            $this->refreshSelection();
        }
    }


    /**
     * Selects the given items by Id, and unselects items that are not in the list.
     * @param string[] $strIdArray
     * @param bool $blnRefresh
     */
    public function setSelectedItemsById(array $strIdArray, $blnRefresh = true)
    {
        $intCount = $this->getItemCount();
        for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
            $objItem = $this->getItem($intIndex);
            $strId = $objItem->getId();
            $objItem->Selected = in_array($strId, $strIdArray);
        }
        if ($blnRefresh && $this->blnOnPage) {
            $this->refreshSelection();
        }
    }

    /**
     * Set the selected item by index. This can only set top level items. Lower level items are untouched.
     * @param integer[] $intIndexArray
     * @param bool $blnRefresh
     */
    public function setSelectedItemsByIndex(array $intIndexArray, $blnRefresh = true)
    {
        $intCount = $this->getItemCount();
        for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
            $objItem = $this->getItem($intIndex);
            $objItem->Selected = in_array($intIndex, $intIndexArray);
        }
        if ($blnRefresh && $this->blnOnPage) {
            $this->refreshSelection();
        }
    }

    /**
     * Set the selected items by value. We equate nulls and empty strings, but must be careful not to equate
     * those with a zero.
     *
     * @param array $mixValueArray
     * @param bool $blnRefresh
     */
    public function setSelectedItemsByValue(array $mixValueArray, $blnRefresh = true)
    {
        $intCount = $this->getItemCount();

        for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
            $objItem = $this->getItem($intIndex);
            $mixCurVal = $objItem->Value;
            $blnSelected = false;
            foreach ($mixValueArray as $mixValue) {
                if (!$mixValue) {
                    if ($mixValue === null || $mixValue === '') {
                        if ($mixCurVal === null || $mixCurVal === '') {
                            $blnSelected = true;
                        }
                    } elseif (!$mixCurVal && !($mixCurVal === null || $mixCurVal === '')) {
                        $blnSelected = true;
                    }
                } elseif ($mixCurVal == $mixValue) {
                    $blnSelected = true;
                }
            }
            $objItem->Selected = $blnSelected;
        }
        if ($blnRefresh && $this->blnOnPage) {
            $this->refreshSelection();
        }
    }

    /**
     * Set the selected items by name.
     * @param string[] $strNameArray
     * @param bool $blnRefresh
     */
    public function setSelectedItemsByName(array $strNameArray, $blnRefresh = true)
    {
        $intCount = $this->getItemCount();
        for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
            $objItem = $this->getItem($intIndex);
            $strName = $objItem->Name;
            $objItem->Selected = in_array($strName, $strNameArray);
        }
        if ($blnRefresh && $this->blnOnPage) {
            $this->refreshSelection();
        }
    }


    /**
     * This method is called when a selection is changed. It should execute the code to refresh the selected state
     * of the items in the control.
     *
     * The default just redraws the control. Redrawing a large list control can take a lot of time, so subclasses should
     * implement a way of just setting the selection through javasacript.
     */
    protected function refreshSelection()
    {
        $this->markAsModified();
    }

    /**
     * Return the first item selected.
     *
     * @return null|ListItem
     * @throws InvalidCast
     */
    public function getFirstSelectedItem()
    {
        $intCount = $this->getItemCount();
        for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
            $objItem = $this->getItem($intIndex);
            if ($objItem->Selected) {
                return $objItem;
            }
        }
        return null;
    }

    /**
     * Return all the selected items.
     *
     * @return ListItem[]
     */
    public function getSelectedItems()
    {
        $aResult = array();
        $intCount = $this->getItemCount();
        for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
            $objItem = $this->getItem($intIndex);
            if ($objItem->Selected) {
                $aResult[] = $objItem;
            }
        }
        return $aResult;
    }

    /**
     * Returns the current state of the control to be able to restore it later.
     */
    public function getState()
    {
        return array('SelectedValues' => $this->SelectedValues);
    }

    /**
     * Restore the  state of the control.
     * @param array $state
     */
    public function putState($state)
    {
        if (!empty($state['SelectedValues'])) {
            $this->SelectedValues = $state['SelectedValues'];
        }
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * PHP __get magic method implementation
     * @param string $strName Property Name
     *
     * @return mixed
     * @throws Exception|Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case "ItemCount":
                return $this->getItemCount();

            case "SelectedIndex":
                for ($intIndex = 0; $intIndex < $this->getItemCount(); $intIndex++) {
                    if ($this->getItem($intIndex)->Selected) {
                        return $intIndex;
                    }
                }
                return -1;

            case "SelectedIndexes":
                $indexes = [];
                for ($intIndex = 0; $intIndex < $this->getItemCount(); $intIndex++) {
                    if ($this->getItem($intIndex)->Selected) {
                        $indexes[] = $intIndex;
                    }
                }
                return $indexes;

            case "SelectedName": // assumes first selected item is the selection
                if ($objItem = $this->getFirstSelectedItem()) {
                    return $objItem->Name;
                }
                return null;

            case "SelectedValue":
            case "Value":
                if ($objItem = $this->getFirstSelectedItem()) {
                    return $objItem->Value;
                }
                return null;

            case "SelectedItem":
                if ($objItem = $this->getFirstSelectedItem()) {
                    return $objItem;
                } elseif ($this->getItemCount()) {
                    return $this->getItem(0);
                }
                return null;
            case "SelectedItems":
                return $this->getSelectedItems();

            case "SelectedNames":
                $objItems = $this->getSelectedItems();
                $strNamesArray = array();
                foreach ($objItems as $objItem) {
                    $strNamesArray[] = $objItem->Name;
                }
                return $strNamesArray;

            case "SelectedValues":
                $objItems = $this->getSelectedItems();
                $values = array();
                foreach ($objItems as $objItem) {
                    $values[] = $objItem->Value;
                }
                return $values;

            case "ItemStyle":
                return $this->objItemStyle;

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
     * PHP __set magic method implementation
     *
     * @param string $strName Property Name
     * @param string $mixValue Propety Value
     *
     * @return void
     * @throws IndexOutOfRange|\Exception|Caller|InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "SelectedIndex":
                try {
                    $mixValue = Type::cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

                $itemCount = $this->getItemCount();
                if (($mixValue < -1) ||    // special case to unselect all
                    ($mixValue > ($itemCount - 1))
                ) {
                    throw new IndexOutOfRange($mixValue, "SelectedIndex");
                }

                $this->setSelectedItemsByIndex(array($mixValue));
                break;

            case "SelectedName":
                $this->setSelectedItemsByName(array($mixValue));
                break;

            case "SelectedValue":
            case "Value": // most common situation
                $this->setSelectedItemsByValue(array($mixValue));
                break;

            case "SelectedNames":
                try {
                    $mixValue = Type::cast($mixValue, Type::ARRAY_TYPE);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                $this->setSelectedItemsByName($mixValue);
                break;

            case "SelectedValues":
                try {
                    $mixValue = Type::cast($mixValue, Type::ARRAY_TYPE);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                $this->setSelectedItemsByValue($mixValue);
                break;

            case "ItemStyle":
                try {
                    $this->blnModified = true;
                    $this->objItemStyle = Type::cast($mixValue, "\\QCubed\\Control\\ListItemStyle");
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;


            default:
                try {
                    parent::__set($strName, $mixValue);
                    break;
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }

    /**
     * Returns a description of the options available to modify by the designer for the code generator.
     *
     * @return QModelConnectorParam[]
     */
    public static function getModelConnectorParams()
    {
        return array_merge(parent::getModelConnectorParams(), array(
            new QModelConnectorParam(QModelConnectorParam::GENERAL_CATEGORY, 'NoAutoLoad',
                'Prevent automatically populating a list type control. Set this if you are doing more complex list loading.',
                Type::BOOLEAN)
        ));
    }
}
