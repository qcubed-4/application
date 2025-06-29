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
//use QCubed\Application\t;

use QCubed as Q;
use QCubed\Css\TextAlignType;
use QCubed\Exception\Caller;
use QCubed\Exception\IndexOutOfRange;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\QString;
use QCubed\Type;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class RadioButtonList
 *
 * This class will render a List of HTML Radio Buttons (inherited from ListControl).
 * By definition, radio button lists are single-select ListControls.
 *
 * So assuming you have a list of 10 items, and you have RepeatColumn set to 3:
 *
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
 * @property string $TextAlign specifies if each ListItem's Name should be displayed to the left or to the right of the radio button.
 * @property boolean $HtmlEntities
 * @property integer $CellPadding specified the HTML Table's CellPadding
 * @property integer $CellSpacing specified the HTML Table's CellSpacing
 * @property integer $RepeatColumns specifies how many columns should be rendered in the HTML Table
 * @property string $RepeatDirection specifies which direction the list should go first: horizontal or vertical
 * @property integer $ButtonMode specifies how to render buttons
 * @package QCubed\Control
 */
class RadioButtonList extends ListControl
{
    const BUTTON_MODE_NONE = 0;
    const BUTTON_MODE_JQ = 1;
    const BUTTON_MODE_SET = 2;
    const BUTTON_MODE_LIST = 3;    // just a vanilla list of radio buttons with no row or column styling

    /** @var string  */
    protected string $strTextAlign = Q\Html::TEXT_ALIGN_RIGHT;

    /** @var  string The class to use when wrapping a button-label group */
    protected string $strButtonGroupClass;

    /** @var bool  */
    protected bool $blnHtmlEntities = true;

    /** @var int  */
    protected int $intCellPadding = -1;
    /** @var int  */
    protected int $intCellSpacing = -1;
    /** @var int  */
    protected int $intRepeatColumns = 1;
    /** @var string  */
    protected string $strRepeatDirection = self::REPEAT_VERTICAL;
    /** @var null|ListItemStyle */
    protected  null|ListItemStyle $objItemStyle  = null;
    /** @var  int|null */
    protected ?int $intButtonMode = null;
    /** @var  string */
    protected string $strMaxHeight = ''; // will create a scroll pane if height is exceeded

    /**
     * Constructor for the class. Initializes the object and sets up the default item style.
     *
     * @param mixed $objParentObject The parent object to which this control belongs.
     * @param string|null $strControlId An optional control ID for this instance. If not provided, a default ID is generated.
     * @return void
     * @throws Caller
     */
    public function __construct(mixed $objParentObject, ? string $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->objItemStyle = new ListItemStyle();
    }

    //////////
    // Methods
    //////////
    public function parsePostData(): void
    {
        $val = $this->objForm->checkableControlValue($this->strControlId);
        if ($val === null) {
            $this->unselectAllItems(false);
        } else {
            $this->setSelectedItemsByIndex(array($val), false);
        }
    }

    public function makeJqWidget(): void
    {
        $ctrlId = $this->ControlId;
        if ($this->intButtonMode == self::BUTTON_MODE_SET) {
            Application::executeControlCommand($ctrlId, 'buttonset', Q\ApplicationBase::PRIORITY_HIGH);
        } elseif ($this->intButtonMode == self::BUTTON_MODE_JQ) {
            Application::executeSelectorFunction(["input:radio", "#" . $ctrlId], 'button', Q\ApplicationBase::PRIORITY_HIGH);
        }
    }

