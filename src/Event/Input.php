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
 * Class Input
 *
 * Detects changes to textboxes and other input elements. Responds to cut/paste, search cancel, etc.
 * Ignores arrow keys, etc.
 * Not in IE8 or below. Buggy in IE9. Full support in IE10 and above.
 * No support in Safari 5 and below for textarea elements.
 *
 * @was QInputEvent
 * @package QCubed\Event
 */
class Input extends EventBase
{
    /** Event Name */
    const EVENT_NAME = 'input';
}
