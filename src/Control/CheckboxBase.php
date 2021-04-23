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
use QCubed\QString;
use QCubed\Type;
use QCubed\TagStyler;
use QCubed\ModelConnector\Param as QModelConnectorParam;
use QCubed\Html;

/**
 * Class Checkbox
 *
 * This class will render an HTML Checkbox.
 *
 * Labels are a little tricky with checkboxes. There are two built-in ways to make labels:
 * 1) Assign a Name property, and render using something like RenderWithName
 * 2) Assign a Text property, in which case the checkbox will be wrapped with a label and the text you assign.
 *
 * @property string $Text is used to display text that is displayed next to the checkbox.  The text is rendered as an html "Label For" the checkbox.
 * @property string $TextAlign specifies if "Text" should be displayed to the left or to the right of the checkbox.
 * @property boolean $Checked specifices whether or not hte checkbox is checked
 * @property boolean $HtmlEntities specifies whether the checkbox text will have to be run through htmlentities or not.
 * @package QCubed\Control
 */
class CheckboxBase extends Q\Project\Control\ControlBase
{
    /** @var string Tag for rendering the control */
    protected $strTag = 'input';
    protected $blnIsVoidElement = true;

    // APPEARANCE
    /** @var string Text opposite to the checkbox */
    protected $strText = null;
    /** @var string the alignment of the string */
    protected $strTextAlign = TextAlignType::RIGHT;

    // BEHAVIOR
    /** @var bool Should the htmlentities function be run on the control's text (strText)? */
    protected $blnHtmlEntities = true;

    // MISC
    /** @var bool Determines whether the checkbox is checked? */
    protected $blnChecked = false;

    /**
     * @var  TagStyler for labels of checkboxes. If side-by-side labeling, the styles will be applied to a
     * span that wraps both the checkbox and the label.
     */
    protected $objLabelStyle;


    //////////
    // Methods
    //////////

    /**
     * Parses the Post Data submitted for the control and sets the values
     * according to the data submitted
     */
    public function parsePostData()
    {
        $val = $this->objForm->checkableControlValue($this->strControlId);
        if ($val !== null) {
            $this->blnChecked = Type::cast($val, Type::BOOLEAN);
        }
    }

    /**
     * Returns the HTML code for the control which can be sent to the client.
     *
     * Note, previous version wrapped this in a div and made the control a block level control unnecessarily. To
     * achieve a block control, set blnUseWrapper and blnIsBlockElement.
     *
     * @return string THe HTML for the control
     */
    protected function getControlHtml()
    {
        $attrOverride = array('type' => 'checkbox', 'name' => $this->strControlId, 'value' => 'true');
        return $this->renderButton($attrOverride);
    }

    /**
     * Render the button code. Broken out to allow QRadioButton to use it too.
     *
     * @param $attrOverride
     * @return string
     */
    protected function renderButton($attrOverride)
    {
        if ($this->blnChecked) {
            $attrOverride['checked'] = 'checked';
        }

        if (strlen($this->strText)) {
            $strText = ($this->blnHtmlEntities) ? QString::htmlEntities($this->strText) : $this->strText;
            if (!$this->blnWrapLabel) {
                $strLabelAttributes = ' for="' . $this->strControlId . '"';
            } else {
                $strLabelAttributes = $this->renderLabelAttributes();
            }
            $strCheckHtml = Html::renderLabeledInput(
                $strText,
                $this->strTextAlign == Html::TEXT_ALIGN_LEFT,
                $this->renderHtmlAttributes($attrOverride),
                $strLabelAttributes,
                $this->blnWrapLabel
            );
            if (!$this->blnWrapLabel) {
                // Additionally wrap in a span so we can associate the label with the checkbox visually and apply the styles
                $strCheckHtml = Html::renderTag('span', $this->renderLabelAttributes(), $strCheckHtml);
            }
        } else {
            $strCheckHtml = $this->renderTag('input', $attrOverride, null, null, true);
        }
        return $strCheckHtml;
    }

    /**
     * Return a styler to style the label that surrounds the control if the control has text.
     * @return TagStyler
     */
    public function getCheckLabelStyler()
    {
        if (!$this->objLabelStyle) {
            $this->objLabelStyle = new TagStyler();
        }
        return $this->objLabelStyle;
    }

