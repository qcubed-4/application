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
 * @package QCubed\Event
 */
class TabKey extends KeyDown
{
    /** @var string|null Condition JS with keycode for tab key */
    protected ?string $strCondition = 'event.keyCode == 9';
}
