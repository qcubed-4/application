<?php
/*/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Action;

use QCubed\Control\ControlBase;

/**
 * Class SetValue
 *
 * Sets the JavaScript value of a control in the form. The value has to be known ahead of time. Useful for
 * automatically clearing a text field when it receives focus, for example.
 *
 * @package QCubed\Action
 */
class SetValue extends ActionBase {
    protected ?string $strControlId = null;
    protected mixed $strValue = "";

    /**
     * Constructor method to initialize the object with control properties.
     *
     * @param ControlBase $objControl The control object containing necessary properties.
     * @param string|null $strValue Optional value to set for the object, default is an empty string.
     */
    public function __construct(ControlBase $objControl, mixed $strValue = "") {
        $this->strControlId = $objControl->ControlId;
        $this->strValue = $strValue;
    }

    /**
     * @param ControlBase $objControl
     * @return string
     */
    public function renderScript(ControlBase $objControl): string
    {
        return sprintf("jQuery('#%s').val('%s');", $this->strControlId, $this->strValue);
    }
}