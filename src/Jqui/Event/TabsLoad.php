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
 * Class TabsLoad
 *
 * The abstract TabsLoad class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered after a remote tab has been loaded.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* tab Type: jQuery The tab that was just loaded.
 * 	* panel Type: jQuery The panel which was just populated by the Ajax
 * response.
 * 
 *
 * @was QTabs_LoadEvent
 */
class TabsLoad extends EventBase
{
    const EVENT_NAME = 'tabsload';
}
