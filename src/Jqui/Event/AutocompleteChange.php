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
 * Class AutocompleteChange
 *
 * The abstract AutocompleteChange class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered when the field is blurred, if the value has changed.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* item Type: Object The item selected from the menu, if any.
 * Otherwise the property is null.
 * 
 *
 * @was QAutocomplete_ChangeEvent
 */
class AutocompleteChange extends EventBase
{
    const EVENT_NAME = 'autocompletechange';
}
