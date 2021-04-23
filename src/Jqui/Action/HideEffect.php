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
 * Class HideEffect
 *
 * Hide a control, using additional visual effects.
 *
 * @package QCubed\Jqui\Action
 * @was QJQHideEffectAction
 */
class HideEffect extends ActionBase
{
    protected $strOptions = null;
    protected $intSpeed = null;

    /**
     * HideEffect constructor.
     * @param ControlBase $objControl
     * @param string $strMethod
     * @param string $strOptions
     * @param int $intSpeed
     */
    public function __construct(ControlBase $objControl, $strMethod = "blind", $strOptions = "", $intSpeed = 1000)
    {
        $this->strOptions = Type::cast($strOptions, Type::STRING);
        $this->intSpeed = Type::cast($intSpeed, Type::INTEGER);

        parent::__construct($objControl, $strMethod);
    }

    /**
     * @param ControlBase $objControl
     * @return string
     */
    public function renderScript(ControlBase $objControl)
    {
        return sprintf('$j("#%s").hide("%s", {%s}, %d);', $this->strControlId, $this->strMethod, $this->strOptions, $this->intSpeed);
    }
}
