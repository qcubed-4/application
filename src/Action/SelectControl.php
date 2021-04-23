<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Action;

use QCubed\Exception\Caller;
use QCubed\Control\ControlBase;
use QCubed\Project\Control\TextBox as QTextBox;

/**
 * Class SelectControl
 *
 * Selects contents inside a TextBox on the client-side/browser
 *
 * @was QSelectControlAction
 * @package QCubed\Action
 */
class SelectControl extends ActionBase
{
    /** @var null|string Control ID of the QTextBox which is to be selected */
    protected $strControlId = null;

    /**
     * Constructor
     *
     * @param QTextBox $objControl
     *
     * @throws Caller
     */
    public function __construct($objControl)
    {
        if (!($objControl instanceof QTextBox)) {
            throw new Caller('First parameter of constructor is expecting an object of type TextBox');
        }

        $this->strControlId = $objControl->ControlId;
    }

    /**
     * Returns the JavaScript to be executed on the client side
     *
     * @param ControlBase $objControl
     *
     * @return string JavaScript to be executed on the client side
     */
    public function renderScript(ControlBase $objControl)
    {
        return sprintf("qc.getW('%s').select();", $this->strControlId);
    }
}
