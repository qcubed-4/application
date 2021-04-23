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
 * Use this event with the QJsTimer control
 * this event is trigger after a
 * delay specified in QJsTimer (param DeltaTime)
 * @was QTimerExpiredEvent
 * @package QCubed\Event
 */
class TimerExpired extends EventBase
{
    /** Event's name. Used by QCubed framework for its internal purpose */
    const EVENT_NAME = 'timerexpiredevent';
}
