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
 * Class DragDrop
 *
 * Drop event: When an element is dropped onto another element
 *
 * @was QDragDropEvent
 * @package QCubed\Event
 */
class DragDrop extends EventBase
{
    /** Event Name */
    const EVENT_NAME = 'drop';
}
