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
 * @package QCubed\Event
 */
class UpArrowKey extends KeyDown
{
    /** @var string|null Condition JS */
    protected ?string $strCondition = 'event.keyCode == 38';
}
