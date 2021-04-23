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
 * Class TabsBeforeActivate
 *
 * The abstract TabsBeforeActivate class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered immediately before a tab is activated. Can be canceled to
 * prevent the tab from activating. If the tabs are currently collapsed,
 * ui.oldTab and ui.oldPanel will be empty jQuery objects. If the tabs
 * are collapsing, ui.newTab and ui.newPanel will be empty jQuery
 * objects.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* newTab Type: jQuery The tab that is about to be activated.
 * 	* oldTab Type: jQuery The tab that is about to be deactivated.
 * 	* newPanel Type: jQuery The panel that is about to be activated.
 * 	* oldPanel Type: jQuery The panel that is about to be deactivated.
 * 
 *
 * @was QTabs_BeforeActivateEvent
 */
class TabsBeforeActivate extends EventBase
{
    const EVENT_NAME = 'tabsbeforeactivate';
}
