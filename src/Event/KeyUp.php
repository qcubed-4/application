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
 * Class KeyUp
 *
 * When a pressed key goes up while the focus is on the control
 *
 * @was QKeyUpEvent
 * @package QCubed\Event
 */
class KeyUp extends EventBase
{
    /** Event Name */
    const EVENT_NAME = 'keyup';
}