    /**
     * There is a little bit of a conundrum here. If there is text assigned to the checkbox, we wrap
     * the checkbox in a label. However, in this situation, its unclear what to do with the class and style
     * attributes that are for the checkbox. We are going to let the developer use the label styler to make
     * it clear what their intentions are.
     * @return string
     */
    protected function renderLabelAttributes()
    {
        $objStyler = new TagStyler();
        $attributes = $this->getHtmlAttributes(null, null, ['title']); // copy tooltip to wrapping label
        $objStyler->setAttributes($attributes);
        $objStyler->override($this->getCheckLabelStyler());

        if (!$this->Enabled) {
            $objStyler->addCssClass('disabled');    // add the disabled class to the label for styling
        }
        if (!$this->Display) {
            $objStyler->Display = false;
        }
        return $objStyler->renderHtmlAttributes();
    }

    /**
     * Checks whether the post data submitted for the control is valid or not
     * Right now it tests whether or not the control was marked as required and then tests whether it
     * was checked or not
     * @return bool
     */
    public function validate()
    {
        if ($this->blnRequired) {
            if (!$this->blnChecked) {
                if ($this->strName) {
                    $this->ValidationError = t($this->strName) . ' ' . t('is required');
                } else {
                    $this->ValidationError = t('Required');
                }
                return false;
            }
        }
        return true;
    }

    /**
     * Returns the current state of the control to be able to restore it later.
     */
    public function getState()
    {
        return array('checked' => $this->Checked);
    }

    /**
     * Restore the  state of the control.
     *
     * @param mixed $state
     */
    public function putState($state)
    {
        if (isset($state['checked'])) {
            $this->Checked = $state['checked'];
        }
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * PHP __get magic method implementation
     * @param string $strName Name of the property
     *
     * @return mixed
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            // APPEARANCE
            case "Text":
                return $this->strText;
            case "TextAlign":
                return $this->strTextAlign;

            // BEHAVIOR
            case "HtmlEntities":
                return $this->blnHtmlEntities;

            // MISC
            case "Checked":
                return $this->blnChecked;
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
     * @param string $strName
     * @param string $mixValue
     *
     * @return void
     * @throws InvalidCast|Caller
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            // APPEARANCE

            case "Text":
                try {
                    $val = Type::cast($mixValue, Type::STRING);
                    if ($val !== $this->strText) {
                        $this->strText = $val;
                        $this->blnModified = true;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "TextAlign":
                try {
                    $val = Type::cast($mixValue, Type::STRING);
                    if ($val !== $this->strTextAlign) {
                        $this->strTextAlign = $val;
                        $this->blnModified = true;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "HtmlEntities":
                try {
                    $this->blnHtmlEntities = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            // MISC
            case "Checked":
                try {
                    $val = Type::cast($mixValue, Type::BOOLEAN);
                    if ($val != $this->blnChecked) {
                        $this->blnChecked = $val;
                        $this->addAttributeScript('prop', 'checked', $val);
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            // Copy certain attributes to the label styler when assigned since its part of the control.
            case 'CssClass':
                try {
                    parent::__set($strName, $mixValue);
                    $this->getCheckLabelStyler()->CssClass = $mixValue; // assign to both checkbox and label so they can be styled together using css
                    $this->blnModified = true;
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

    /**
     * Returns an description of the options available to modify by the designer for the code generator.
     *
     * @return QModelConnectorParam[]
     */
    public static function getModelConnectorParams()
    {
        return array_merge(parent::getModelConnectorParams(), array(
            new QModelConnectorParam (get_called_class(), 'Text', 'Label on checkbox', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'TextAlign', 'Left or right alignment of label',
                QModelConnectorParam::SELECTION_LIST,
                array(
                    '\\QCubed\\Css\\TextAlignType::RIGHT' => 'Right',
                    '\\QCubed\\Css\\TextAlignType::LEFT' => 'Left'
                )),
            new QModelConnectorParam (get_called_class(), 'HtmlEntities', 'Whether to apply HTML entities on the label',
                Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'CssClass',
                'The css class(es) to apply to the checkbox and label together', Type::STRING)
        ));
    }

}