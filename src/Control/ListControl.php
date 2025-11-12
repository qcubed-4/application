<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Control;

    use QCubed\Project\Control\ControlBase;
    use QCubed\Exception\Caller;
    use QCubed\Exception\IndexOutOfRange;
    use QCubed\Exception\InvalidCast;
    use QCubed\ModelConnector\Param as QModelConnectorParam;
    use QCubed\Type;

    /**
     * Class ListControl
     *
     * Abstract object which is extended by anything that involves lists of selectable items.
     * This object is the foundation for the ListBox, CheckBoxList, RadioButtonList
     * and TreeNav. Subclasses can be used as objects to specify one-to-many and many-to-many relationships.
     *
     * @property-read integer $ItemCount      the current count of ListItems in the control.
     * @property integer $SelectedIndex  is the index number of the control that is selected. "-1" means that nothing is selected. If multiple items are selected, it will return the lowest index number of all ListItems that are currently selected. Set functionality: select that specific ListItem and will unselect all other currently selected ListItems.
     * @property-read array $SelectedIndexes  An array if index numbers corresponding to the selecting in a multi-select situation.
     * @property string $SelectedName   simply returns ListControl::SelectedItem->Name, or null if nothing is selected.
     * @property-read ListItem $SelectedItem   (readonly!) returns the ListItem object, itself, that is selected (or the ListItem with the lowest index number of a ListItems that are currently selected if multiple items are selected). It will return null if nothing is selected.
     * @property-read array $SelectedItems  returns an array of selected ListItems (if any).
     * @property mixed $SelectedValue  simply returns ListControl::SelectedItem->Value, or null if nothing is selected.
     * @property array $SelectedNames  returns an array of all selected names
     * @property array $SelectedValues returns an array of all selected values
     * @property string $ItemStyle     {@link ListItemStyle}
     * @see     ListItemStyle
     * @package Controls
     * @package QCubed\Control
     */
    abstract class ListControl extends ControlBase
    {
        use ListItemManagerTrait;

        public const string REPEAT_HORIZONTAL = 'Horizontal';
        public const string REPEAT_VERTICAL = 'Vertical';


        /** @var null|ListItemStyle The common style for all elements in the list */
        protected ?ListItemStyle $objItemStyle = null;

        //////////
        // Methods
        //////////

        /**
         * Adds a new item to the list. The item can be specified as a `ListItem` instance
         * or created dynamically from the provided parameters.
         *
         * @param ListItem|string $mixListItemOrName The ListItem instance to add, or the name of the new item.
         * @param string|null $strValue Optional. The value associated with the item. Used only if creating a new ListItem.
         * @param bool|null $blnSelected Optional. Indicates whether the item should be marked as selected. Used only if creating a new ListItem.
         * @param bool|null $blnDisabled Optional. Indicates whether the item should be marked as disabled. Used only if creating a new ListItem.
         * @param string|null $strItemGroup Optional. The group name the item belongs to. Used only if creating a new ListItem.
         * @param string|null $mixOverrideParameters Optional. Additional override parameters for item creation. Used only if creating a new ListItem.
         *
         * @return void
         * @throws Caller
         */
        public function addItem(
            ListItem|string $mixListItemOrName,
            ?string         $strValue = null,
            ?bool           $blnSelected = null,
            ?bool           $blnDisabled = null,
            ?string         $strItemGroup = null,
            ?string         $mixOverrideParameters = null
        ): void
        {
            if ($mixListItemOrName instanceof ListItem) {
                $objListItem = $mixListItemOrName;
            } elseif ($mixOverrideParameters) {
                $objListItem = new ListItem(
                    $mixListItemOrName,
                    $strValue,
                    $blnSelected,
                    $blnDisabled,
                    $strItemGroup,
                    $mixOverrideParameters
                );
            } else {
                $objListItem = new ListItem(
                    $mixListItemOrName,
                    $strValue,
                    $blnSelected,
                    $blnDisabled,
                    $strItemGroup
                );
            }

            $this->addListItem($objListItem);
        }

        /**
         * Adds an array of items, or an array of key=>value pairs. Convenient for adding a list from a type table.
         * When passing key=>val pairs, mixSelectedValues can be an array or just a single value to compare against to indicate what is selected.
         *
         * @param array $mixItemArray Array of ListItems or key=>val pairs.
         * @param mixed|null $mixSelectedValues Array of selected values, or value of one selection
         * @param string|null $strItemGroup allows you to apply grouping (<optgroup> tag)
         * @param string|null $mixOverrideParameters OverrideParameters for ListItemStyle
         *
         * @throws InvalidCast
         * @throws Caller
         */
        public function addItems(
            array  $mixItemArray,
            mixed  $mixSelectedValues = null,
            ?string $strItemGroup = null,
            ?string $mixOverrideParameters = null
        ): void
        {
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
            }
            $this->reindex();
            $this->markAsModified();
        }

        /**
         * Return the ID. Used by ListItemManager trait.
         * @return string|null
         */
        public function getId(): ?string
        {
            return $this->strControlId;
        }

        /**
         * Recursively unselects all the items and subitems in the list.
         *
         * @param bool $blnRefresh True if we need to reflect the change in the HTML page. False if we are recording
         *   what the user has already done.
         * @throws IndexOutOfRange
         * @throws InvalidCast|Caller
         */
        public function unselectAllItems(bool $blnRefresh = true): void
        {
            $intCount = $this->getItemCount();
            for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
                $objItem = $this->getItem($intIndex);
                if ($objItem instanceof ListItem) {
                    $objItem->Selected = false;
                }
            }
            if ($blnRefresh && $this->blnOnPage) {
                $this->refreshSelection();
            }
        }

        /**
         * Set selected items by their IDs.
         *
         * This method marks items as selected based on the given array of IDs. If
         * the optional refresh parameter is true, the selection will be refreshed
         * based on the current state.
         *
         * @param string[] $strIdArray Array of item IDs to mark as selected.
         * @param bool $blnRefresh Optional. Whether to refresh the selection after setting the items. Default is true.
         * @return void
         * @throws IndexOutOfRange
         * @throws InvalidCast|Caller
         */
        public function setSelectedItemsById(array $strIdArray, bool $blnRefresh = true): void
        {
            $intCount = $this->getItemCount();
            for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
                $objItem = $this->getItem($intIndex);
                $strId = $objItem->getId();
                if ($objItem instanceof ListItem) {
                    $objItem->Selected = in_array($strId, $strIdArray);
                }
            }
            if ($blnRefresh && $this->blnOnPage) {
                $this->refreshSelection();
            }
        }

        /**
         * Set the selected item by an index. This can only set top-level items. Lower level items are untouched.
         * @param integer[] $intIndexArray
         * @param bool $blnRefresh
         * @throws IndexOutOfRange
         * @throws InvalidCast|Caller
         */
        public function setSelectedItemsByIndex(array $intIndexArray, bool $blnRefresh = true): void
        {
            $intCount = $this->getItemCount();
            for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
                $objItem = $this->getItem($intIndex);
                if ($objItem instanceof ListItem) {
                    $objItem->Selected = in_array($intIndex, $intIndexArray);
                }
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
         * @throws IndexOutOfRange
         * @throws InvalidCast|Caller
         */
        public function setSelectedItemsByValue(array $mixValueArray, bool $blnRefresh = true): void
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
                if ($objItem instanceof ListItem) {
                    $objItem->Selected = $blnSelected;
                }
            }
            if ($blnRefresh && $this->blnOnPage) {
                $this->refreshSelection();
            }
        }

        /**
         * Set the selected items by name.
         * @param string[] $strNameArray
         * @param bool $blnRefresh
         * @throws IndexOutOfRange
         * @throws InvalidCast|Caller
         */
        public function setSelectedItemsByName(array $strNameArray, bool $blnRefresh = true): void
        {
            $intCount = $this->getItemCount();
            for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
                $objItem = $this->getItem($intIndex);
                $strName = $objItem->Name;
                if ($objItem instanceof ListItem) {
                    $objItem->Selected = in_array($strName, $strNameArray);
                }
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
         * implement a way of just setting the selection through JavaScript.
         */
        protected function refreshSelection(): void
        {
            $this->markAsModified();
        }

        /**
         * Retrieves the first selected item from the list of items, if any.
         * Iterates through the items and returns the first item marked as selected.
         *
         * @return ListItem|null Returns the first selected ListItem if found, or null if no selected item exists.
         * @throws IndexOutOfRange
         * @throws InvalidCast|Caller
         */
        public function getFirstSelectedItem(): ?ListItem
        {
            $intCount = $this->getItemCount();
            for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
                $objItem = $this->getItem($intIndex);
                if ($objItem instanceof ListItem && $objItem->Selected) {
                    return $objItem;
                }
            }
            return null;
        }

        /**
         * Retrieve all selected items.
         *
         * @return ListItem[] An array containing all selected ListItem objects.
         * @throws InvalidCast|IndexOutOfRange|Caller
         */
        public function getSelectedItems(): array
        {
            $aResult = array();
            $intCount = $this->getItemCount();
            for ($intIndex = 0; $intIndex < $intCount; $intIndex++) {
                $objItem = $this->getItem($intIndex);
                if ($objItem instanceof ListItem && $objItem->Selected) {
                    $aResult[] = $objItem;
                }
            }
            return $aResult;
        }

        /**
         * Returns the current state of the control to be able to restore it later.
         */
        public function getState(): array
        {
            return array('SelectedValues' => $this->SelectedValues);
        }

        /**
         * Assigns the given state to the current object, updating properties if applicable.
         *
         * @param mixed $state The state data to be applied, which may include 'SelectedValues'.
         * @return void
         */
        public function putState(mixed $state): void
        {
            if (!empty($state['SelectedValues'])) {
                $this->SelectedValues = $state['SelectedValues'];
            }
        }

        /////////////////////////
        // Public Properties: GET
        /////////////////////////

        /**
         * Magic method to get the property value based on the property name.
         *
         * @param string $strName The property name to retrieve.
         *
         * @return mixed The value of the requested property. The return type varies depending on the property:
         *               - ItemCount: int
         *               - SelectedIndex: int
         *               - SelectedIndexes: array
         *               - SelectedName: null|string
         *               - SelectedValue: mixed
         *               - Value: mixed
         *               - SelectedItem: mixed
         *               - SelectedItems: array
         *               - SelectedNames: array
         *               - SelectedValues: array
         *               - ItemStyle: mixed
         *               - default: mixed (result of parent::__get or exception)
         * @throws Caller Thrown if the property is not found or an error occurs in parent::__get.
         * @throws \Exception
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case "ItemCount":
                    return $this->getItemCount();

                case "SelectedIndex":
                    for ($intIndex = 0; $intIndex < $this->getItemCount(); $intIndex++) {
                        $objItem = $this->getItem($intIndex);
                        if ($objItem instanceof ListItem && $objItem->Selected) {
                            return $intIndex;
                        }
                    }
                    return -1;

                case "SelectedIndexes":
                    $indexes = [];
                    for ($intIndex = 0; $intIndex < $this->getItemCount(); $intIndex++) {
                        $objItem = $this->getItem($intIndex);
                        if ($objItem instanceof ListItem && $objItem->Selected) {
                            $indexes[] = $intIndex;
                        }
                    }
                    return $indexes;

                case "SelectedName": // assumes the first selected item is the selection
                    if ($objItem = $this->getFirstSelectedItem()) {
                        return $objItem->Name;
                    }
                    return '';

                case "SelectedValue":
                case "Value":
                    if ($objItem = $this->getFirstSelectedItem()) {
                        return $objItem->Value;
                    }
                    return '';

                case "SelectedItem":
                    if ($objItem = $this->getFirstSelectedItem()) {
                        return $objItem;
                    } elseif ($this->getItemCount()) {
                        return $this->getItem(0);
                    }
                    return '';
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
         * Magic method to set a property value dynamically.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to set the property to.
         *
         * @return void
         * @throws InvalidCast If the value cannot be cast to the required type.
         * @throws IndexOutOfRange If the selected index is out of the valid range.
         * @throws Caller If the parent class cannot handle the property.
         * @throws \Exception
         */
        public function __set(string $strName, mixed $mixValue): void
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
            }
        }

        /**
         * Returns a description of the options available to modify by the designer for the code generator.
         *
         * @return QModelConnectorParam[]
         * @throws Caller
         */
        public static function getModelConnectorParams(): array
        {
            return array_merge(parent::getModelConnectorParams(), array(
                new QModelConnectorParam(QModelConnectorParam::GENERAL_CATEGORY, 'NoAutoLoad',
                    'Prevent automatically populating a list type control. Set this if you are doing more complex list loading.',
                    Type::BOOLEAN)
            ));
        }
    }
