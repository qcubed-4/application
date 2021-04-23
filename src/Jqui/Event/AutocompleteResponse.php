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
 * Class AutocompleteResponse
 *
 * The abstract AutocompleteResponse class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered after a search completes, before the menu is shown. Useful
 * for local manipulation of suggestion data, where a custom source
 * option callback is not required. This event is always triggered when a
 * search completes, even if the menu will not be shown because there are
 * no results or the Autocomplete is disabled.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* content Type: Array Contains the response data and can be modified
 * to change the results that will be shown. This data is already
 * normalized, so if you modify the data, make sure to include both value
 * and label properties for each item.
 * 
 *
 * @was QAutocomplete_ResponseEvent
 */
class AutocompleteResponse extends EventBase
{
    const EVENT_NAME = 'autocompleteresponse';
}
