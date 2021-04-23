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
use QCubed\Js;
use QCubed\Control\ControlBase;


/**
 * Class Alert
 *
 * Displays an alert to the user
 *
 * @was QAlertAction
 * @package QCubed\Action
 */
class Alert extends ActionBase
{
    /** @var string Message to be shown as the alert */
    protected $strMessage;

    /**
     * Constructor
     *
     * @param string $strMessage Message to be shown as the alert
     */
    public function __construct($strMessage)
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
    public function __get($strName)
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
     * Returns the JavaScript to be executed on the client side
     *
     * @param ControlBase $objControl
     *
     * @return string
     */
    public function renderScript(ControlBase $objControl)
    {
        $strMessage = Js\Helper::toJsObject($this->strMessage);

        return sprintf("alert(%s);", $strMessage);
    }
}
