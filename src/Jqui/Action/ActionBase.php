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
 * Class ActionBase
 *
 * Base class for all jQuery-based effects.
 *
 * @package QCubed\Jqui\Action
 * @was QJQAction
 */
abstract class ActionBase extends \QCubed\Action\ActionBase
{
    /** @var null|string  */
    protected $strControlId = null;
    /** @var string|null  */
    protected $strMethod = null;

    /**
     * ActionBase constructor.
     * @param ControlBase $objControl
     * @param $strMethod
     */
    protected function __construct(ControlBase $objControl, $strMethod)
    {
        $this->strControlId = $objControl->ControlId;
        $this->strMethod = Type::cast($strMethod, Type::STRING);
        $this->setJavaScripts($objControl);
    }

    /**
     * @param ControlBase $objControl
     */
    private function setJavaScripts(ControlBase $objControl)
    {
        $objControl->addJavascriptFile(QCUBED_JQUI_JS);
    }
}
