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
 * Class Size
 *
 * Resize a control
 *
 * @package QCubed\Jqui\Action
 * @was QJQSizeAction
 */
class Size extends ActionBase
{
    protected $strOptions = null;
    protected $intSpeed = null;

    /**
     * Size constructor.
     * @param ControlBase $objControl
     * @param $strOptions
     * @param int $intSpeed
     */
    public function __construct(ControlBase $objControl, $strOptions, $intSpeed = 1000)
    {
        $this->strOptions = Type::cast($strOptions, Type::STRING);
        $this->intSpeed = Type::cast($intSpeed, Type::INTEGER);

        parent::__construct($objControl, 'size');
    }

    /**
     * @param ControlBase $objControl
     * @return string
     */
    public function renderScript(ControlBase $objControl)
    {
        return sprintf('$j("#%s").effect("size", {%s}, %d);', $this->strControlId, $this->strOptions, $this->intSpeed);
    }
}
