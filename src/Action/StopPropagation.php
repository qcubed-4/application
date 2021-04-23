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
 * Class StopPropagation
 *
 * Prevents the event from bubbling up the DOM tree, preventing any parent
 * handlers from being notified of the event.
 *
 * @was QStopPropagationAction
 * @package QCubed\Action
 */
class StopPropagation extends ActionBase
{
    /**
     * Returns the JavaScript to be executed on the client side
     *
     * @param ControlBase $objControl
     *
     * @return string Client side JS
     */
    public function renderScript(ControlBase $objControl)
    {
        return 'event.stopPropagation();';
    }
}
