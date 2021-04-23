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
 * Class RegisterClickPosition
 *
 * Registers the click position on an ImageInput control.
 *
 * @was QRegisterClickPositionAction
 * @package QCubed\Action
 */
class RegisterClickPosition extends ActionBase
{
    /** @var null|string Control ID of the control on which the click position has to be registered */
    protected $strControlId = null;

    /**
     * Returns the JavaScript to be executed on the client side
     *
     * @param ControlBase $objControl
     *
     * @return string
     */
    public function renderScript(ControlBase $objControl)
    {
        return sprintf("qc.getW('%s').registerClickPosition(event);", $objControl->ControlId);
    }
}

