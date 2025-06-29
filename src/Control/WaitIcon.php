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
use QCubed\Exception\InvalidCast;
use QCubed\Type;
use QCubed as Q;

/**
 * @package QCubed\Control
 */
class WaitIcon extends Q\Project\Control\ControlBase
{
    ///////////////////////////
    // Private Member Variables
    ///////////////////////////

    // APPEARANCE
    /** @var string String to be displayed as alt text (e.g. "Please wait")  */
    protected string $strText;
    /** @var string HTML tag name to be used for rendering the text */
    protected string $strTagName = 'span';
    /** @var bool */
    protected bool $blnDisplay = false;

    /**
     * Constructor for the class.
     *
     * @param mixed $objParentObject The parent object to which this control belongs.
     * @param string|null $strControlId Optional control ID. If null, an ID will be generated automatically.
     * @return void
     * @throws Caller
     */
    public function __construct(FormBase|ControlBase $objParentObject, ?string $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->strText = t('Please wait...');
    }

    public function parsePostData(): void
    {
    }

    /**
     * Validates the wait icon (for now it just returns true)
     *
     * @return bool
     */
    public function validate(): bool
    {
        return true;
    }

    /**
     * Returns the HTML we have to send to the browser to render this wait icon
     * @return string HTML to be returned
     */
    protected function getControlHtml(): string
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
     * Magic method to get the value of a property by its name.
     *
     * @param string $strName The name of the property to retrieve.
     * @return mixed The value of the requested property.
     * @throws Caller If the property does not exist or an error occurs during retrieval.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            // APPEARANCE
            case "Text":
                return $this->strText;
            case "TagName":
                return $this->strTagName;

            /** Uses HtmlAttributeManager now
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
     * Magic method to set the value of a property dynamically.
     *
     * @param string $strName The name of the property to be set.
     * @param mixed $mixValue The value to assign to the property.
     * @return void
     * @throws InvalidCast If the value cannot be cast to the required type.
     * @throws Caller If the property does not exist or cannot be set.
     */
    public function __set(string $strName, mixed $mixValue): void
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
