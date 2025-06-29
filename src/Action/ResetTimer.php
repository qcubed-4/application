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
 * Class ResetTimer
 *
 * Resets a particular on a control.
 *
 * @package QCubed\Action
 */
class ResetTimer extends ActionBase
{
    /**
     * Returns the JavaScript to be executed on the client side (to clear the timeout on the control)
     *
     * @param ControlBase $objControl Control on which the timeout has to be cleared
     *
     * @return string JavaScript to be executed on the client side
     */
    public function renderScript(ControlBase $objControl): string
    {
        return sprintf("qcubed.clearTimeout('%s');", $objControl->ControlId);
    }
}