    /**
     * Generates the HTML for an individual item in the control.
     *
     * @param mixed $objItem The item for which HTML is being generated.
     * @param int $intIndex The index of the item within the control.
     * @param string|null $strTabIndex The tabindex attribute value for the input element.
     * @param bool $blnWrapLabel Whether to wrap the label around the input element.
     * @return string The rendered HTML for the item.
     */
    protected function getItemHtml(mixed $objItem, int $intIndex, ?string $strTabIndex, bool $blnWrapLabel): string
    {
        $objLabelStyles = new Q\TagStyler();
        if ($this->objItemStyle) {
            $objLabelStyles->override($this->objItemStyle); // default style
        }
        if ($objItemStyle = $objItem->ItemStyle) {
            $objLabelStyles->override($objItemStyle); // per item styling
        }

        $objStyles = new Q\TagStyler();
        $objStyles->setHtmlAttribute('type', 'radio');
        $objStyles->setHtmlAttribute('value', $intIndex);
        $objStyles->setHtmlAttribute('name', $this->strControlId);
        $strIndexedId = $this->strControlId . '_' . $intIndex;
        $objStyles->setHtmlAttribute('id', $strIndexedId);

        if ($strTabIndex) {
            $objStyles->TabIndex = $strTabIndex;    // Use parent control tabIndex, which will cause the browser to take them in order of drawing
        }
        if (!$this->Enabled) {
            $objStyles->Enabled = false;
        }

        $strLabelText = $this->getLabelText($objItem);

        if ($objItem->Selected) {
            $objStyles->setHtmlAttribute('checked', 'checked');
        }

        $objStyles->setHtmlAttribute("autocomplete", "off"); // recommended bugfix for firefox in certain situations

        if (!$blnWrapLabel) {
            $objLabelStyles->setHtmlAttribute('for', $strIndexedId);
        }

        $this->overrideItemAttributes($objItem, $objStyles, $objLabelStyles);

        return Q\Html::renderLabeledInput(
            $strLabelText,
            $this->strTextAlign == TextAlignType::LEFT,
            $objStyles->renderHtmlAttributes(),
            $objLabelStyles->renderHtmlAttributes(),
            $blnWrapLabel);
    }

    /**
     * Override the item attributes, allowing customization for the given item, its attributes, and label attributes.
     * @param object $objItem The item to override attributes for.
     * @param Q\TagStyler $objItemAttributes The object containing style attributes for the item.
     * @param Q\TagStyler $objLabelAttributes The object containing style attributes for the label associated with the item.
     * @return void
     */
    protected function overrideItemAttributes(object $objItem, Q\TagStyler $objItemAttributes, Q\TagStyler $objLabelAttributes): void
    {
    }

    /**
     * Retrieves the label text for a given item. Uses the item's Label property, falling back to the Name property if the Label is empty. Optionally applies HTML entity encoding based on the instance's configuration.
     *
     * @param mixed $objItem The item from which to retrieve the label text. The item must have Label and Name properties.
     * @return string The processed label text.
     */
    protected function getLabelText(mixed $objItem): string
    {
        $strLabelText = $objItem->Label;
        if (empty($strLabelText)) {
            $strLabelText = $objItem->Name;
        }
        if ($this->blnHtmlEntities) {
            $strLabelText = QString::htmlEntities($strLabelText);
        }
        return $strLabelText;
    }

    protected function getControlHtml(): string
    {
        $intItemCount = $this->getItemCount();
        if (!$intItemCount) {
            return '';
        }

        if ($this->intButtonMode == self::BUTTON_MODE_SET || $this->intButtonMode == self::BUTTON_MODE_LIST) {
            return $this->renderButtonSet();
        } elseif ($this->intRepeatColumns == 1) {
            $strToReturn = $this->renderButtonColumn();
        } else {
            $strToReturn = $this->renderButtonTable();
        }

        if ($this->strMaxHeight) {
            $objStyler = new Q\TagStyler();
            $objStyler->setCssStyle('max-height', $this->strMaxHeight, true);
            $objStyler->setCssStyle('overflow-y', 'scroll');

            $strToReturn = Q\Html::renderTag('div', $objStyler->renderHtmlAttributes(), $strToReturn);
        }
        return $strToReturn;
    }

    /**
     * Renders the button group as a table, paying attention to the number of columns wanted.
     * @return string
     * @throws Caller
     * @throws InvalidCast
     * @throws IndexOutOfRange
     */
    public function renderButtonTable(): string
    {
        // TODO: Do this without using a table, since this is really not a correct use of html
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

                    $strItemHtml = $this->getItemHtml($this->getItem($intIndex), $intIndex,
                        $this->getHtmlAttribute('tabindex'), $this->blnWrapLabel);
                    $strCellHtml = Q\Html::renderTag('td', null, $strItemHtml);
                    $strRowHtml .= $strCellHtml;
                }

