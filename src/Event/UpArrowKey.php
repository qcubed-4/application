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
 * Class UpArrowKey
 *
 * @was QUpArrowKeyEvent
 * @package QCubed\Event
 */
class UpArrowKey extends KeyDown
{
    /** @var string Condition JS */
    protected $strCondition = 'event.keyCode == 38';
}
