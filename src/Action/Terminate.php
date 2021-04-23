<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Action;

use QCubed\Control\ControlBase;

/**
 * Class Terminate
 *
 * Prevents the default action on an event.
 *
 * E.g. If you have a click action added to a label whose text is a link, clicking it will take the action
 * but also take you to the link pointed by the label. You can add a QTerminateAction after all QClickEvent
 * handlers and that will make sure that action handlers are triggered but the browser does not navigate
 * the user to the link pointed by the label
 *
 * @was QTerminateAction
 * @package QCubed\Action
 */
class Terminate extends ActionBase
{
    /**
     * Returns the JS for the browser
     *
     * @param ControlBase $objControl
     *
     * @return string JS to prevent the default action
     */
    public function renderScript(ControlBase $objControl)
    {
        return 'event.preventDefault();';
    }
}
