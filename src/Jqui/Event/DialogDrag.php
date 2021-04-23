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
 * Class DialogDrag
 *
 * The abstract DialogDrag class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
 * Triggered while the dialog is being dragged.
 * 
 * 	* event Type: Event 
 * 
 * 	* ui Type: Object 
 * 
 * 	* position Type: Object The current CSS position of the dialog.
 * 	* offset Type: Object The current offset position of the dialog.
 * 
 *
 * @was QDialog_DragEvent
 */
class DialogDrag extends EventBase
{
    const EVENT_NAME = 'dialogdrag';
}
