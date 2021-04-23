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
 * Be careful with change events for listboxes -
 * they don't fire when the user picks a value on many browsers!
 *
 * @was QChangeEvent
 * @package QCubed\Event
 */
class Change extends EventBase
{
    /** Event Name */
    const EVENT_NAME = 'change';
}
