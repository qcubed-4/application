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
 * Class ServerControl
 *
 * Server control action is identical to server action, except
 * the handler for it is defined NOT in the form, but in a control.
 *
 * @was QServerControlAction
 * @package QCubed\Action
 */
class ServerControl extends Server
{
    /**
     * @param ControlBase $objControl Control where the action handler is defined
     * @param string $strMethodName Name of the method which acts as the action handler
     * @param mixed $mixCausesValidationOverride Override for CausesValidation (if needed)
     * @param string $strJsReturnParam Override for ActionParameter
     */
    public function __construct(
        ControlBase $objControl,
        $strMethodName,
        $mixCausesValidationOverride = null,
        $strJsReturnParam = ""
    ) {
        parent::__construct([$objControl, $strMethodName], $mixCausesValidationOverride,
            $strJsReturnParam);
    }
}

