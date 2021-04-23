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
 * Blur event: keyboard focus moving away from the control.
 *
 * @was QBlurEvent
 * @package QCubed\Event
 */
class Blur extends EventBase
{
    /** Event Name */
    const EVENT_NAME = 'blur';
}
