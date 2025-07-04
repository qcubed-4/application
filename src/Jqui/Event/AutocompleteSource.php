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
 * Class AutocompleteSource
 *
 * A special event to handle source ajax callbacks
 *
 * @package QCubed\Jqui\Event
 */
class AutocompleteSource extends EventBase
{
    /** Event Name */
    const EVENT_NAME = 'QAutocomplete_Source';
    const JS_RETURN_PARAM = 'ui'; // ends up being the request.term value
}