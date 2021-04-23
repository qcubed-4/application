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
 * Class DroppableOver
 *
 * The abstract DroppableOver class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered when an accepted draggable is dragged over the droppable
 * (based on thetolerance option).
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* draggable Type: jQuery A jQuery object representing the draggable
 * element.
 * 	* helper Type: jQuery A jQuery object representing the helper that
 * is being dragged.
 * 	* position Type: Object Current CSS position of the draggable helper
 * as { top, left } object.
 * 	* offset Type: Object Current offset position of the draggable
 * helper as { top, left } object.
 * 
 *
 * @was QDroppable_OverEvent
 */
class DroppableOver extends EventBase
{
    const EVENT_NAME = 'dropover';
}
