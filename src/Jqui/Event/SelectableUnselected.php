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
 * Class SelectableUnselected
 *
 * The abstract SelectableUnselected class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered at the end of the select operation, on each element removed
 * from the selection.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* unselected Type: Element The selectable item that has been
 * unselected.
 * 
 *
 * @was QSelectable_UnselectedEvent
 */
class SelectableUnselected extends EventBase
{
    const EVENT_NAME = 'selectableunselected';
}
