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
use QCubed\Exception\Caller;

/**
 * Class AjaxControl
 *
 * Ajax control action is identical to Ajax action, except
 * the handler for it is defined NOT on the form host, but on a QControl.
 *
 * @package QCubed\Action
 */
class AjaxControl extends Ajax
{
    /**
     * @param ControlBase $objControl Control where the action handler is defined
     * @param string $strMethodName Name of the action handler method
     * @param string|null $objWaitIconControl The wait icon to be implemented
     * @param null $mixCausesValidationOverride Override for CausesValidation (if needed)
     * @param string $strJsReturnParam Override for ActionParameter
     * @param boolean $blnAsync True to have the events for this action fire asynchronously
     * @throws Caller
     */
    public function __construct(
        ControlBase $objControl,
        string $strMethodName,
        ?string $objWaitIconControl = 'default',
        mixed $mixCausesValidationOverride = null,
        mixed $strJsReturnParam = "",
        ?bool $blnAsync = false
    ) {
        parent::__construct([$objControl, $strMethodName], $objWaitIconControl,
            $mixCausesValidationOverride, $strJsReturnParam, $blnAsync);
    }
}
