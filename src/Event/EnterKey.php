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
 * Class EnterKey
 *
 * When enter key is pressed while the control is in focus
 *
 * @was QEnterKeyEvent
 * @package QCubed\Event
 */
class EnterKey extends KeyDown
{
    /** @var string Condition JS */
    protected $strCondition = 'event.keyCode == 13';
}
