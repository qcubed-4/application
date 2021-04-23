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
 * Class Highlight
 *
 * Highlight a control
 *
 * @package QCubed\Jqui\Action
 * @was QJQHighlightAction
 */
class Highlight extends ActionBase
{
    protected $strOptions = null;
    protected $strSpeed = null;

    /**
     * Highlight constructor.
     * @param ControlBase $objControl
     * @param string $strOptions
     * @param int $strSpeed
     */
    public function __construct(ControlBase $objControl, $strOptions = "", $strSpeed = 1000)
    {
        $this->strOptions = Type::cast($strOptions, Type::STRING);
        $this->strSpeed = Type::cast($strSpeed, Type::STRING);

        parent::__construct($objControl, 'highlight');
    }

    /**
     * @param ControlBase $objControl
     * @return string
     */
    public function renderScript(ControlBase $objControl)
    {
        return sprintf('$j("#%s").effect("highlight", {%s}, %s);', $this->strControlId, $this->strOptions, $this->strSpeed);
    }
}
