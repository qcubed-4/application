<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Action;

use QCubed\Exception\Caller;
use QCubed\Control\ControlBase;
use QCubed\Type;

/**
 * Class ToggleDisplay
 *
 * Toggle the Disaply of a control
 *
 * @was QToggleDisplayAction
 * @package QCubed\Action
 */
class ToggleDisplay extends ActionBase
{
    /** @var string Control ID of the control */
    protected $strControlId = null;
    /** @var boolean|null Enforce 'show' or 'hide' action */
    protected $blnDisplay = null;

    /**
     * @param ControlBase $objControl
     * @param bool $blnDisplay
     *
     * @throws Caller
     */
    public function __construct($objControl, $blnDisplay = null)
    {
        if (!($objControl instanceof ControlBase)) {
            throw new Caller('First parameter of constructor is expecting an object of type QControl');
        }

        $this->strControlId = $objControl->ControlId;

        if (!is_null($blnDisplay)) {
            $this->blnDisplay = Type::cast($blnDisplay, Type::BOOLEAN);
        }
    }

    /**
     * Returns the JavaScript to be executed on the client side
     *
     * @param ControlBase $objControl
     *
     * @return string Returns the JavaScript to be executed on the client side
     */
    public function renderScript(ControlBase $objControl)
    {
        if ($this->blnDisplay === true) {
            $strShowOrHide = 'show';
        } else {
            if ($this->blnDisplay === false) {
                $strShowOrHide = 'hide';
            } else {
                $strShowOrHide = '';
            }
        }

        return sprintf("qc.getW('%s').toggleDisplay('%s');",
            $this->strControlId, $strShowOrHide);
    }
}
