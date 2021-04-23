<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Type;

/**
 * Class ImageInput
 *
 * This class will render an HTML Image input <input type="image">.
 *
 * Image inputs act like buttons, but specifically also produce an x and y coordinate for where the image was clicked.
 * There are other ways to produce image buttons, including using a Button control and adding an Image
 * control to it, or adding a background image to a Button. You can also just use an Image control and add an onClick handler.
 * Each produce different html, and you can pick which one is more suitable to your needs.
 *
 * @property string $AlternateText is rendered as the HTML "alt" tag
 * @property string $ImageUrl is the url of the image to be used
 * @property boolean $PrimaryButton     Set to true if you want this button to submit the form
 * @property-read integer $ClickX
 * @property-read integer $ClickY
 * @was QImageButton
 * @package QCubed\Control
 */
class ImageInput extends ActionControl
{
    protected $strAlternateText = null;
    protected $strImageUrl = null;
    protected $intClickX;
    protected $intClickY;
    /** @var bool True to make this button submit the form, which is the default for HTML input images */
    protected $blnPrimaryButton = true;

    // SETTINGS
    protected $blnActionsMustTerminate = true;


    /**
     * MUST be used in conjunction with RegisterClickPosition Action to work.
     */
    public function parsePostData()
    {
        $strKeyX = sprintf('%s_x', $this->strControlId);
        $strKeyY = sprintf('%s_y', $this->strControlId);
        if (isset ($_POST[$strKeyX]) && $_POST[$strKeyX] !== '') {
            $this->intClickX = $_POST[$strKeyX];
            $this->intClickY = $_POST[$strKeyY];
        }
    }

    protected function getControlHtml()
    {
        $overrides = [
            'name'=>$this->strControlId,
            'type'=>'image',
            'alt'=>$this->strAlternateText,
            'src'=>$this->strImageUrl
        ];

        if (!$this->blnPrimaryButton) {
            //$overrides['onclick'] = "return false;";   // prevent default behavior
        }

        $strToReturn = $this->renderTag('input', $overrides,
            null, null, true);

        $strToReturn .= sprintf('<input type="hidden" name="%s_x" id="%s_x" value=""/><input type="hidden" name="%s_y" id="%s_y" value=""/>',
            $this->strControlId,
            $this->strControlId,
            $this->strControlId,
            $this->strControlId);

        return $strToReturn;
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    public function __get($strName)
    {
        switch ($strName) {
            // APPEARANCE
            case "AlternateText":
                return $this->strAlternateText;
            case "ImageUrl":
                return $this->strImageUrl;

            // BEHAVIOR
            case "PrimaryButton":
                return $this->blnPrimaryButton;
            case "ClickX":
                return $this->intClickX;
            case "ClickY":
                return $this->intClickY;

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
        $this->blnModified = true;

        switch ($strName) {
            // APPEARANCE
            case "AlternateText":
                try {
                    $this->strAlternateText = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "ImageUrl":
                try {
                    $this->strImageUrl = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            // BEHAVIOR
            case "PrimaryButton":
                try {
                    $this->blnPrimaryButton = Type::cast($mixValue, Type::BOOLEAN);
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
