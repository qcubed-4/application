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
 * Class SortableBeforeStop
 *
 * The abstract SortableBeforeStop class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * This event is triggered when sorting stops, but when the
 * placeholder/helper is still available.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* helper Type: jQuery The jQuery object representing the helper being
 * sorted.
 * 	* item Type: jQuery The jQuery object representing the current
 * dragged element.
 * 	* offset Type: Object The current absolute position of the helper
 * represented as { top, left }.
 * 	* position Type: Object The current position of the helper
 * represented as { top, left }.
 * 	* originalPosition Type: Object The original position of the element
 * represented as { top, left }.
 * 	* sender Type: jQuery The sortable that the item comes from if
 * moving from one sortable to another.
 * 	* placeholder Type: jQuery The jQuery object representing the
 * element being used as a placeholder.
 * 
 *
 * @was QSortable_BeforeStopEvent
 */
class SortableBeforeStop extends EventBase
{
    const EVENT_NAME = 'sortbeforestop';
}
