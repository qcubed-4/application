<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Action;

use QCubed\Control\DialogInterface;
use QCubed\Exception\Caller;
use QCubed\Control\ControlBase;

/**
 * Class ShowDialog
 *
 * Shows a Dialog
 * This is the JQuery UI alternative to show dialog
 *
 * @deprecated Create dialogs on the fly
 * @was QShowDialog
 * @package QCubed\Action
 */
class ShowDialog extends ActionBase
{
    /** @var null|string The JS to show the dialog */
    protected $strJavaScript = null;

    /**
     * Constructor
     *
     * @param DialogInterface $objControl
     *
     * @throws Caller
     */
    public function __construct(DialogInterface $objControl)
    {
        $strControlId = $objControl->getJqControlId();
        $this->strJavaScript = sprintf('jQuery("#%s").dialog("open");', $strControlId);
    }

    /**
     * Returns the JavaScript to be executed on the client side for opening/showing the dialog
     *
     * @param ControlBase $objControl
     *
     * @return null|string JS that will be run on the client side
     */
    public function renderScript(ControlBase $objControl)
    {
        return $this->strJavaScript;
    }
}
