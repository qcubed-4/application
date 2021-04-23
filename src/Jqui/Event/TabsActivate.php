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
 * Class TabsActivate
 *
 * The abstract TabsActivate class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered after a tab has been activated (after animation completes).
 * If the tabs were previously collapsed, ui.oldTab and ui.oldPanel will
 * be empty jQuery objects. If the tabs are collapsing, ui.newTab and
 * ui.newPanel will be empty jQuery objects.
 * Note: Since the activate event is only fired on tab activation, it is
 * not fired for the initial tab when the tabs widget is created. If you
 * need a hook for widget creation use the create event.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* newTab Type: jQuery The tab that was just activated.
 * 	* oldTab Type: jQuery The tab that was just deactivated.
 * 	* newPanel Type: jQuery The panel that was just activated.
 * 	* oldPanel Type: jQuery The panel that was just deactivated.
 * 
 *
 * @was QTabs_ActivateEvent
 */
class TabsActivate extends EventBase
{
    const EVENT_NAME = 'tabsactivate';
}
