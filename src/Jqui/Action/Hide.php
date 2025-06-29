<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Jqui\Action;

use QCubed\Control\ControlBase;
use QCubed\Exception\Caller;

/**
 * Class Hide
 *
 * Hide control (if it's showing)
 *
 * @package QCubed\Jqui\Action
 */
class Hide extends ActionBase
{
    /**
     * Hide constructor.
     * @param ControlBase $objControl
     * @param string $strMethod
     * @throws Caller
     */
    public function __construct(ControlBase $objControl, string $strMethod = "slow")
    {
        parent::__construct($objControl, $strMethod);
    }

    /**
     * @param ControlBase $objControl
     * @return string
     */
    public function renderScript(ControlBase $objControl): string
    {
        return sprintf('$j("#%s").hide("%s");', $this->strControlId, $this->strMethod);
    }
}