<?php
    /**
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Control;

    require_once(dirname(__DIR__, 2) . '/i18n/i18n-lib.inc.php');


    use QCubed\Exception\Caller;
    use QCubed\Exception\IndexOutOfRange;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Application;
    use QCubed\ApplicationBase;
    use QCubed\QString;
    use QCubed\TagStyler;
    use QCubed as Q;
    use QCubed\Type;
    use QCubed\Html;
    use QCubed\ModelConnector\Param as QModelConnectorParam;

    /**
     * Class CheckboxList
     *
     * This class will render a List of HTML Checkboxes (inherited from ListControl).
     * By definition, checkbox lists are multiple-select ListControls.
     *
     * So assuming you have a list of 10 items, and you have RepeatColumn set to 3:
     *    RepeatDirection::Horizontal would render as:
     *    1 2 3
     *    4 5 6
     *    7 8 9
     *    10
     *
     *    RepeatDirection::Vertical would render as:
     *    1 5 8
     *    2 6 9
     *    3 7 10
     *    4
     *
     * @package Controls
     *
     * @property string $Text is used to display text that is displayed next to the checkbox.  The text is rendered as an HTML "Label For" the checkbox.
     * @property string $TextAlign specifies the horizontal alignment of the text.  Valid values are "left", "right", and "center".
     * @property ListItemStyle $ItemStyle specifies the style of the individual list items.  This is used to specify the CSS class and/or inline style.
     * @property integer $ButtonMode specifies how the list should be rendered.  Valid values are:
     * @property integer $CellPadding specified the HTML Table's CellPadding
     * @property integer $CellSpacing specified the HTML Table's CellSpacing
     * @property integer $RepeatColumns specifies how many columns should be rendered in the HTML Table
     * @property string $RepeatDirection specifies which direction the list should go first...
     * @property boolean $HtmlEntities
     * @package QCubed\Control
     */
    class CheckboxList extends ListControl
    {
        const int BUTTON_MODE_NONE = 0;    // Uses the RepeatColumns and RepeatDirection settings to make a structure
        const int BUTTON_MODE_JQ = 1;        // a list of individual jquery ui buttons
        const int BUTTON_MODE_SET = 2;    // a jqueryui button set
        const int BUTTON_MODE_LIST = 3;    // just a vanilla list of checkboxes with no row or column styling

        ///////////////////////////
        // Private Member Variables
        ///////////////////////////

        // APPEARANCE
        protected string $strTextAlign = Q\Html::TEXT_ALIGN_RIGHT;

        // BEHAVIOR
        protected bool $blnHtmlEntities = true;

        // LAYOUT
        protected int $intCellPadding = -1;
        protected int $intCellSpacing = -1;
        protected int $intRepeatColumns = 1;
        protected string $strRepeatDirection = self::REPEAT_VERTICAL;
        protected ?int $intButtonMode = null;
        protected ?string $strMaxHeight = null; // will create a scroll pane if height is exceeded

        /**
         * Constructor for the class, initializing the control with the parent object and optional control ID.
         *
         * @param FormBase|ControlBase $objParentObject The parent object responsible for managing this control.
         * @param string|null $strControlId An optional control ID to uniquely identify the control. Defaults to null.
         *
         * @throws Caller
         */
        public function __construct(FormBase|ControlBase $objParentObject, ?string $strControlId = null)
        {
            parent::__construct($objParentObject, $strControlId);
        }

        //////////
        // Methods
        //////////
        /**
         * Parses post data to update the state of the control based on submitted values.
         * It determines the selected items or clears selections if no value is submitted.
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws IndexOutOfRange
         */
        public function parsePostData(): void
        {
            $val = $this->objForm->checkableControlValue($this->strControlId);
            if (empty($val)) {
                $this->unselectAllItems(false);
            } else {
                $this->setSelectedItemsByIndex($val, false);
            }
        }

        /**
         * Initializes and configures the jQuery widget for the control based on the specified button mode.
         * Executes the appropriate jQuery method depending on the button mode (e.g., buttonset or button).
         * @return void
         */
        protected function makeJqWidget(): void
        {
            $ctrlId = $this->ControlId;
            if ($this->intButtonMode == self::BUTTON_MODE_SET) {
                Application::executeControlCommand($ctrlId, 'buttonset', ApplicationBase::PRIORITY_HIGH);
            } elseif ($this->intButtonMode == self::BUTTON_MODE_JQ) {
                Application::executeSelectorFunction(["input:checkbox", "#" . $ctrlId], 'button', ApplicationBase::PRIORITY_HIGH);
            }
        }

        /**
         * Generates the HTML for an individual item with styling, attributes, and optionally wraps it with a label.
         *
         * @param ListItem $objItem The item to render as HTML. Contains attributes like label, name, and selected state.
         * @param int $intIndex The index of the item in the list.
         * @param string|null $strTabIndex Optional tab index to be assigned for keyboard navigation.
         * @param bool $blnWrapLabel Whether the label should wrap the input element or be associated separately.
         * @return string The rendered HTML string for the item.
         */
        protected function getItemHtml(ListItem $objItem, int $intIndex, ?string $strTabIndex, bool $blnWrapLabel): string
        {
            $objLabelStyles = new TagStyler();
            if ($this->objItemStyle) {
                $objLabelStyles->override($this->objItemStyle); // default style
            }
            if ($objItemStyle = $objItem->ItemStyle) {
                $objLabelStyles->override($objItemStyle); // per item styling
            }

            $objStyles = new TagStyler();
            $objStyles->setHtmlAttribute('type', 'checkbox');
            $objStyles->setHtmlAttribute('name', $this->strControlId . '[]');
            $objStyles->setHtmlAttribute('value', $intIndex);

            $strIndexedId = $objItem->Id;
            $objStyles->setHtmlAttribute('id', $strIndexedId);
            if ($strTabIndex) {
                $objStyles->TabIndex = $strTabIndex;
            }
            if (!$this->Enabled) {
                $objStyles->Enabled = false;
            }

            $strLabelText = $objItem->Label;
            if (empty($strLabelText)) {
                $strLabelText = $objItem->Name;
            }
            if ($this->blnHtmlEntities) {
                $strLabelText = QString::htmlEntities($strLabelText);
            }

            if ($objItem->Selected) {
                $objStyles->setHtmlAttribute('checked', 'checked');
            }

            if (!$blnWrapLabel) {
                $objLabelStyles->setHtmlAttribute('for', $strIndexedId);
            }

            $objStyles->addCssClass('qc-tableCell');
            $objLabelStyles->addCssClass('qc-tableCell');

            return Q\Html::renderLabeledInput(
                $strLabelText,
                $this->strTextAlign == Q\Html::TEXT_ALIGN_LEFT,
                $objStyles->renderHtmlAttributes(),
                $objLabelStyles->renderHtmlAttributes(),
                $blnWrapLabel);
        }

        /**
         * Generate the HTML for the control based on the button mode configuration.
         * @return string The rendered HTML for the control.
         */
        protected function getControlHtml(): string
        {
            /* Deprecated. Use Margin and Padding on the ItemStyle attribute.
            If ($this->intCellPadding >= 0)
                $strCellPadding = sprintf('cellpadding="%s" ', $this->intCellPadding);
            else
                $strCellPadding = "";

            if ($this->intCellSpacing >= 0)
                $strCellSpacing = sprintf('cellspacing="%s" ', $this->intCellSpacing);
            else
                $strCellSpacing = "";
            */

            if ($this->intButtonMode == self::BUTTON_MODE_SET || $this->intButtonMode == self::BUTTON_MODE_LIST) {
                return $this->renderButtonSet();
            } else {
                $strToReturn = $this->renderButtonTable();
            }

            return $strToReturn;
        }

        /**
         * Generates and renders a table of buttons by organizing items into rows and columns
         * based on the configured repeat direction and column count. If a maximum height is
         * specified, the table is wrapped in a scrolling container.
         *
         * @return string The rendered HTML content as a string representing the button table.
         */
        public function renderButtonTable(): string
        {
            $strToReturn = '';
            if ($this->ItemCount > 0) {
                // Figure out the number of ROWS for this table
                $intRowCount = floor($this->ItemCount / $this->intRepeatColumns);
                $intWidowCount = ($this->ItemCount % $this->intRepeatColumns);
                if ($intWidowCount > 0) {
                    $intRowCount++;
                }

                // Iterate through Table Rows
                for ($intRowIndex = 0; $intRowIndex < $intRowCount; $intRowIndex++) {
                    // Figure out the number of COLUMNS for this particular ROW
                    if (($intRowIndex == $intRowCount - 1) && ($intWidowCount > 0)) { // on the last row for a table with widowed-columns, ColCount is the number of widows
                        $intColCount = $intWidowCount;
                    } else { // otherwise, ColCount is simply intRepeatColumns
                        $intColCount = $this->intRepeatColumns;
                    }

                    // Iterate through Table Columns
                    $strRowHtml = '';
                    for ($intColIndex = 0; $intColIndex < $intColCount; $intColIndex++) {
                        if ($this->strRepeatDirection == self::REPEAT_HORIZONTAL) {
                            $intIndex = $intColIndex + $this->intRepeatColumns * $intRowIndex;
                        } else {
                            $intIndex = (floor($this->ItemCount / $this->intRepeatColumns) * $intColIndex)
                                + min(($this->ItemCount % $this->intRepeatColumns), $intColIndex)
                                + $intRowIndex;
                        }

                        $strItemHtml = $this->getItemHtml($this->objListItemArray[$intIndex], $intIndex,
                            $this->getHtmlAttribute('tabindex'), $this->blnWrapLabel);
                        $strRowHtml .= $strItemHtml;
                    }

                    $strRowHtml = Html::renderTag('div', ['class' => 'qc-tableRow'], $strRowHtml);
                    $strToReturn .= $strRowHtml;
                }

                if ($this->strMaxHeight) {
                    // wrap a table in a scrolling div that will end up being the actual object
                    //$objStyler = new QTagStyler();
                    $this->setCssStyle('max-height', $this->strMaxHeight, true);
                    $this->setCssStyle('overflow-y', 'scroll');

                    $strToReturn = Html::renderTag('div', ['class' => 'qc-table'], $strToReturn);
                } else {
                    $this->addCssClass('qc-table'); // format as a table
                }
            }

            return $this->renderTag('div', ['id' => $this->strControlId], null, $strToReturn);
        }

        /**
         * Generates and renders a set of buttons by iterating through a list of items, generating the HTML
         * for each item, and appending it to the final output with a newline character between each entry.
         * The resulting HTML is wrapped within a parent div element.
         *
         * @return string The rendered HTML content as a string representing the button set.
         */
        public function renderButtonSet(): string
        {
            $count = $this->ItemCount;
            $strToReturn = '';
            for ($intIndex = 0; $intIndex < $count; $intIndex++) {
                $strToReturn .= $this->getItemHtml($this->objListItemArray[$intIndex], $intIndex,
                        $this->getHtmlAttribute('tabindex'), $this->blnWrapLabel) . "\n";
            }
            return $this->renderTag('div', ['id' => $this->strControlId], null, $strToReturn);
        }

        /**
         * Renders a column of buttons by iterating through a list of items and wrapping each item's HTML
         * in a div element. The resulting HTML structure is wrapped in a parent div element.
         *
         * @return string The rendered HTML content as a string representing the button column.
         */
        public function renderButtonColumn(): string
        {
            $count = $this->ItemCount;
            $strToReturn = '';
            for ($intIndex = 0; $intIndex < $count; $intIndex++) {
                $strHtml = $this->getItemHtml($this->objListItemArray[$intIndex], $intIndex,
                    $this->getHtmlAttribute('tabindex'), $this->blnWrapLabel);
                $strToReturn .= Html::renderTag('div', null, $strHtml);
            }
            return $this->renderTag('div', ['id' => $this->strControlId], null, $strToReturn);
        }


        /**
         * Validates the current state of the object. Checks if a required value is selected when the object is marked as required.
         * @return bool Returns true if validation passes, false otherwise.
         */
        public function validate(): bool
        {
            if ($this->blnRequired) {
                if ($this->SelectedIndex == -1) {
                    $this->ValidationError = t($this->strName) . ' ' . t('is required');
                    return false;
                }
            }
            return true;
        }

        /**
         * Refreshes the selection state of the control and updates any associated UI elements.
         * Executes necessary JavaScript functions to synchronize the selection.
         *
         * @return void
         */
        protected function refreshSelection(): void
        {
            $indexes = $this->SelectedIndexes;
            Application::executeSelectorFunction(['input', '#' . $this->ControlId], 'val', $indexes);
            if ($this->intButtonMode == self::BUTTON_MODE_SET ||
                $this->intButtonMode == self::BUTTON_MODE_JQ
            ) {
                Application::executeSelectorFunction(['input', '#' . $this->ControlId], 'button', "refresh");
            }
        }


        /////////////////////////
        // Public Properties: GET
        /////////////////////////

        /**
         * Magic method to retrieve the value of a property by name.
         * Supports various appearance, behavior, and layout attributes.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the specified property.
         * @throws Caller If the accessed property is not defined.
         * @throws \Exception
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                // APPEARANCE
                case "TextAlign":
                    return $this->strTextAlign;

                // BEHAVIOR
                case "HtmlEntities":
                    return $this->blnHtmlEntities;

                // LAYOUT
                case "CellPadding":
                    return $this->intCellPadding;
                case "CellSpacing":
                    return $this->intCellSpacing;
                case "RepeatColumns":
                    return $this->intRepeatColumns;
                case "RepeatDirection":
                    return $this->strRepeatDirection;
                case "ItemStyle":
                    return $this->objItemStyle;
                case "ButtonMode":
                    return $this->intButtonMode;
                case "MaxHeight":
                    return $this->strMaxHeight;

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
         * Sets the value for a given property by name, performing type validation and handling specific logic
         * based on the property's attributes.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the specified property.
         *
         * @return void
         *
         * @throws InvalidCast Thrown if the provided value cannot be cast to the expected type.
         * @throws Caller Thrown if an invalid property name is provided or validation fails for certain
         *     constraints.
         * @throws \Exception
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                // APPEARANCE
                case "TextAlign":
                    try {
                        if ($this->strTextAlign !== ($mixValue = Type::cast($mixValue, Type::STRING))) {
                            $this->blnModified = true;
                            $this->strTextAlign = $mixValue;
                        }
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "HtmlEntities":
                    try {
                        if ($this->blnHtmlEntities !== ($mixValue = Type::cast($mixValue, Type::BOOLEAN))) {
                            $this->blnModified = true;
                            $this->blnHtmlEntities = $mixValue;
                        }
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                // LAYOUT
                case "CellPadding":
                    try {
                        if ($this->intCellPadding !== ($mixValue = Type::cast($mixValue, Type::INTEGER))) {
                            $this->blnModified = true;
                            $this->intCellPadding = $mixValue;
                        }
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "CellSpacing":
                    try {
                        if ($this->intCellSpacing !== ($mixValue = Type::cast($mixValue, Type::INTEGER))) {
                            $this->blnModified = true;
                            $this->intCellSpacing = $mixValue;
                        }
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "RepeatColumns":
                    try {
                        if ($this->intRepeatColumns !== ($mixValue = Type::cast($mixValue,
                                Type::INTEGER))
                        ) {
                            $this->blnModified = true;
                            $this->intRepeatColumns = $mixValue;
                        }
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    if ($this->intRepeatColumns < 1) {
                        throw new Caller("RepeatColumns must be greater than 0");
                    }
                    break;
                case "RepeatDirection":
                    try {
                        if ($this->strRepeatDirection !== ($mixValue = Type::cast($mixValue,
                                Type::STRING))
                        ) {
                            $this->blnModified = true;
                            $this->strRepeatDirection = $mixValue;
                        }
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "ItemStyle":
                    try {
                        $this->blnModified = true;
                        $this->objItemStyle = Type::cast($mixValue, "\\QCubed\\TagStyler");
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;

                case "ButtonMode":
                    try {
                        if ($this->intButtonMode !== ($mixValue = Type::cast($mixValue, Type::INTEGER))) {
                            $this->blnModified = true;
                            $this->intButtonMode = $mixValue;
                        }
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;

                case "MaxHeight":
                    try {
                        if (empty($mixValue)) {
                            $this->strMaxHeight = null;
                        } else {
                            $this->strMaxHeight = Type::cast($mixValue, Type::STRING);
                        }
                        $this->blnModified = true;
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
         * Retrieves the parameters for configuring the model connector, merging the parent's parameters
         * with additional specific parameters for customization.
         *
         * @return array Returns an array of model connector parameters, including options for text alignment,
         *               HTML entity handling, checkbox repeat columns and directions, button display modes,
         *               and maximum height for scrollable panes.
         * @throws Caller
         */
        public static function getModelConnectorParams(): array
        {
            return array_merge(parent::getModelConnectorParams(), array(
                new QModelConnectorParam(get_called_class(), 'TextAlign', '', QModelConnectorParam::SELECTION_LIST,
                    array(
                        null => 'Default',
                        '\\QCubed\\Css\\TextAlignType::LEFT' => 'Left',
                        '\\QCubed\\Css\\TextAlignType::RIGHT' => 'Right',
                        '\\QCubed\\Css\\TextAlignType::CENTER' => 'Center'

                    )),
                new QModelConnectorParam(get_called_class(), 'HtmlEntities',
                    'Set too false to have the browser interpret the labels as HTML', Type::BOOLEAN),
                new QModelConnectorParam(get_called_class(), 'RepeatColumns',
                    'The number of columns of checkboxes to display', Type::INTEGER),
                new QModelConnectorParam(get_called_class(), 'RepeatDirection',
                    'Whether to repeat horizontally or vertically', QModelConnectorParam::SELECTION_LIST,
                    array(
                        null => 'Default',
                        '\\QCubed\\Control\\CheckboxList::REPEAT_HORIZONTAL' => 'Horizontal',
                        '\\QCubed\\Control\\CheckboxList::REPEAT_VERTICAL' => 'Vertical'
                    )),
                new QModelConnectorParam(get_called_class(), 'ButtonMode', 'How to display the buttons',
                    QModelConnectorParam::SELECTION_LIST,
                    array(
                        null => 'Default',
                        '\\QCubed\\Control\\CheckboxList::BUTTON_MODE_JQ' => 'JQuery UI Buttons',
                        '\\QCubed\\Control\\CheckboxList::BUTTON_MODE_SET' => 'JQuery UI Buttonset'
                    )),
                new QModelConnectorParam(get_called_class(), 'MaxHeight',
                    'If set, will wrap it in a scrollable pane with the given max height', Type::INTEGER)
            ));
        }

        /**
         * Returns the generator corresponding to this control.
         *
         * @return \QCubed\Codegen\Generator\CheckboxList
         */
        public static function getCodeGenerator(): Q\Codegen\Generator\CheckboxList
        {
            return new Q\Codegen\Generator\CheckboxList(__CLASS__);
        }

    }
