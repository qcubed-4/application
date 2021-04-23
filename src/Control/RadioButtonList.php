<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

require_once(dirname(dirname(__DIR__)) . '/i18n/i18n-lib.inc.php');
use QCubed\Application\t;

use QCubed as Q;
use QCubed\Css\TextAlignType;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\QString;
use QCubed\Type;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class RadioButtonList
 *
 * This class will render a List of HTML Radio Buttons (inhereting from ListControl).
 * By definition, radio button lists are single-select ListControls.
 *
 * So assuming you have a list of 10 items, and you have RepeatColumn set to 3:
 *
 *    RepeatDirection::Horizontal would render as:
 *    1    2    3
 *    4    5    6
 *    7    8    9
 *    10
 *
 *    RepeatDirection::Vertical would render as:
 *    1    5    8
 *    2    6    9
 *    3    7    10
 *    4
 *
 * @package Controls
 *
 * @property string $TextAlign specifies if each ListItem's Name should be displayed to the left or to the right of the radio button.
 * @property boolean $HtmlEntities
 * @property integer $CellPadding specified the HTML Table's CellPadding
 * @property integer $CellSpacing specified the HTML Table's CellSpacing
 * @property integer $RepeatColumns specifies how many columns should be rendered in the HTML Table
 * @property string $RepeatDirection specifies which direction should the list go first: horizontal or vertical
 * @property integer $ButtonMode specifies how to render buttons
 * @was QRadioButtonList
 * @package QCubed\Control
 */
class RadioButtonList extends ListControl
{
    const BUTTON_MODE_NONE = 0;
    const BUTTON_MODE_JQ = 1;
    const BUTTON_MODE_SET = 2;
    const BUTTON_MODE_LIST = 3;    // just a vanilla list of radio buttons with no row or column styling

    /** @var string  */
    protected $strTextAlign = Q\Html::TEXT_ALIGN_RIGHT;

    /** @var  string The class to use when wrapping a button-label group */
    protected $strButtonGroupClass;

    /** @var bool  */
    protected $blnHtmlEntities = true;

    /** @var int  */
    protected $intCellPadding = -1;
    /** @var int  */
    protected $intCellSpacing = -1;
    /** @var int  */
    protected $intRepeatColumns = 1;
    /** @var string  */
    protected $strRepeatDirection = self::REPEAT_VERTICAL;
    /** @var null|ListItemStyle  */
    protected $objItemStyle = null;
    /** @var  int */
    protected $intButtonMode;
    /** @var  string */
    protected $strMaxHeight; // will create a scroll pane if height is exceeded

    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->objItemStyle = new ListItemStyle();
    }

    //////////
    // Methods
    //////////
    public function parsePostData()
    {
        $val = $this->objForm->checkableControlValue($this->strControlId);
        if ($val === null) {
            $this->unselectAllItems(false);
        } else {
            $this->setSelectedItemsByIndex(array($val), false);
        }
    }

    public function makeJqWidget()
    {
        $ctrlId = $this->ControlId;
        if ($this->intButtonMode == self::BUTTON_MODE_SET) {
            Application::executeControlCommand($ctrlId, 'buttonset', Application::PRIORITY_HIGH);
        } elseif ($this->intButtonMode == self::BUTTON_MODE_JQ) {
            Application::executeSelectorFunction(["input:radio", "#" . $ctrlId], 'button', Application::PRIORITY_HIGH);
        }
    }

    protected function getItemHtml($objItem, $intIndex, $strTabIndex, $blnWrapLabel)
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

        $strHtml = Q\Html::renderLabeledInput(
            $strLabelText,
            $this->strTextAlign == TextAlignType::LEFT,
            $objStyles->renderHtmlAttributes(),
            $objLabelStyles->renderHtmlAttributes(),
            $blnWrapLabel);

        return $strHtml;
    }

    /**
     * Provides a way for subclasses to override the attributes on specific items just before they are drawn.
     *
     * @param $objItem
     * @param $objItemAttributes
     * @param $objLabelAttributes
     */
    protected function overrideItemAttributes($objItem, Q\TagStyler $objItemAttributes, Q\TagStyler $objLabelAttributes)
    {
    }

    /**
     * Return the escaped text of the label.
     *
     * @param $objItem
     * @return string
     */
    protected function getLabelText($objItem)
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

    protected function getControlHtml()
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
     */
    public function renderButtonTable()
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
     * Renders the checkbox list as a buttonset, rendering just as a list of checkboxes and allowing css or javascript
     * to format the rest.
     * @return string
     */
    public function renderButtonSet()
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
     */
    public function renderButtonColumn()
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

    public function validate()
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
     * Override of superclass that will update the selection using javascript so that the whole control does
     * not need to be redrawn.
     */
    protected function refreshSelection()
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
    public function __get($strName)
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
    public function __set($strName, $mixValue)
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
     * Returns an description of the options available to modify by the designer for the code generator.
     *
     * @return QModelConnectorParam[]
     */
    public static function getModelConnectorParams()
    {
        return array_merge(parent::getModelConnectorParams(), array(
            new QModelConnectorParam(get_called_class(), 'TextAlign', '', QModelConnectorParam::SELECTION_LIST,
                array(
                    null => 'Default',
                    '\\QCubed\\Css\\TextAlignType::LEFT' => 'Left',
                    '\\QCubed\\Css\\TextAlignType::RIGHT' => 'Right'
                )),
            new QModelConnectorParam(get_called_class(), 'HtmlEntities',
                'Set to false to have the browser interpret the labels as HTML', Type::BOOLEAN),
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
     * @return Q\Codegen\Generator\GeneratorBase
     */
    public static function getCodeGenerator() {
        return new Q\Codegen\Generator\RadioButtonList(__CLASS__);
    }

}
