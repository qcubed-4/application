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
 * Class Hide
 *
 * Hide a control (if it's showing)
 *
 * @package QCubed\Jqui\Action
 * @was QJQHideAction
 */
class Hide extends ActionBase
{
    /**
     * Hide constructor.
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
        return sprintf('$j("#%s").hide("%s");', $this->strControlId, $this->strMethod);
    }
}
