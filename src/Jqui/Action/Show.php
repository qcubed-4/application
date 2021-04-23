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

/**
 * Class Show
 *
 * Show a control (if it's hidden)
 *
 * @package QCubed\Jqui\Action
 * @was QJQShowAction
 */
class Show extends ActionBase
{
    /**
     * Show constructor.
     * @param ControlBase $objControl
     * @param string $strMethod
     */
    public function __construct(ControlBase $objControl, $strMethod = "slow")
    {
        parent::__construct($objControl, $strMethod);
    }

    /**
     * @param ControlBase $objControl
     * @return string
     */
    public function renderScript(ControlBase $objControl)
    {
        return sprintf('$j("#%s").show("%s");', $this->strControlId, $this->strMethod);
    }
}
