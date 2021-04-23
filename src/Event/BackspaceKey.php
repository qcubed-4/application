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
 * When the Backspace key is pressed with element in focus
 *
 * @was QBackspaceKeyEvent
 */
class BackspaceKey extends KeyDown
{
    /** @var string Condition JS with keycode for escape key */
    protected $strCondition = 'event.keyCode == 8';
}

