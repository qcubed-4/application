<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Action;

use QCubed\Control\ControlBase;
use QCubed\Project\Jqui\Dialog;
use QCubed\Control\DialogInterface;

/**
 * Class HideDialog
 *
 * Hiding a JQuery UI Dialog (Dialog)
 *
 * @was QHideDialog
 * @package QCubed\Action
 * @deprecated Dialogs in general should be created on the fly. Also, this implementation is very JQuery UI-specific.
 */
class HideDialog extends ActionBase
{
    /** @var null|string JS to be executed on the client side for closing the dialog */
    protected ?string $strJavaScript = null;

    /**
     * Constructor
     *
     * @param Dialog $objControl
     *
     */
    public function __construct(DialogInterface $objControl)
    {
        $strControlId = $objControl->getJqControlId();
        $this->strJavaScript = sprintf('jQuery("#%s").dialog("close");', $strControlId);
    }

    /**
     * Returns the JavaScript to be executed on the client side
     *
     * @param ControlBase $objControl
     *
     * @return string|null JavaScript to be executed on the client side
     */
    public function renderScript(ControlBase $objControl): ?string
    {
        return $this->strJavaScript;
    }
}
