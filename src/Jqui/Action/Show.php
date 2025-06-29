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
 * Class Show
 *
 * Show a control (if it's hidden)
 *
 * @package QCubed\Jqui\Action
 */
class Show extends ActionBase
{
    /**
     * Show constructor.
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
        return sprintf('$j("#%s").show("%s");', $this->strControlId, $this->strMethod);
    }
}
