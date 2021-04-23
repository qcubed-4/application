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
 * Class TabKey
 *
 * @was QTabKeyEvent
 * @package QCubed\Event
 */
class TabKey extends KeyDown
{
    /** @var string Condition JS with keycode for tab key */
    protected $strCondition = 'event.keyCode == 9';
}
