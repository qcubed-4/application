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
use QCubed\Type;

/**
 * Class DataGridCheckboxColumn
 *
 * A checkbox column that specifically is for inclusion in a QDataGrid object. The two work together to hand off
 * important events and functionality.
 *
 * The default functionality of this class shows the name of the column in the header and uses the primary key of
 * a database object as the checkbox ID. It also
 * stores the information on which boxes are checked in the session variable so that they can be easily recalled and do
 * not clutter the form state. You can override additional functions below if you would like to store the checkbox state
 * with the items themselves, or somewhere else.
 *
 * If you turn on the CheckAll box in the header, you must subclass this column, and at a minimum implement the GetAllIds() function
 * so that it knows the full set of IDs to record as checked.
 *
 * This column keeps track of what is checked and not checked in real time using ajax rather than using POST methods.
 * The primary reason is that what is visible in the table will generally not be the complete set of data available from
 * the database if the datagrid is using a paginator.
 *
 * @package QCubed\Table
 */
class DataGridCheckboxColumn extends CheckboxColumn
{
    /** @var  bool Record the state of the AllChecked checkbox in the header. */
    protected ?bool $blnAllChecked = false;
    protected ?bool $blnShowCheckAll = false; // Default to false so that we have default functionality that does not require subclassing.

    /**
     * Return the array of item IDs that are checked. Default stores the IDs in the session. Override if you are storing
     * them elsewhere.
     *
     * @return array
     */
    public function getCheckedItemIds(): array
    {
        $strFormId = $this->ParentTable->Form->FormId;
        $strTableId = $this->ParentTable->ControlId;

        if (!empty($_SESSION['checkedItems'][$strFormId][$strTableId][$this->strId])) {
            return array_keys($_SESSION['checkedItems'][$strFormId][$strTableId][$this->strId]);
        } else {
            return array();
        }
    }

    /**
     * Clear all the checked items. Default stores the IDs in the session. Override if you are storing
     * them elsewhere.
     */
    public function clearCheckedItems(): void
    {
        if ($this->ParentTable) {
            $strFormId = $this->ParentTable->Form->FormId;
            $strTableId = $this->ParentTable->ControlId;
            unset($_SESSION['checkedItems'][$strFormId][$strTableId][$this->strId]);
        }
    }

    /**
     * A checkbox in the column was clicked. The parent datagrid routes checkbox clicks to this function. Not for
     * general consumption.
     *
     * We are detecting every click, rather than using ParsePostData, because in a multi-user environment,
     * post-data can be stale. Also, post-data only tells us what is turned on, not what is turned off, and
     * so we would need to query the database to see what is missing from the post-data to know what should be
     * turned off. This could be tricky, as the list of visible items might have changed.
     *
     * Known issue: If you do the following things, the check all box is not checked:
     *    - Click check all, then uncheck one item, then recheck that item. To fix would require querying the entire
     *    list every time one item is checked. Not important enough to slow things down like that.
     * - Click check all, then refresh the page. This is actually by design, because in a multi-user environment,
     *   if you refresh the page, you may get new items in the list which the previous check-all click would not have
     *   checked.
     *
     * @param array $strParameter
     * @throws Caller
     * @throws InvalidCast
     */
    public function click(array $strParameter): void
    {
        $blnChecked = Type::cast($strParameter['checked'], Type::BOOLEAN);

        $idItems = explode('_', $strParameter['id']);
        $strItemId = end($idItems);

        if ($strItemId == 'all') {
            $this->checkAll($blnChecked);
            $this->blnAllChecked = $blnChecked;
        } else {
            $this->setItemCheckedState($strItemId, $blnChecked);
            if (!$blnChecked) {
                $this->blnAllChecked = false;
            }
            // Since we are in a datagrid, we would have to query all the data to know whether checking one item
            // leaves the control in a state where all the items are checked. This is a lot of work to save the
            // user one extra click.
        }
    }



    /** QHtmlTableCheckboxColumn Overrides */

    /**
     * The overrides below implement the necessary functionality for the QHtmlTableCheckBoxColumn superclass.
     * You shouldn't need to change them. They eventually call into GetItemId, which you will need to override
     * to return the item ID of the given line item.
     */

    /**
     * Return the ID attribute of the checkbox tag. Must be unique to the form. This will use the column ID, which
     * should be unique, and add the item ID to the end to generate the object ID.
     *
     * @param mixed|null $item
     * @return string
     */
    protected function getCheckboxId(mixed $item): string
    {
        return $this->Id . '_' . $this->_GetItemId($item); // id here must be unique to the form
    }

