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
 * Class TabsBeforeLoad
 *
 * The abstract TabsBeforeLoad class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered when a remote tab is about to be loaded, after the
 * beforeActivate event. Can be canceled to prevent the tab panel from
 * loading content; though the panel will still be activated. This event
 * is triggered just before the Ajax request is made, so modifications
 * can be made to ui.jqXHR and ui.ajaxSettings.
 * 
 * _Note: Although ui.ajaxSettings is provided and can be modified, some
 * of these properties have already been processed by jQuery. For
 * example, prefilters have been applied, data has been processed, and
 * type has been determined. The beforeLoad event occurs at the same
 * time, and therefore has the same restrictions, as the beforeSend
 * callback from jQuery.ajax()._
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* tab Type: jQuery The tab that is being loaded.
 * 	* panel Type: jQuery The panel which will be populated by the Ajax
 * response.
 * 	* jqXHR Type: jqXHR The jqXHR object that is requesting the content.
 * 	* ajaxSettings Type: Object The properties that will be used by
 * jQuery.ajax to request the content.
 * 
 *
 * @was QTabs_BeforeLoadEvent
 */
class TabsBeforeLoad extends EventBase
{
    const EVENT_NAME = 'tabsbeforeload';
}
