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
use QCubed\Type;

/**
 * Class Transfer
 *
 * Transfer the border of a control to another control
 *
 * @package QCubed\Jqui\Action
 * @was QJQTransferAction
 */
class Transfer extends ActionBase
{
    protected $strTargetControlId = null;
    protected $strOptions = null;
    protected $intSpeed = null;

    /**
     * Transfer constructor.
     * @param ControlBase $objControl
     * @param ControlBase $objTargetControl
     * @param string $strOptions
     * @param int $intSpeed
     */
    public function __construct(ControlBase $objControl, ControlBase $objTargetControl, $strOptions = "", $intSpeed = 1000)
    {
        $this->strTargetControlId = $objTargetControl->ControlId;

        $this->strOptions = Type::cast($strOptions, Type::STRING);
        $this->intSpeed = Type::cast($intSpeed, Type::INTEGER);

        parent::__construct($objControl, 'transfer');
    }

    /**
     * @param ControlBase $objControl
     * @return string
     */
    public function renderScript(ControlBase $objControl)
    {
        return sprintf('$j("#%s").effect("transfer", {to: "#%s_ctl" %s}, %d);', $this->strControlId, $this->strTargetControlId, $this->strOptions, $this->intSpeed);
    }
}
