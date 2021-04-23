<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Event;

/**
 * Class DialogButton
 *
 * Special event to handle button clicks in dialogs.
 *
 * Add an action to this event to get a button click.
 * The action parameter will be the id of the button that was clicked.
 *
 * This current implementation is JQuery UI and relies on the ui javascript parameter being the button id.
 *
 * @usage    $dlg->addAction(new QDialog_ButtonEvent(), new QAjaxAction($this, 'ButtonClick'));
 * @was QDialog_ButtonEvent
 * @package QCubed\Event
 */
class DialogButton extends EventBase
{
    /** Event Name */
    const EVENT_NAME = 'QDialog_Button';
    const JS_RETURN_PARAM = 'ui'; // ends up being the button id
}
