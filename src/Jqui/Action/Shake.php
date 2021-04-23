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
 * Class Shake
 *
 * Make a control shake left and right
 *
 * @package QCubed\Jqui\Action
 * @was QJQShakeAction
 */
class Shake extends ActionBase
{
    protected $strOptions = null;
    protected $intSpeed = null;

    /**
     * Shake constructor.
     * @param ControlBase $objControl
     * @param string $strOptions
     * @param int $intSpeed
     */
    public function __construct(ControlBase $objControl, $strOptions = "", $intSpeed = 1000)
    {
        $this->strOptions = Type::cast($strOptions, Type::STRING);
        $this->intSpeed = Type::cast($intSpeed, Type::INTEGER);

        parent::__construct($objControl, 'shake');
    }

    /**
     * @param ControlBase $objControl
     * @return string
     */
    public function renderScript(ControlBase $objControl)
    {
        return sprintf('$j("#%s_ctl").effect("shake", {%s}, %s);', $this->strControlId, $this->strOptions, $this->intSpeed);
    }
}
