<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Css\DisplayType;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Type;

/**
 * Class Fieldset
 *
 * Encapsulates a fieldset, which has a legend that acts as a label. HTML5 defines a new Name element, which
 * is not yet supported in IE of this writing, but other browsers support it. So, if its defined, we will output
 * it in the html, but it will not affect what appears on the screen unless you draw the Name too.
 *
 * @package Controls\Base
 *
 * @property string $Legend is the legend that will be output for the fieldset.
 * @was QFieldset
 * @package QCubed\Control
 */
class Fieldset extends BlockControl
{
    /** @var string HTML tag to the used for the Block Control */
    protected $strTagName = 'fieldset';
    /** @var string Default display style for the control. See QCubed\Css\Display class for available list */
    protected $strDefaultDisplayStyle = DisplayType::BLOCK;
    /** @var bool Is the control a block element? */
    protected $blnIsBlockElement = true;
    /** @var bool Use htmlentities for the control? */
    protected $blnHtmlEntities = false;
    /** @var  string legend */
    protected $strLegend;

    /**
     * We will output style tags and such, but fieldset styling is not well supported across browsers.
     */
    protected function getInnerHtml()
    {
        $strHtml = parent::getInnerHtml();

        if (!empty($this->strLegend)) {
            $strHtml = '<legend>' . $this->strLegend . '</legend>' . _nl() . $strHtml;
        }

        return $strHtml;
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    public function __get($strName)
    {
        switch ($strName) {
            // APPEARANCE
            case "Legend":
                return $this->strLegend;

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
            case "Legend":
                try {
                    $this->strLegend = Type::cast($mixValue, Type::STRING);
                    $this->blnModified = true;
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
}
