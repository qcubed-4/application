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
 * Class AutocompleteFocus
 *
 * The abstract AutocompleteFocus class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered when focus is moved to an item (not selecting). The default
 * action is to replace the text fields value with the value of the
 * focused item, though only if the event was triggered by a keyboard
 * interaction.
 * Canceling this event prevents the value from being updated, but does
 * not prevent the menu item from being focused.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* item Type: Object The focused item.
 * 
 *
 * @was QAutocomplete_FocusEvent
 */
class AutocompleteFocus extends EventBase
{
    const EVENT_NAME = 'autocompletefocus';
}
