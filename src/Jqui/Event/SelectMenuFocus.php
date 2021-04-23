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
 * Class SelectMenuFocus
 *
 * The abstract SelectMenuFocus class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered when an items gains focus.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* item Type: jQuery The focused item.
 * 
 *
 * @was QSelectMenu_FocusEvent
 */
class SelectMenuFocus extends EventBase
{
    const EVENT_NAME = 'selectmenufocus';
}
