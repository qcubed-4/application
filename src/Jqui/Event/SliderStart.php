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
 * Class SliderStart
 *
 * The abstract SliderStart class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered when the user starts sliding.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* handle Type: jQuery The jQuery object representing the handle being
 * moved.
 * 	* handleIndex Type: Number The numeric index of the handle being
 * moved.
 * 	* value Type: Number The current value of the slider.
 * 
 *
 * @was QSlider_StartEvent
 */
class SliderStart extends EventBase
{
    const EVENT_NAME = 'slidestart';
}
