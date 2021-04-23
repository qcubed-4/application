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

/**
 * Class HideDialog
 *
 * Hiding a JQuery UI Dialog (Dialog)
 *
 * @was QHideDialog
 * @package QCubed\Action
 * @deprecated Dialogs in general should be created on the fly. Also this implementation is very JQuery UI specific.
 */
class HideDialog extends ActionBase
{
    /** @var null|string JS to be executed on the client side for closing the dialog */
    protected $strJavaScript = null;

    /**
     * Constructor
     *
     * @param QDialog $objControl
     *
     * @throws \QCubed\Exception\Caller
     */
    public function __construct(\QCubed\Control\DialogInterface $objControl)
    {
        $strControlId = $objControl->getJqControlId();
        $this->strJavaScript = sprintf('jQuery("#%s").dialog("close");', $strControlId);
    }

    /**
     * Returns the JavaScript to be executed on the client side
     *
     * @param ControlBase $objControl
     *
     * @return null|string JavaScript to be executed on the client side
     */
    public function renderScript(ControlBase $objControl)
    {
        return $this->strJavaScript;
    }
}
