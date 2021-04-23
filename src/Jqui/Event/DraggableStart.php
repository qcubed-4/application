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
 * Class DraggableStart
 *
 * The abstract DraggableStart class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered when dragging starts.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* helper Type: jQuery The jQuery object representing the helper thats
 * being dragged.
 * 	* position Type: Object Current CSS position of the helper as { top,
 * left } object.
 * 	* offset Type: Object Current offset position of the helper as {
 * top, left } object.
 * 
 *
 * @was QDraggable_StartEvent
 */
class DraggableStart extends EventBase
{
    const EVENT_NAME = 'dragstart';
}
