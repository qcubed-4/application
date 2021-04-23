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
 * Class SelectMenuChange
 *
 * The abstract SelectMenuChange class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered when the selected item has changed. Not every select event
 * will fire a change event.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* item Type: jQuery The active item.
 * 
 *
 * @was QSelectMenu_ChangeEvent
 */
class SelectMenuChange extends EventBase
{
    const EVENT_NAME = 'selectmenuchange';
}
