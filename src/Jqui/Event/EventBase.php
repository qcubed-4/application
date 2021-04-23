<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Jqui\Event;

/**
 * Class QJqUiEvent: When an event is triggered by jQuery-UI (drag, drop, resize etc.)
 * @was QJqUiEvent
 * @package QCubed\Event
 */
abstract class EventBase extends \QCubed\Event\EventBase {
    // be sure to subclass your events from this class if they are JqUiEvents
}
