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
 * Class AccordionBeforeActivate
 *
 * The abstract AccordionBeforeActivate class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered directly before a panel is activated. Can be canceled to
 * prevent the panel from activating. If the accordion is currently
 * collapsed, ui.oldHeader and ui.oldPanel will be empty jQuery objects.
 * If the accordion is collapsing, ui.newHeader and ui.newPanel will be
 * empty jQuery objects.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* newHeader Type: jQuery The header that is about to be activated.
 * 	* oldHeader Type: jQuery The header that is about to be deactivated.
 * 	* newPanel Type: jQuery The panel that is about to be activated.
 * 	* oldPanel Type: jQuery The panel that is about to be deactivated.
 * 
 *
 * @was QAccordion_BeforeActivateEvent
 */
class AccordionBeforeActivate extends EventBase
{
    const EVENT_NAME = 'accordionbeforeactivate';
}
