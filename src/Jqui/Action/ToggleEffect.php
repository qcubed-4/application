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
use QCubed\Exception\InvalidCast;
use QCubed\Type;

/**
 * Class ToggleEffect
 *
 * Toggle visibility of a control, using additional visual effects
 *
 * @package QCubed\Jqui\Action
 */
class ToggleEffect extends ActionBase
{
    protected mixed $strOptions = null;
    protected mixed $intSpeed = null;

    /**
     * ToggleEffect constructor.
     * @param ControlBase $objControl
     * @param string $strMethod
     * @param string|null $strOptions
     * @param int $intSpeed
     * @throws Caller
     * @throws InvalidCast
     */
    public function __construct(ControlBase $objControl, string $strMethod = "slow", ?string $strOptions = "", int $intSpeed = 1000)
    {
        $this->strOptions = Type::cast($strOptions, Type::STRING);
        $this->intSpeed = Type::cast($intSpeed, Type::INTEGER);

        parent::__construct($objControl, $strMethod);
    }

    /**
     * @param ControlBase $objControl
     * @return string
     */
    public function renderScript(ControlBase $objControl): string
    {
        return sprintf('$j("#%s").toggle("%s", {%s}, %d);', $this->strControlId, $this->strMethod, $this->strOptions, $this->intSpeed);
    }
}