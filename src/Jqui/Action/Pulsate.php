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
 * Class Pulsate
 *
 * Pulsate the contents of a control
 *
 * @package QCubed\Jqui\Action
 */
class Pulsate extends ActionBase
{
    protected mixed $strOptions = null;
    protected mixed $intSpeed = null;

    /**
     * Pulsate constructor.
     * @param ControlBase $objControl
     * @param string|null $strOptions
     * @param int $intSpeed
     * @throws Caller
     * @throws InvalidCast
     */
    public function __construct(ControlBase $objControl, ?string $strOptions = "", int $intSpeed = 1000)
    {
        $this->strOptions = Type::cast($strOptions, Type::STRING);
        $this->intSpeed = Type::cast($intSpeed, Type::INTEGER);

        parent::__construct($objControl, 'pulsate');
    }

    /**
     * @param ControlBase $objControl
     * @return string
     */
    public function renderScript(ControlBase $objControl): string
    {
        return sprintf('$j("#%s").effect("pulsate", {%s}, %d);', $this->strControlId, $this->strOptions, $this->intSpeed);
    }
}