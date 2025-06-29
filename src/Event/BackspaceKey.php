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
 * When the Backspace key is pressed with an element in focus
 *
 */
class BackspaceKey extends KeyDown
{
    /** @var string|null Condition JS with keycode for an escape key */
    protected ?string $strCondition = 'event.keyCode == 8';
}

