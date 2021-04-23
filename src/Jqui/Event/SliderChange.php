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
 * Class SliderChange
 *
 * The abstract SliderChange class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered after the user slides a handle, if the value has changed; or
 * if the value is changed programmatically via the value method.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* handle Type: jQuery The jQuery object representing the handle that
 * was changed.
 * 	* handleIndex Type: Number The numeric index of the handle that was
 * moved.
 * 	* value Type: Number The current value of the slider.
 * 
 *
 * @was QSlider_ChangeEvent
 */
class SliderChange extends EventBase
{
    const EVENT_NAME = 'slidechange';
}
