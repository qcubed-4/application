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
 * Class TabsCreate
 *
 * The abstract TabsCreate class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered when the tabs are created. If the tabs are collapsed, ui.tab
 * and ui.panel will be empty jQuery objects.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* tab Type: jQuery The active tab.
 * 	* panel Type: jQuery The active panel.
 * 
 *
 * @was QTabs_CreateEvent
 */
class TabsCreate extends EventBase
{
    const EVENT_NAME = 'tabscreate';
}
