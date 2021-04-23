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
 * Class SortableRemove
 *
 * The abstract SortableRemove class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * This event is triggered when a sortable item from the list has been
 * dropped into another. The former is the event target.
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
 * 	* placeholder Type: jQuery The jQuery object representing the
 * element being used as a placeholder.
 * 
 *
 * @was QSortable_RemoveEvent
 */
class SortableRemove extends EventBase
{
    const EVENT_NAME = 'sortremove';
}
