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
 * Class SliderSlide
 *
 * The abstract SliderSlide class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered on every mouse move during slide. The value provided in the
 * event as ui.value represents the value that the handle will have as a
 * result of the current movement. Canceling the event will prevent the
 * handle from moving and the handle will continue to have its previous
 * value.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* handle Type: jQuery The jQuery object representing the handle being
 * moved.
 * 	* handleIndex Type: Number The numeric index of the handle being
 * moved.
 * 	* value Type: Number The value that the handle will move to if the
 * event is not canceled.
 * 	* values Type: Array An array of the current values of a
 * multi-handled slider.
 * 
 *
 * @was QSlider_SlideEvent
 */
class SliderSlide extends EventBase
{
    const EVENT_NAME = 'slide';
}
