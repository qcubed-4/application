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
 * Class SpinnerSpin
 *
 * The abstract SpinnerSpin class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered during increment/decrement (to determine direction of spin
 * compare current value with ui.value).
 * Can be canceled, preventing the value from being updated.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* value Type: Number The new value to be set, unless the event is
 * cancelled.
 * 
 *
 * @was QSpinner_SpinEvent
 */
class SpinnerSpin extends EventBase
{
    const EVENT_NAME = 'spin';
}
