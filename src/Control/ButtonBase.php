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
 * Class ButtonBase
 *
 * Base class for HTML Button.
 *
 * Since the html button tag can have any html markup as content, the button is a subclass of a block control. You can
 * use Text, Template, or subclass and define your own getInnerHtml method to define the content of a button.
 *
 * @package Controls
 *
 * @property boolean $PrimaryButton is a boolean to specify whether or not the button is 'primary'
 * (e.g. makes this button a "Submit" form element rather than a "Button" form element)
 * @was QButtonBase
 * @package QCubed\Control
 */
abstract class ButtonBase extends BlockControl
{
    ///////////////////////////
    // Private Member Variables
    ///////////////////////////

    // BEHAVIOR
    /** @var bool Is the button a primary button (causes form submission)? */
    protected $blnPrimaryButton = false;

    // SETTINGS
    /**
     * @var bool Prevent any more actions from happening once action has been taken on this control
     *  causes "event.preventDefault()" to be called on the client side
     */
    protected $blnActionsMustTerminate = true;
    protected $strTagName = "button";
    protected $mixCausesValidation = self::CAUSES_VALIDATION_ALL;   //default to causing validation. Can be turned off by user of control.


    /**
     * Returns the attributes as html
     *
     * @param null $attributeOverrides
     * @param null $styleOverrides
     * @return string
     */
    public function renderHtmlAttributes($attributeOverrides = null, $styleOverrides = null) {
        if (!$attributeOverrides) {
            $attributeOverrides = [];
        }
        if ($this->blnPrimaryButton) {
            $attributeOverrides['type'] = "submit";
        } else {
            $attributeOverrides['type'] = "button";
        }
        $attributeOverrides['name'] = $this->ControlId;
        return parent::renderHtmlAttributes($attributeOverrides, $styleOverrides);
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * PHP Magic __get method implementation
     * @param string $strName Name of the property to be fetched
     *
     * @return mixed
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case "PrimaryButton":
                return $this->blnPrimaryButton;

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
     * PHP Magic method __set implementation for this class (QButtonBase)
     * @param string $strName Name of the property
     * @param string $mixValue Value of the property
     *
     * @return void
     * @throws InvalidCast|Caller
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "PrimaryButton":
                try {
                    $val = Type::Cast($mixValue, Type::BOOLEAN);
                    if ($val !== $this->blnPrimaryButton) {
                        $this->blnPrimaryButton = $val;
                        $this->blnModified = true;
                    }
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
}