                $strRowHtml = Q\Html::renderTag('tr', null, $strRowHtml);
                $strToReturn .= $strRowHtml;
            }
        }

        return $this->renderTag('table',
            null,
            null,
            $strToReturn);
    }

    /**
     * Renders the checkbox list as a buttonset, rendering just as a list of checkboxes and allowing CSS or JavaScript
     * to format the rest.
     * @return string
     * @throws Caller
     * @throws IndexOutOfRange
     * @throws InvalidCast
     */
    public function renderButtonSet(): string
    {
        $count = $this->ItemCount;
        $strToReturn = '';
        for ($intIndex = 0; $intIndex < $count; $intIndex++) {
            $strToReturn .= $this->getItemHtml($this->getItem($intIndex), $intIndex,
                    $this->getHtmlAttribute('tabindex'), $this->blnWrapLabel) . "\n";
        }
        return $this->renderTag('div',
            null,
            null,
            $strToReturn);
    }

    /**
     * Render as a single column. This implementation simply wraps the rows in divs.
     * @return string
     * @throws Caller
     * @throws IndexOutOfRange
     * @throws InvalidCast
     */
    public function renderButtonColumn(): string
    {
        $count = $this->ItemCount;
        $strToReturn = '';
        $groupAttributes = null;
        if ($this->strButtonGroupClass) {
            $groupAttributes = ["class" => $this->strButtonGroupClass];
        }
        for ($intIndex = 0; $intIndex < $count; $intIndex++) {
            $strHtml = $this->getItemHtml($this->getItem($intIndex), $intIndex, $this->getHtmlAttribute('tabindex'),
                $this->blnWrapLabel);
            $strToReturn .= Q\Html::renderTag('div', $groupAttributes, $strHtml);
        }
        return $this->renderTag('div',
            null,
            null,
            $strToReturn);
    }

    public function validate(): bool
    {
        if ($this->blnRequired) {
            if ($this->SelectedIndex == -1) {
                $this->ValidationError = sprintf(t('%s is required'), $this->strName);
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
        $index = $this->SelectedIndex;
        Application::executeSelectorFunction(['input', '#' . $this->ControlId], 'val', [$index]);
        if ($this->intButtonMode == self::BUTTON_MODE_SET ||
            $this->intButtonMode == self::BUTTON_MODE_JQ
        ) {
            Application::executeSelectorFunction(['input', '#' . $this->ControlId], 'button', "refresh");
        }
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
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
                    $this->intCellPadding = Type::cast($mixValue, Type::INTEGER);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "CellSpacing":
                try {
                    $this->intCellSpacing = Type::cast($mixValue, Type::INTEGER);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "RepeatColumns":
                try {
                    $this->intRepeatColumns = Type::cast($mixValue, Type::INTEGER);
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
                    $this->strRepeatDirection = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "ItemStyle":
                try {
                    $this->objItemStyle = Type::cast($mixValue, "\QCubed\Control\ListItemStyle");
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case "ButtonMode":
                try {
                    $this->intButtonMode = Type::cast($mixValue, Type::INTEGER);
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
            new QModelConnectorParam(get_called_class(), 'TextAlign', '', QModelConnectorParam::SELECTION_LIST,
                array(
                    null => 'Default',
                    '\\QCubed\\Css\\TextAlignType::LEFT' => 'Left',
                    '\\QCubed\\Css\\TextAlignType::RIGHT' => 'Right'
                )),
            new QModelConnectorParam(get_called_class(), 'HtmlEntities',
                'Set too false to have the browser interpret the labels as HTML', Type::BOOLEAN),
            new QModelConnectorParam(get_called_class(), 'RepeatColumns',
                'The number of columns of checkboxes to display', Type::INTEGER),
            new QModelConnectorParam(get_called_class(), 'RepeatDirection',
                'Whether to repeat horizontally or vertically', QModelConnectorParam::SELECTION_LIST,
                array(
                    null => 'Default',
                    '\\QCubed\\Control\\RadioButtonList::REPEAT_HORIZONTAL' => 'Horizontal',
                    '\\QCubed\\Control\\RadioButtonList::REPEAT_VERTICAL' => 'Vertical'
                )),
            new QModelConnectorParam(get_called_class(), 'ButtonMode', 'How to display the buttons',
                QModelConnectorParam::SELECTION_LIST,
                array(
                    null => 'Default',
                    '\\QCubed\\Control\\RadioButtonList::BUTTON_MODE_JQ' => 'JQuery UI Buttons',
                    '\\QCubed\\Control\\RadioButtonList::BUTTON_MODE_SET' => 'JQuery UI Buttonset'
                )),
            new QModelConnectorParam(get_called_class(), 'MaxHeight',
                'If set, will wrap it in a scrollable pane with the given max height', Type::INTEGER)
        ));
    }

    /**
     * Returns the generator corresponding to this control.
     *
     * @return Q\Codegen\Generator\RadioButtonList
     */
    public static function getCodeGenerator(): Q\Codegen\Generator\RadioButtonList
    {
        return new Q\Codegen\Generator\RadioButtonList(__CLASS__);
    }

}
