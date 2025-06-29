<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Action;

use QCubed\Control\ControlBase;

/**
 * Class CssAction
 *
 * Sets the CSS class of a control on the client side (does not update the server side)
 *
 * @package QCubed\Action
 */
class CssAction extends ActionBase
{
    /** @var string|null CSS property to be set */
    protected ?string $strCssProperty = null;
    /** @var string|null Value to which the CSS property should be set */
    protected ?string $strCssValue = null;
    /**
     * @var null|string The control ID for which the action should be done.
     *                  By default, it is applied to the QControl to which the action is added.
     */
    protected ?string $strControlId = null;

    /**
     * Constructor
     *
     * @param string $strCssProperty
     * @param string $strCssValue
     * @param ControlBase|null $objControl
     */
    public function __construct(string $strCssProperty, string $strCssValue, ?ControlBase $objControl = null)
    {
        $this->strCssProperty = $strCssProperty;
        $this->strCssValue = $strCssValue;
        if ($objControl) {
            $this->strControlId = $objControl->ControlId;
        }
    }

    /**
     * Returns the JavaScript to be executed on the client side
     *
     * @param ControlBase $objControl
     *
     * @return string JavaScript to be executed on the client side for setting the CSS
     */
    public function renderScript(ControlBase $objControl): string
    {
        if ($this->strControlId == null) {
            $this->strControlId = $objControl->ControlId;
        }

        // Specified a Temporary CSS Class to use?
        return sprintf('$j("#%s").css("%s", "%s"); ', $this->strControlId, $this->strCssProperty, $this->strCssValue);
    }
}
