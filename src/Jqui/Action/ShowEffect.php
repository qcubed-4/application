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
 * Class ShowEffect
 *
 * Show a control (if it's hidden) using additional visual effects.
 *
 * @package QCubed\Jqui\Action
 */
class ShowEffect extends ActionBase
{
    protected mixed $strOptions = null;
    protected mixed $intSpeed = null;

    public function __construct(ControlBase $objControl, string $strMethod = "default", ?string $strOptions = "", int $intSpeed = 1000)
    {
        $this->strOptions = Type::cast($strOptions, Type::STRING);
        $this->intSpeed = Type::cast($intSpeed, Type::INTEGER);

        parent::__construct($objControl, $strMethod);
    }

    public function renderScript(ControlBase $objControl): string
    {
        return sprintf('$j("#%s").show("%s", {%s}, %d);', $this->strControlId, $this->strMethod, $this->strOptions, $this->intSpeed);
    }
}
