<?php
    /**
     *
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
    use QCubed\Html;
    use QCubed\Project\Application;
    use QCubed\QString;
    use QCubed\Type;
    use QCubed as Q;

    /**
     * Class ListBoxBase
     *
     * This will render an HTML DropDown or MultiSelect box [SELECT] element.
     * It extends {@link ListControl}.  By default, the number of visible rows is set to 1 and
     * the selection mode is set to single, creating a dropdown select box.
     *
     * @property integer $Rows          specifies how many rows you want to have shown.
     * @property string $LabelForRequired
     * @property string $LabelForRequiredUnnamed
     * @property string $SelectionMode SELECTION_MODE_* const specifies if this is a "Single" or "Multiple" select control.
     *
     * @package QCubed\Control
     */
    abstract class ListBoxBase extends ListControl
    {
        /** Can select only one item. */
        public const string SELECTION_MODE_SINGLE = 'Single';
        /** Can select more than one */
        public const string SELECTION_MODE_MULTIPLE = 'Multiple';
        /** Selection mode isn't specified */
        public const string SELECTION_MODE_NONE = 'None';

        ///////////////////////////
        // Private Member Variables
        ///////////////////////////

        // APPEARANCE
        /** @var string Error to be shown if the box is empty, has a name and is marked as required */
        protected string $strLabelForRequired;
        /** @var string Error to be shown If the box is empty, doesn't have a name and is marked as required */
        protected string $strLabelForRequiredUnnamed;

        /**
         * ListBoxBase constructor.
         * @param ControlBase|FormBase $objParentObject
         * @param string|null $strControlId
         * @throws Caller
         */
        public function __construct(FormBase|ControlBase $objParentObject, ?string $strControlId = null)
        {
            parent::__construct($objParentObject, $strControlId);

            $this->strLabelForRequired = t('%s is required');
            $this->strLabelForRequiredUnnamed = t('Required');
            $this->objItemStyle = new ListItemStyle();
        }

        /**
         * Parses and processes POST data for the control to determine selected items.
         * Updates the control's state based on the submitted form data.
         *
         * Handles multi-select and single-select controls, processing the selection
         * appropriately or clearing selections when no data is submitted.
         *
         * @return void
         * @throws Caller
         * @throws IndexOutOfRange
         * @throws InvalidCast
         */
        public function parsePostData(): void
        {
            if (array_key_exists($this->strControlId, $_POST)) {
                if (is_array($_POST[$this->strControlId])) {
                    // Multi-Select, so find them all.
                    $this->setSelectedItemsById($_POST[$this->strControlId], false);
                } elseif ($_POST[$this->strControlId] === '') {
                    $this->unselectAllItems(false);
                } else {
                    // Single-select
                    $this->setSelectedItemsById(array($_POST[$this->strControlId]), false);
                }
            } else {
                // Multiselect forms with nothing passed via $_POST mean that everything was DE selected
                if ($this->SelectionMode == self::SELECTION_MODE_MULTIPLE) {
                    $this->unselectAllItems(false);
                }
            }
        }

        /**
         * Returns the HTML-Code for a single Item
         *
         * @param ListItem $objItem
         * @return string resulting HTML
         */
        protected function getItemHtml(ListItem $objItem): string
        {
            // The Default Item Style
            if ($this->objItemStyle) {
                $objStyler = clone ($this->objItemStyle);
            } else {
                $objStyler = new ListItemStyle();
            }

            // Apply any Style Override (if applicable)
            if ($objStyle = $objItem->ItemStyle) {
                $objStyler->override($objStyle);
            }

            $objStyler->setHtmlAttribute('value', ($objItem->Empty) ? '' : $objItem->Id);

            if ($objItem->Selected) {
                $objStyler->setHtmlAttribute('selected', 'selected');
            }

            if ($objItem->Disabled) {
                $objStyler->setHtmlAttribute('disabled', 'disabled');
            }

            return Html::renderTag('option', $objStyler->renderHtmlAttributes(),
                    QString::htmlEntities($objItem->Name), false, true) . _nl();
        }

        /**
         * Returns the HTML for the entire control.
         * @return string
         * @throws Caller
         */
        protected function getControlHtml(): string
        {
            // If no selection is specified, we select the first item, because once we draw this, that is what the browser
            // will consider selected on the screen.
            // We need to make sure that what we draw is mirrored in our current state
            if ($this->SelectionMode == self::SELECTION_MODE_SINGLE &&
                $this->SelectedIndex == -1 &&
                $this->ItemCount > 0
            ) {
                $this->SelectedIndex = 0;
            }

            if ($this->SelectionMode == self::SELECTION_MODE_MULTIPLE) {
                $attrOverride['name'] = $this->strControlId . "[]";
            } else {
                $attrOverride['name'] = $this->strControlId;
            }

            $strToReturn = $this->renderTag('select', $attrOverride, null, $this->renderInnerHtml());

            // If MultiSelect and if NOT required, add a "Reset" button to deselect everything
            if (($this->SelectionMode == self::SELECTION_MODE_MULTIPLE) && (!$this->blnRequired) && ($this->Enabled) && ($this->blnVisible)) {
                $strToReturn .= $this->getResetButtonHtml();
            }
            return $strToReturn;
        }

        /**
         * Renders the inner HTML of the list box, including options and option groups if applicable.
         *
         * This method organizes items into groups if they have a defined group label and generates
         * the appropriate HTML structure. Items without a group are rendered directly as individual options,
         * while grouped items are wrapped within optgroup tags.
         *
         * @return string The rendered HTML content for the list box.
         * @throws Caller
         */
        protected function renderInnerHtml(): string
        {
            $strHtml = '';
            $intItemCount = $this->getItemCount();
            if (!$intItemCount) {
                return '';
            }
            $groups = array();

            for ($intIndex = 0; $intIndex < $intItemCount; $intIndex++) {
                try {
                    /** @var ListItem $objItem */
                    $objItem = $this->getItem($intIndex);
                } catch (InvalidCast) {
                    // If an error occurs, continue with the next index
                    continue;
                }
                // Figure Out Groups (if applicable)
                if ($strGroup = $objItem->ItemGroup) {
                    $groups[$strGroup][] = $objItem;
                } else {
                    $groups[''][] = $objItem;
                }
            }

            foreach ($groups as $strGroup => $items) {
                if (!$strGroup) {
                    foreach ($items as $objItem) {
                        $strHtml .= $this->getItemHtml($objItem);
                    }
                } else {
                    $strGroupHtml = '';
                    foreach ($items as $objItem) {
                        $strGroupHtml .= $this->getItemHtml($objItem);
                    }
                    $strHtml .= Html::renderTag('optgroup', ['label' => QString::htmlEntities($strGroup)], $strGroupHtml);
                }
            }
            return $strHtml;
        }

        // For multiple-select-based lightboxes, you must define the way a "Reset" button should look

        /**
         * Generates and returns the HTML string for the reset button.
         *
         * @return string The HTML content for the reset button.
         */
        abstract protected function getResetButtonHtml(): string;

        /**
         * Determines whether the supplied input data is valid or not.
         * @return bool
         */
        public function validate(): bool
        {
            if ($this->blnRequired) {
                if ($this->SelectedIndex == -1) {
                    if ($this->strName) {
                        $this->ValidationError = sprintf($this->strLabelForRequired, $this->strName);
                    } else {
                        $this->ValidationError = $this->strLabelForRequiredUnnamed;
                    }
                    return false;
                }

                if ($this->SelectedIndex == 0 && $this->SelectedValue == null) {
                    if ($this->strName) {
                        $this->ValidationError = sprintf($this->strLabelForRequired, $this->strName);
                    } else {
                        $this->ValidationError = $this->strLabelForRequiredUnnamed;
                    }
                    return false;
                }
            }

            return true;
        }

        /**
         * Override of superclass that will update the selection using JavaScript so that the whole control does
         * not need to be redrawn.
         */
        protected function refreshSelection(): void
        {
            $items = $this->SelectedItems;
            $values = [];
            foreach ($items as $objItem) {
                $values[] = $objItem->Id;
            }

            Application::executeControlCommand($this->ControlId, 'val', $values);
        }

        /**
         * Restore the state of the control. This override makes sure the item exists before putting it. Otherwise,
         * if the item did not exist, the default selection would be removed and nothing would be selected.
         * @param mixed $state
         */
        public function putState(mixed $state): void
        {
            if (!empty($state['SelectedValues'])) {
                // assume only one selection in a list
                $strValue = reset($state['SelectedValues']);
                if ($this->findItemByValue($strValue)) {
                    $this->SelectedValues = [$strValue];
                }
            }
        }


        /////////////////////////
        // Public Properties: GET
        /////////////////////////

        /**
         * Magic method to retrieve property values.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed Returns the value of the requested property.
         * @throws Caller If the property does not exist.
         * @throws \Exception
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                // APPEARANCE
                case "Rows":
                    return $this->getHtmlAttribute('size');
                case "LabelForRequired":
                    return $this->strLabelForRequired;
                case "LabelForRequiredUnnamed":
                    return $this->strLabelForRequiredUnnamed;

                // BEHAVIOR
                case "SelectionMode":
                    return $this->hasHtmlAttribute('multiple') ? self::SELECTION_MODE_MULTIPLE : self::SELECTION_MODE_SINGLE;

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
         * Magic method to set the value of a property.
         * Allows dynamic assignment of properties and handles typecasting or validation for certain attributes.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the property.
         *
         * @return void
         * @throws Caller Thrown when attempting to set an undefined property.
         * @throws InvalidCast Thrown when the value cannot be cast to the required type.*@throws \Exception
         * @throws \Exception
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                // APPEARANCE
                case "Rows":
                    try {
                        $this->setHtmlAttribute('size', Type::cast($mixValue, Type::INTEGER));
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "LabelForRequired":
                    try {
                        $this->strLabelForRequired = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "LabelForRequiredUnnamed":
                    try {
                        $this->strLabelForRequiredUnnamed = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                // BEHAVIOR
                case "SelectionMode":
                    try {
                        if (Type::cast($mixValue, Type::STRING) == self::SELECTION_MODE_MULTIPLE) {
                            $this->setHtmlAttribute('multiple', 'multiple');
                        } else {
                            $this->removeHtmlAttribute('multiple');
                        }
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

        /**
         * Returns a description of the options available to modify by the designer for the code generator.
         *
         * @return Q\ModelConnector\Param[]
         * @throws Caller
         */
        public static function getModelConnectorParams(): array
        {
            return array_merge(parent::getModelConnectorParams(), array(
                new Q\ModelConnector\Param(get_called_class(), 'Rows', 'Height of field for multirow field',
                    Type::INTEGER),
                new Q\ModelConnector\Param(get_called_class(), 'SelectionMode', 'Single or multiple selections',
                    Q\ModelConnector\Param::SELECTION_LIST,
                    array(
                        null => 'Default',
                        'self::SELECTION_MODE_SINGLE' => 'Single',
                        'self::SELECTION_MODE_MULTIPLE' => 'Multiple'
                    ))
            ));
        }

        /**
         * Creates and returns an instance of the ListBox code generator.
         *
         * @return Q\Codegen\Generator\ListBox An instance of the ListBox code generator.
         */
        public static function getCodeGenerator(): Q\Codegen\Generator\ListBox
        {
            return new Q\Codegen\Generator\ListBox(__CLASS__);
        }
    }