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
 * Class DownArrowKey
 *
 * @was QDownArrowKeyEvent
 * @package QCubed\Event
 */
class DownArrowKey extends KeyDown
{
    /** @var string Condition JS */
    protected $strCondition = 'event.keyCode == 40';
}
