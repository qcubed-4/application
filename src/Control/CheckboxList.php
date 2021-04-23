<?php
/**
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

require_once(dirname(dirname(__DIR__)) . '/i18n/i18n-lib.inc.php');
use QCubed\Application\t;

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\QString;
use QCubed\TagStyler;
use QCubed as Q;
use QCubed\Type;
use QCubed\Html;
use QCubed\ModelConnector\Param as QModelConnectorParam;


/**
 * Class CheckboxList
 *
 * This class will render a List of HTML Checkboxes (inhereting from ListControl).
 * By definition, checkbox lists are multiple-select ListControls.
 *
 * So assuming you have a list of 10 items, and you have RepeatColumn set to 3:
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
 * @property string $Text is used to display text that is displayed next to the checkbox.  The text is rendered as an html "Label For" the checkbox.
 * @property integer $CellPadding specified the HTML Table's CellPadding
 * @property integer $CellSpacing specified the HTML Table's CellSpacing
 * @property integer $RepeatColumns specifies how many columns should be rendered in the HTML Table
 * @property string $RepeatDirection pecifies which direction should the list go first...
 * @property boolean $HtmlEntities
 * @was QCheckBoxList
 * @package QCubed\Control
 */
class CheckboxList extends ListControl
{
    const BUTTON_MODE_NONE = 0;    // Uses the RepeatColumns and RepeateDirection settings to make a structure
    const BUTTON_MODE_JQ = 1;        // a list of individual jquery ui buttons
    const BUTTON_MODE_SET = 2;    // a jqueryui button set
    const BUTTON_MODE_LIST = 3;    // just a vanilla list of checkboxes with no row or column styling

    ///////////////////////////
    // Private Member Variables
    ///////////////////////////

    // APPEARANCE
    protected $strTextAlign = Q\Html::TEXT_ALIGN_RIGHT;

    // BEHAVIOR
    protected $blnHtmlEntities = true;

    // LAYOUT
    protected $intCellPadding = -1;
    protected $intCellSpacing = -1;
    protected $intRepeatColumns = 1;
    protected $strRepeatDirection = self::REPEAT_VERTICAL;
    protected $objItemStyle = null;
    protected $intButtonMode;
    protected $strMaxHeight; // will create a scroll pane if height is exceeded

    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
    }

    //////////
    // Methods
    //////////
    /**
     * Parses the post data. Many different scenarios are covered. See below.
     */
    public function parsePostData()
    {
        $val = $this->objForm->checkableControlValue($this->strControlId);
        if (empty($val)) {
            $this->unselectAllItems(false);
        } else {
            $this->setSelectedItemsByIndex($val, false);
        }
    }

    /**
     * Return the javascript associated with the control.
     */
    protected function makeJqWidget()
    {
        $ctrlId = $this->ControlId;
        if ($this->intButtonMode == self::BUTTON_MODE_SET) {
            Application::executeControlCommand($ctrlId, 'buttonset', Application::PRIORITY_HIGH);
        } elseif ($this->intButtonMode == self::BUTTON_MODE_JQ) {
            Application::executeSelectorFunction(["input:checkbox", "#" . $ctrlId], 'button', Application::PRIORITY_HIGH);
        }
    }

    /**
     * Return the HTML for the given item.
     *
     * @param ListItem $objItem
     * @param integer $intIndex
     * @param string $strTabIndex
     * @param boolean $blnWrapLabel
     * @return string
     */
    protected function getItemHtml(ListItem $objItem, $intIndex, $strTabIndex, $blnWrapLabel)
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

        $strHtml = Q\Html::renderLabeledInput(
            $strLabelText,
            $this->strTextAlign == Q\Html::TEXT_ALIGN_LEFT,
            $objStyles->renderHtmlAttributes(),
            $objLabelStyles->renderHtmlAttributes(),
            $blnWrapLabel);

        return $strHtml;
    }

    /**
     * Return the html to draw the base control.
     * @return string
     */
    protected function getControlHtml()
    {
        /* Deprecated. Use Margin and Padding on the ItemStyle attribute.
        if ($this->intCellPadding >= 0)
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
     * Renders the button group as a table, paying attention to the number of columns wanted.
     * @return string
     */
    public function renderButtonTable()
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
                // wrap table in a scrolling div that will end up being the actual object
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
     * Renders the checkbox list as a buttonset, rendering just as a list of checkboxes and allowing css or javascript
     * to format the rest.
     * @return string
     */
    public function renderButtonSet()
    {
        $count = $this->ItemCount;
        $strToReturn = '';
        for ($intIndex = 0; $intIndex < $count; $intIndex++) {
            $strToReturn .= $this->getItemHtml($this->objListItemArray[$intIndex], $intIndex,
                    $this->getHtmlAttribute('tabindex'), $this->blnWrapLabel) . "\n";
        }
        $strToReturn = $this->renderTag('div', ['id' => $this->strControlId], null, $strToReturn);
        return $strToReturn;
    }

    /**
     * Render as a single column. This implementation simply wraps the columns in divs.
     * @return string
     */
    public function renderButtonColumn()
    {
        $count = $this->ItemCount;
        $strToReturn = '';
        for ($intIndex = 0; $intIndex < $count; $intIndex++) {
            $strHtml = $this->getItemHtml($this->objListItemArray[$intIndex], $intIndex,
                $this->getHtmlAttribute('tabindex'), $this->blnWrapLabel);
            $strToReturn .= Html::renderTag('div', null, $strHtml);
        }
        $strToReturn = $this->renderTag('div', ['id' => $this->strControlId], null, $strToReturn);
        return $strToReturn;
    }


    /**
     * Validate the control.
     * @return bool
     */
    public function validate()
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
     * Override of superclass that will update the selection using javascript so that the whole control does
     * not need to be redrawn.
     */
    protected function refreshSelection()
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
     * Returns a description of the options available to modify by the designer for the code generator.
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
                    '\\QCubed\\Css\\TextAlignType::RIGHT' => 'Right',
                    '\\QCubed\\Css\\TextAlignType::CENTER' => 'Center'

                )),
            new QModelConnectorParam(get_called_class(), 'HtmlEntities',
                'Set to false to have the browser interpret the labels as HTML', Type::BOOLEAN),
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
     * @return Q\Codegen\Generator\GeneratorBase
     */
    public static function getCodeGenerator() {
        return new Q\Codegen\Generator\CheckboxList(__CLASS__);
    }

}
