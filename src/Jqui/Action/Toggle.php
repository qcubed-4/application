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
 * Class Toggle
 *
 * Toggle visibility of a control, using additional visual effects
 *
 * @package QCubed\Jqui\Action
 * @was QJQToggleAction
 */
class Toggle extends ActionBase
{
    /**
     * Toggle constructor.
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
        return sprintf('$j("#%s").toggle("%s");', $this->strControlId, $this->strMethod);
    }
}
