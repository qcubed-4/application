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
 * Class AutocompleteSelect
 *
 * The abstract AutocompleteSelect class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered when an item is selected from the menu. The default action
 * is to replace the text fields value with the value of the selected
 * item.
 * Canceling this event prevents the value from being updated, but does
 * not prevent the menu from closing.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* item Type: Object An Object with label and value properties for the
 * selected option.
 * 
 *
 * @was QAutocomplete_SelectEvent
 */
class AutocompleteSelect extends EventBase
{
    const EVENT_NAME = 'autocompleteselect';
}
