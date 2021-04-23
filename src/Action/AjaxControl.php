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
 * Class AjaxControl
 *
 * Ajax control action is identical to Ajax action, except
 * the handler for it is defined NOT on the form host, but on a QControl.
 *
 * @was QAjaxControlAction
 * @package QCubed\Action
 */
class AjaxControl extends Ajax
{
    /**
     * @param ControlBase $objControl Control where the action handler is defined
     * @param string $strMethodName Name of the action handler method
     * @param string $objWaitIconControl The wait icon to be implemented
     * @param null $mixCausesValidationOverride Override for CausesValidation (if needed)
     * @param string $strJsReturnParam Override for ActionParameter
     * @param boolean $blnAsync True to have the events for this action fire asynchronously
     */
    public function __construct(
        ControlBase $objControl,
        $strMethodName,
        $objWaitIconControl = 'default',
        $mixCausesValidationOverride = null,
        $strJsReturnParam = "",
        $blnAsync = false
    ) {
        parent::__construct([$objControl, $strMethodName], $objWaitIconControl,
            $mixCausesValidationOverride, $strJsReturnParam, $blnAsync);
    }
}
