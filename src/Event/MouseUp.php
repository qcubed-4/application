<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Event;

/**
 * Class MouseUp
 *
 * When the left mouse button is released (after being pressed) from over the control
 *
 * @was QMouseUpEvent
 * @package QCubed\Event
 */
class MouseUp extends EventBase
{
    /** Event Name */
    const EVENT_NAME = 'mouseup';
}
