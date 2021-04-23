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
 * Class AccordionCreate
 *
 * The abstract AccordionCreate class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered when the accordion is created. If the accordion is
 * collapsed, ui.header and ui.panel will be empty jQuery objects.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* header Type: jQuery The active header.
 * 	* panel Type: jQuery The active panel.
 * 
 *
 * @was QAccordion_CreateEvent
 */
class AccordionCreate extends EventBase
{
    const EVENT_NAME = 'accordioncreate';
}
