<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Action;

use QCubed\Control\Calendar;
use QCubed\Exception\Caller;
use QCubed\Control\ControlBase;

/**
 * Class HideCalendar
 *
 * Hides a calendar control
 *
 * @was QHideCalendarAction
 * @package QCubed\Action
 */
class HideCalendar extends ActionBase
{
    /** @var null|string Control ID of the calendar control */
    protected $strControlId = null;

    /**
     * Constructor
     * @param Calendar $calControl
     *
     * @throws Caller
     */
    public function __construct($calControl)
    {
        if (!($calControl instanceof Calendar)) {
            throw new Caller('First parameter of constructor is expecting an object of type QCalendar');
        }
        $this->strControlId = $calControl->ControlId;
    }

    /**
     * Returns the JavaScript to be executed on the client side
     * @param ControlBase $objControl
     *
     * @return string JavaScript to be executed on the client side
     */
    public function renderScript(ControlBase $objControl)
    {
        return sprintf("qc.getC('%s').hideCalendar();", $this->strControlId);
    }
}
