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
 * Class ResizableStop
 *
 * The abstract ResizableStop class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * This event is triggered at the end of a resize operation.
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
 * top }
 * 	* size Type: Object The current size represented as { width, height
 * }
 * 
 *
 * @was QResizable_StopEvent
 */
class ResizableStop extends EventBase
{
    const EVENT_NAME = 'resizestop';
}