    /**
     * Return the value attribute of the checkbox tag. Values are required in HTML for checkboxes.
     *
     * @param mixed|null $item
     * @return string
     */
    protected function getCheckboxValue($item): string
    {
        return '';    // Since we are not using post to submit information, we don't need a value.
    }

    /**
     * Return true to draw the checkbox corresponding to this item as checked, and false for unchecked.
     * @param mixed|null $item
     * @return bool
     */
    protected function isChecked(mixed $item): bool
    {
        if (is_null($item)) {
            return $this->blnAllChecked;
        } else {
            return $this->getItemCheckedState($item);
        }
    }

    /**
     * Gets the parameters for a checkbox, including custom data attributes and event handlers.
     *
     * @param mixed|null $item The item for which the checkbox parameters are generated.
     * @return array The array of parameters for the checkbox.
     */
    public function getCheckboxParams(mixed $item): array
    {
        $params = parent::getCheckboxParams($item);
        $params['data-col'] = $this->strId;

        if (!$item) {
            // the check all box
            // sets the currently visible checkboxes appropriately
            $params['onclick'] = sprintf('$j("#%s input:checkbox[data-col=%s]").prop("checked", this.checked)',
                $this->ParentTable->ControlId, $this->strId);
        }
        return $params;
    }

    /** End HtmlTableCheckboxColumn Overrides */

    /**
     * Returns the checked state of the item. Default stores the IDs in the session. Override if you are storing
     * them elsewhere.
     *
     * @param $item
     * @return bool
     */
    protected function getItemCheckedState($item): bool
    {
        $strFormId = $this->ParentTable->Form->FormId;
        $strTableId = $this->ParentTable->ControlId;
        $id = $this->getItemId($item);
        return !empty($_SESSION['checkedItems'][$strFormId][$strTableId][$this->strId][$id]);
    }


    /**
     * Saves the checked state of the item to be recalled later. Default stores the IDs in the session. Override if you are storing
     * them elsewhere.
     *
     * @param int|string $itemId The identifier of the item to update.
     * @param bool $blnChecked The checked state to set for the item. True for checked, false for unchecked.
     * @return void
     */
    public function setItemCheckedState(int|string $itemId, bool $blnChecked): void
    {
        $strFormId = $this->ParentTable->Form->FormId;
        $strTableId = $this->ParentTable->ControlId;
        if ($blnChecked) {
            $_SESSION['checkedItems'][$strFormId][$strTableId][$this->strId][$itemId] = true;
        } else {
            unset($_SESSION['checkedItems'][$strFormId][$strTableId][$this->strId][$itemId]);
        }
    }

    /**
     * Checks or unchecks all the items. This will check or uncheck each individual item. Override to use a mechanism
     * to check them all at once.
     *
     * @param $blnChecked True to check all, false to uncheck all
     * @throws InvalidCast
     */
    protected function checkAll(bool $blnChecked): void
    {
        $ids = $this->getAllIds();

        if ($ids === null) {
            throw new InvalidCast('You must create a subclass and implement GetAllIds() when showing the Check All box.');
        }

        if ($ids) {
            foreach ($ids as $id) {
                $this->setItemCheckedState($id, $blnChecked);
            }
        }
    }

    /**
     * An internal helper to eventually get the item ID.
     *
     * @param mixed|null $item
     * @return string|null
     */
    private function _GetItemId(mixed $item): ?string
    {
        if ($item) {
            return $this->getItemId($item);
        } else {
            return 'all';
        }
    }

    /**
     * Override this to return an array of all the IDs of the objects in the table, including IDs that are not
     * currently visible on the page being shown. If you create your own CheckAll function, or if you are not showing
     * the CheckAll box in the header, you do not need to implement this.
     *
     * If you want to return an empty set, return an empty array.
     *
     * @return array|null
     */
    protected function getAllIds(): ?array
    {
        return null;
    }

    /**
     * Returns the unique ID of the given item. This ID will be used to generate the ID in the tag
     * of the checkbox but will not directly correspond to the ID. The given item ID only needs to be unique within your
     * list of items.
     *
     * The default will assume this is a database object and use the primary key as the ID. Override if you want something else.
     *
     * @param mixed $item
     * @return string|null
     */
    protected function getItemId(mixed $item): ?string
    {
        if (is_object($item)) {
            return $item->primaryKey();
        }
        return null;
    }
}