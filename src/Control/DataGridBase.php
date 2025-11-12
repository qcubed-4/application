<?php
    /**
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Control;

    use QCubed as Q;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Application;
    use QCubed\Table\ColumnBase;
    use QCubed\Table\DataColumn;
    use QCubed\Table\DataGridCheckboxColumn;
    use QCubed\Type;
    use Throwable;


    if (!defined('QCUBED_FONT_AWESOME_CSS')) {
        define('QCUBED_FONT_AWESOME_CSS', 'https://opensource.keycdn.com/fontawesome/4.6.3/font-awesome.min.css');
    }

    /**
     * A base class for creating data grids within the QCubed framework.
     *
     * This class is designed to handle common functionality for data grids, such as sorting, pagination,
     * column tracking, and checkbox interactions. It acts as the foundation for building customizable
     * and interactive data grids that operate seamlessly within the framework.
     *
     * Features include:
     * - Sorting columns in ascending or descending order.
     * - Integrating paginator within the grid.
     * - Managing and tracking column states and interactions, including checkbox selections.
     * - Providing hooks for customization, like overriding header rows or adding actions.
     *
     * This class extends the functionality of TableBase, inheriting its table structure and capabilities
     * while adding data-grid-specific behavior.
     *
     * @property  string $SortColumnId The Id of the currently sorted column. Does not change if columns are re-ordered.
     * @property  int $SortColumnIndex The index of the currently sorted column.
     * @property  int $SortDirection SortAscending or SortDescending.
     * @property  array $SortInfo An array containing the sort data, so you can save and restore it later if needed.
     * @method getColumnById(string $strId)
     */
    class DataGridBase extends TableBase
    {
        /** Numbers that can be used to multiply against the results of comparison functions to reverse the order. */
        public const int SORT_ASCENDING = 1;
        public const int SORT_DESCENDING = -1;

        /** @var int Cuter to generate column Ids for columns that do not have them. */
        protected int $intLastColumnId = 0;

        /** @var  string Keeps track of the current sort column. We do it by Id so that the table can add/hide/show or rearrange columns and maintain the sort column. */
        protected string $strSortColumnId = '';

        /** @var int The direction of the currently sorted column. */
        protected int $intSortDirection = self::SORT_ASCENDING;

        /** @var string Default class */
        protected string $strCssClass = 'datagrid';
        /**
         * @var array|bool|int|mixed|string
         */
        protected mixed $SortColumnId;

        /**
         * Retrieves the last column ID as an integer.
         * @return int Returns the last column ID.
         */
        public function getIntLastColumnId(): int
        {
            return $this->intLastColumnId;
        }

        /**
         * Sets the value of the last column ID.
         *
         * @param int $intLastColumnId The ID of the last column to be set.
         * @return void
         */
        public function setIntLastColumnId(int $intLastColumnId): void
        {
            $this->intLastColumnId = $intLastColumnId;
        }

        /**
         * Constructor for initializing the object with a parent object and an optional control ID.
         * It loads necessary CSS files, initializes actions, and handles exceptions during construction.
         *
         * @param FormBase|ControlBase $objParentObject The parent object to which the control belongs.
         * @param string|null $strControlId An optional ID for the control. If not provided, a default ID will be generated.
         * @return void
         * @throws Caller If there is an issue during the construction process.
         */
        public function __construct(FormBase|ControlBase $objParentObject, ?string $strControlId = null)
        {
            try {
                parent::__construct($objParentObject, $strControlId);

                $this->addCssFile(QCUBED_FONT_AWESOME_CSS);

                $this->addActions();
            } catch (Caller  $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }

            print $this->strSortColumnId;
        }

        /**
         * Renders and returns the caption content.
         *
         * @return string The result of the caption rendering process.
         * @throws Caller
         */
        protected function renderCaption(): string
        {
            return $this->renderPaginator();
        }

        /**
         * Renders and returns the paginator content within a caption element.
         *
         * @return string The resulting HTML of the paginator rendering process or an empty string if the paginator is not set.
         * @throws Caller
         */
        protected function renderPaginator(): string
        {
            $objPaginator = $this->objPaginator;
            if (!$objPaginator) {
                return '';
            }

            $strHtml = $objPaginator->render(false);
            $strHtml = Q\Html::renderTag('span', ['class' => 'paginator-control'], $strHtml);
            if ($this->strCaption) {
                $strHtml = '<span>' . Q\QString::htmlEntities($this->strCaption) . '</span>' . $strHtml;
            }

            return Q\Html::renderTag('caption', null, $strHtml);
        }

        /**
         * Registers actions for handling checkbox column clicks and data grid sorting events.
         *
         * The method adds specific event-action pairs to the component to
         * handle checkbox clicks and sorting interactions. It also
         * prevents event propagation where necessary.
         *
         * @return void
         * @throws Caller
         */
        public function addActions(): void
        {
            $this->addAction(new Q\Event\CheckboxColumnClick(), new Q\Action\AjaxControl($this, 'checkClick'));
            $this->addAction(new Q\Event\CheckboxColumnClick(),
                new Q\Action\StopPropagation()); // prevent check click from bubbling as a row clicks.

            $this->addAction(new Q\Event\DataGridSort(), new Q\Action\AjaxControl($this, 'sortClick'));
            $this->addAction(new Q\Event\DataGridSort(), new Q\Action\StopPropagation());
        }

        /**
         * Adds a column at the specified index, assigning an ID to the column if none exists.
         *
         * @param int $intColumnIndex The index at which the column will be added.
         * @param ColumnBase $objColumn The column objects to be added.
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function addColumnAt(int $intColumnIndex, ColumnBase $objColumn): void
        {
            parent::addColumnAt($intColumnIndex, $objColumn);
            if (!$objColumn->Id) {
                $objColumn->Id = $this->ControlId . '_col_' . $this->intLastColumnId++;
            }
        }

        /**
         * Handles a click event for a specific column in the data grid.
         *
         * @param string $strFormId The ID of the form where the event originated.
         * @param string $strControlId The ID of the control where the event occurred.
         * @param mixed $strParameter Parameters associated with the click event, including column data.
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function checkClick(string $strFormId, string $strControlId, mixed $strParameter): void
        {
            $intColumnIndex = $strParameter['col'];
            $objColumn = $this->getColumn($intColumnIndex, true);

            if ($objColumn instanceof DataGridCheckboxColumn) {
                $objColumn->click($strParameter);
            }
        }

        /**
         * Clears the checked items for the specified column or all checkbox columns if no ID is provided.
         *
         * @param string|null $strColId The ID of the checkbox column to clear. If null, all checkbox columns will be cleared.
         * @return void
         */
        public function clearCheckedItems(?string $strColId = null): void
        {
            foreach ($this->objColumnArray as $objColumn) {
                if ($objColumn instanceof DataGridCheckboxColumn) {
                    if (is_null($strColId) || $objColumn->Id === $strColId) {
                        $objColumn->clearCheckedItems();
                    }
                }
            }
        }

        /**
         * Retrieves the IDs of the checked items for a specified checkbox column, or all checked item IDs
         * if no specific column ID is provided.
         *
         * @param string|null $strColId The optional ID of the checkbox column to retrieve checked item IDs from.
         *                              If null, it attempts to retrieve checked item IDs from any checkbox column.
         * @return array|null The array of checked item IDs if found; otherwise, null if the column is not found.
         */
        public function getCheckedItemIds(?string $strColId = null): ?array
        {
            foreach ($this->objColumnArray as $objColumn) {
                if ($objColumn instanceof DataGridCheckboxColumn) {
                    if (is_null($strColId) ||
                        $objColumn->Id === $strColId
                    ) {
                        return $objColumn->getCheckedItemIds();
                    }
                }
            }
            return null; // column not found
        }

        /**
         * Handles the click event for sorting a column in a grid or table. This method determines the column to sort by
         * based on the provided parameter, updates the sort direction, and resets pagination if applicable.
         *
         * @param string $strFormId The ID of the form in which the control triggering the event is located.
         * @param string $strControlId The ID of the control that triggered the event.
         * @param mixed $mixParameter The parameter indicating the column to sort by, typically its index.
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function sortClick(string $strFormId, string $strControlId, mixed $mixParameter): void
        {
            $intColumnIndex = Type::cast($mixParameter, Type::INTEGER);
            $objColumn = $this->getColumn($intColumnIndex, true);

            if (!$objColumn) {
                return;
            }
            assert($objColumn instanceof DataColumn);

            $this->blnModified = true;

            $strId = $objColumn->Id;

            // Reset pagination (if applicable)
            if ($this->objPaginator) {
                $this->PageNumber = 1;
            }

            // Make sure the Column is Sortable
            if ($objColumn->OrderByClause) {
                // It is

                // Are we currently sorting by this column?
                if ($this->strSortColumnId === $strId) {
                    // Yes, we are currently sorting by this column.

                    // In Reverse?
                    if ($this->intSortDirection == self::SORT_DESCENDING) {
                        // Yep -- reverse the sort
                        $this->intSortDirection = self::SORT_ASCENDING;
                    } else {
                        // Nope -- can we reverse?
                        if ($objColumn->ReverseOrderByClause) {
                            $this->intSortDirection = self::SORT_DESCENDING;
                        }
                    }
                } else {
                    // Nope -- so let's set it to this column
                    $this->strSortColumnId = $strId;
                    $this->intSortDirection = self::SORT_ASCENDING;
                }
            } else {
                // It isn't -- clear all-sort properties
                $this->intSortDirection = self::SORT_ASCENDING;
                $this->strSortColumnId = null;
            }
        }

        /**
         * Generates and returns the HTML for the table header rows.
         *
         * @return string The rendered HTML string for the header rows.
         */
        protected function getHeaderRowHtml(): string
        {
            $strToReturn = '';
            for ($i = 0; $i < $this->intHeaderRowCount; $i++) {
                $this->intCurrentHeaderRowIndex = $i;

                $strCells = '';
                if ($this->objColumnArray) {
                    foreach ($this->objColumnArray as $objColumn) {
                        assert ($objColumn instanceof DataColumn);
                        if ($objColumn->Visible) {
                            $strCellValue = $this->getHeaderCellContent($objColumn);
                            $aParams = $objColumn->getHeaderCellParams();
                            $aParams['id'] = $objColumn->Id;
                            if ($objColumn->OrderByClause) {
                                if (isset($aParams['class'])) {
                                    $aParams['class'] .= ' ' . 'sortable';
                                } else {
                                    $aParams['class'] = 'sortable';
                                }
                            }
                            $strCells .= Q\Html::renderTag('th', $aParams, $strCellValue);
                        }
                    }
                }
                $strToReturn .= Q\Html::renderTag('tr', $this->getHeaderRowParams(), $strCells);
            }

            return $strToReturn;
        }

        /**
         * Generates and returns the content for a header cell in a data column.
         *
         * @param DataColumn $objColumn The data column object containing the configuration and values for the header cell.
         * @return string The rendered content of the header cell, including any applicable sorting indicators or spans for positioning.
         */
        protected function getHeaderCellContent(DataColumn $objColumn): string
        {
            $blnSortable = false;
            $strCellValue = $objColumn->fetchHeaderCellValue();
            if ($objColumn->HtmlEntities) {
                $strCellValue = Q\QString::htmlEntities($strCellValue);
            }
            $strCellValue = Q\Html::renderTag('span', null, $strCellValue);    // wrap in a span for positioning

            if ($this->strSortColumnId == $objColumn->Id) {
                if ($this->intSortDirection == self::SORT_ASCENDING) {
                    $strCellValue = $strCellValue . ' ' . Q\Html::renderTag('i', ['class' => 'fa fa-sort-desc fa-lg']);
                } else {
                    $strCellValue = $strCellValue . ' ' . Q\Html::renderTag('i', ['class' => 'fa fa-sort-asc fa-lg']);
                }
                $blnSortable = true;
            } else {
                if ($objColumn->OrderByClause) {    // sortable, but not currently being sorted
                    $strCellValue = $strCellValue . ' ' . Q\Html::renderTag('i',
                            ['class' => 'fa fa-sort fa-lg']);
                    $blnSortable = true;
                }
            }

            if ($blnSortable) {
                //Wrap the header cell in an HTML5 block link to help with assistive technologies.
                $strCellValue = Q\Html::renderTag('div', null, $strCellValue);
                $strCellValue = Q\Html::renderTag('a', ['href' => 'javascript:;'],
                    $strCellValue); // this action will be handled by qcubed.js click handler in qcubed.datagrid2()
            }

            return $strCellValue;
        }

        /**
         * Creates and initializes the jQuery widget for the control.
         *
         * @return void
         */
        protected function makeJqWidget(): void
        {
            parent::makeJqWidget();
            Application::executeJsFunction('qcubed.datagrid2', $this->ControlId);
        }

        /**
         * Retrieves and returns the current state of the object, including
         * sorting and pagination data, if applicable.
         *
         * @return array|null An associative array containing the state data,
         *               including sort column ID, sort direction, and page number.
         */
        public function getState(): ?array
        {
            $state = array();
            if ($this->strSortColumnId) {
                $state["c"] = $this->strSortColumnId;
                $state["d"] = $this->intSortDirection;
            }
            if ($this->Paginator || $this->PaginatorAlternate) {
                $state["p"] = $this->PageNumber;
            }
            return $state;
        }

        /**
         * Updates the state of the object using the provided state array.
         *
         * @param mixed $state An associative array containing state information.
         *                     Valid keys:
         *                     - 'c': The column key to set as the sorting column.
         *                     - 'd': The sorting direction, either ascending or descending.
         *                     - 'p': The page number to set if a paginator is available.
         *
         * @return void
         */
        public function putState(mixed $state): void
        {
            // use the name as the column key because columns might be added or removed for some reason
            if (isset($state["c"])) {
                $this->strSortColumnId = $state["c"];
            }
            if (isset($state["d"])) {
                $this->intSortDirection = $state["d"];
                if ($this->intSortDirection != self::SORT_DESCENDING) {
                    $this->intSortDirection = self::SORT_ASCENDING;    // make sure it's only one of two values
                }
            }
            if (isset($state["p"]) &&
                ($this->Paginator || $this->PaginatorAlternate)
            ) {
                $this->PageNumber = $state["p"];
            }
        }

        /**
         * Retrieves the index of the column currently set for sorting.
         *
         * @return int|false The index of the sort column if found, or false if no match is found.
         */
        public function getSortColumnIndex(): false|int
        {
            if ($this->objColumnArray && ($count = count($this->objColumnArray))) {
                for ($i = 0; $i < $count; $i++) {
                    if ($this->objColumnArray[$i]->Id == $this->SortColumnId) {
                        return $i;
                    }
                }
            }
            return false;
        }

        /**
         * Retrieves the order by information based on the current sort column and sort direction.
         *
         * @return mixed The order by clause if available; otherwise, null.
         */
        public function getOrderByInfo(): mixed
        {
            if ($this->strSortColumnId) {
                $objColumn = $this->getColumnById($this->strSortColumnId);
                assert($objColumn instanceof DataColumn);
                if ($objColumn->OrderByClause) {
                    if ($this->intSortDirection == self::SORT_ASCENDING) {
                        return $objColumn->OrderByClause;
                    } else {
                        if ($objColumn->ReverseOrderByClause) {
                            return $objColumn->ReverseOrderByClause;
                        } else {
                            return $objColumn->OrderByClause;
                        }
                    }
                } else {
                    return null;
                }
            } else {
                return null;
            }
        }

        /**
         * Magic method to retrieve the value of a property dynamically.
         * Handles specific property names and provides custom behavior for each.
         *
         * @param string $strName The name of the property to retrieve.
         * @return mixed The value of the requested property or the parent::__get result.
         * @throws Caller If the property does not exist or cannot be retrieved.
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                // MISC
                case "OrderByClause":
                    return $this->getOrderByInfo();

                case "SortColumnId":
                    return $this->strSortColumnId;
                case "SortDirection":
                    return $this->intSortDirection;

                case "SortColumnIndex":
                    return $this->getSortColumnIndex();

                case "SortInfo":
                    return ['id' => $this->strSortColumnId, 'dir' => $this->intSortDirection];

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
         * @param string $strName
         * @param mixed $mixValue
         * @return void
         * @throws Caller
         * @throws Throwable Exception
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case "SortColumnId":
                    try {
                        $this->strSortColumnId = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "SortColumnIndex":
                    try {
                        $intIndex = Type::cast($mixValue, Type::INTEGER);
                        if ($intIndex < 0) {
                            $intIndex = 0;
                        }
                        if ($intIndex < count($this->objColumnArray)) {
                            $objColumn = $this->objColumnArray[$intIndex];
                        } elseif (count($this->objColumnArray) > 0) {
                            $objColumn = end($this->objColumnArray);
                        } else {
                            // no columns
                            $objColumn = null;
                        }
                        if ($objColumn && $objColumn->OrderByClause) {
                            $this->strSortColumnId = $objColumn->Id;
                        }
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;

                case "SortDirection":
                    try {
                        $this->intSortDirection = Type::cast($mixValue, Type::INTEGER);
                        if ($this->intSortDirection != self::SORT_DESCENDING) {
                            $this->intSortDirection = self::SORT_ASCENDING;    // make sure it's only one of two values
                        }
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "SortInfo":    // Restore the SortInfo obtained from the getter
                    try {
                        if (is_array($mixValue) && isset($mixValue['id']) && isset($mixValue['dir'])) {
                            $this->intSortDirection = Type::cast($mixValue['dir'], Type::INTEGER);
                            $this->strSortColumnId = Type::cast($mixValue['id'], Type::STRING);
                        } else {
                            // We add logic here to handle invalid format or default values
                            throw new Caller("Invalid mixValue format: an expected array with 'id' and 'dir'.");
                        }
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
