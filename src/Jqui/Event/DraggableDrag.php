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
 * Class DraggableDrag
 *
 * The abstract DraggableDrag class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered while the mouse is moved during the dragging, immediately
 * before the current move happens.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* helper Type: jQuery The jQuery object representing the helper thats
 * being dragged.
 * 	* position Type: Object Current CSS position of the helper as { top,
 * left } object. The values may be changed to modify where the element
 * will be positioned. This is useful for custom containment, snapping,
 * etc.
 * 	* offset Type: Object Current offset position of the helper as {
 * top, left } object.
 * 
 *
 * @was QDraggable_DragEvent
 */
class DraggableDrag extends EventBase
{
    const EVENT_NAME = 'drag';
}
