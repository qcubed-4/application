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

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Type;
use QCubed as Q;

/**
 * This file contains the QWaitIcon class.
 *
 * @package Controls
 * @filesource
 */

/**
 * @package Controls
 *
 * @property string $Text
 * @property string $TagName
 * @property string $Padding
 * @property string $HorizontalAlign
 * @property string $VerticalAlign
 */

/**
 * @was QWaitIcon
 * @package QCubed\Control
 */
class WaitIcon extends Q\Project\Control\ControlBase
{
    ///////////////////////////
    // Private Member Variables
    ///////////////////////////

    // APPEARANCE
    /** @var string String to be displayed as alt text (e.g. "Please wait")  */
    protected $strText;
    /** @var string HTML tag name to be used for rendering the text */
    protected $strTagName = 'span';
    /** @var bool */
    protected $blnDisplay = false;

    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->strText = t('Please wait...');
    }

    public function parsePostData()
    {
    }

    /**
     * Validates the wait icon (for now it just returns true)
     *
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * Returns the HTML we have to send to the browser to render this wait icon
     * @return string HTML to be returned
     */
    protected function getControlHtml()
    {
        $strImg = Q\Html::renderTag('img',
            [
                'src' => QCUBED_IMAGE_URL . '/spinner_14.gif',
                'width' => 14,
                'height' => 14,
                'alt' => $this->strText
            ],
            null,
            true);
        return $this->renderTag($this->strTagName, null, null, $strImg);
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * PHP magic method
     *
     * @param string $strName Property name
     *
     * @return mixed|null|string
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            // APPEARANCE
            case "Text":
                return $this->strText;
            case "TagName":
                return $this->strTagName;

            /** uses HtmlAttributeManager now
             * case "HorizontalAlign":
             * return $this->strHorizontalAlign;
             * case "VerticalAlign":
             * return $this->strVerticalAlign;*/

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
     * PHP magic method
     *
     * @param string $strName Property name
     * @param string $mixValue Property value
     *
     * @return mixed|void
     * @throws Caller|InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        $this->blnModified = true;

        switch ($strName) {
            // APPEARANCE
            case "Text":
                try {
                    $this->strText = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "TagName":
                try {
                    $this->strTagName = Type::cast($mixValue, Type::STRING);
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
