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
 * Class SelectableSelected
 *
 * The abstract SelectableSelected class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered at the end of the select operation, on each element added to
 * the selection.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* selected Type: Element The selectable item that has been selected.
 * 
 *
 * @was QSelectable_SelectedEvent
 */
class SelectableSelected extends EventBase
{
    const EVENT_NAME = 'selectableselected';
}
