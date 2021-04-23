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
 * Class ResizableResize
 *
 * The abstract ResizableResize class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * This event is triggered during the resize, on the drag of the resize
 * handler.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* element Type: jQuery The jQuery object representing the element to
 * be resized
 * 	* helper Type: jQuery The jQuery object representing the helper
 * thats being resized
 * 	* originalElement Type: jQuery The jQuery object representing the
 * original element before it is wrapped
 * 	* originalPosition Type: Object The position represented as { left,
 * top } before the resizable is resized
 * 	* originalSize Type: Object The size represented as { width, height
 * } before the resizable is resized
 * 	* position Type: Object The current position represented as { left,
 * top }. The values may be changed to modify where the element will be
 * positioned. Useful for custom resizing logic.
 * 	* size Type: Object The current size represented as { width, height
 * }. The values may be changed to modify where the element will be
 * positioned. Useful for custom resizing logic.
 * 
 *
 * @was QResizable_ResizeEvent
 */
class ResizableResize extends EventBase
{
    const EVENT_NAME = 'resize';
}
