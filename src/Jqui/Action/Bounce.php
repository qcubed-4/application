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
 * Class Bounce
 *
 * Make a control bounce up and down.
 *
 * @package QCubed\Jqui\Action
 * @was QJQBounceAction
 */
class Bounce extends ActionBase
{
    protected $strOptions = null;
    protected $strSpeed = null;

    public function __construct(ControlBase $objControl, $strOptions = "", $strSpeed = 1000)
    {
        $this->strOptions = Type::cast($strOptions, Type::STRING);
        $this->strSpeed = Type::cast($strSpeed, Type::STRING);

        parent::__construct($objControl, 'bounce');
    }

    /**
     * @param ControlBase $objControl
     * @return string
     */
    public function renderScript(ControlBase $objControl)
    {
        return sprintf('$j("#%s_ctl").effect("bounce", {%s}, %s);', $this->strControlId, $this->strOptions, $this->strSpeed);
    }
}
