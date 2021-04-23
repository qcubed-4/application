<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Exception;

/**
 * Class CrossScripting
 *
 * Called when the textbox fails CrossScripting checks
 *
 * @was QCrossScriptingException
 * @package QCubed\Action
 */
class CrossScripting extends Caller
{
    /**
     * Constructor
     * @param string $strControlId Control ID of the control for which it being called
     */
    public function __construct($strControlId)
    {
        parent::__construct("Cross Scripting Violation: Potential cross script injection in Control \"" .
            $strControlId . "\"\r\nTo allow any input on this TextBox control, set CrossScripting to QCrossScripting::Allow. Also consider QCrossScripting::HTMLPurifier.",
            2);
    }
}