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
 * Class KeyPress
 *
 * When a keyboard key has been pressed (key went down, and went up)
 *
 * @was QKeyPressEvent
 * @package QCubed\Event
 */
class KeyPress extends EventBase
{
    /** Event Name */
    const EVENT_NAME = 'keypress';
}
