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
 * Class ProgressbarComplete
 *
 * The abstract ProgressbarComplete class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered when the value of the progressbar reaches the maximum value.
 * 
 * 	* event Type: Event 
 * 	* ui Type: Object 
 * 
 * _Note: The ui object is empty but included for consistency with other
 * events._
 *
 * @was QProgressbar_CompleteEvent
 */
class ProgressbarComplete extends EventBase
{
    const EVENT_NAME = 'progressbarcomplete';
}
