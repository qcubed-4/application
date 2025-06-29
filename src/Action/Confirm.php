<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Action;

use QCubed\Exception\Caller;
use QCubed\Control\ControlBase;
use QCubed\Js;

/**
 * Class Confirm
 *
 * This action works as an if-else stopper for another action.
 * This action should be added to a control with the same event type before another action of that event type
 * Doing so brings up a JavaScript Confirmation box in front of the user.
 * If the user clicks on 'OK', then the next action is executed (and any actions after that as well)
 * If the user clicks on 'Cancel', then the next /rest of the action(s) is not executed
 *
 * @package QCubed\Action
 */
class Confirm extends ActionBase
{
    /** @var string Message to be shown to the user on the confirmation prompt */
    protected string $strMessage;

    /**
     * Constructor of the function
     * @param string $strMessage Message which is to be set as the confirmation prompt message
     */
    public function __construct(string $strMessage)
    {
        $this->strMessage = $strMessage;
    }

    /**
     * PHP Magic function to get the property values of an object of the class
     *
     * @param string $strName Name of the property
     *
     * @return mixed|null|string
     * @throws Caller
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'Message':
                return $this->strMessage;
            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * Renders a JavaScript confirmation script for the provided control.
     *
     * @param ControlBase $objControl The control for which the script is being rendered.
     * @return string A JavaScript confirmation script string.
     */
    public function renderScript(ControlBase $objControl): string
    {
        $strMessage = Js\Helper::toJsObject($this->strMessage);

        return sprintf("if (!confirm(%s)) return false;", $strMessage);
    }
}
