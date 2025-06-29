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
use QCubed\Control\Calendar;

/**
 * Class ShowCalendar
 *
 * Shows a Calendar Control. Probably should be deprecated in favor of calendar plugins from CSS and JavaScript frameworks.
 *
 * @was QShowCalendarAction
 * @package QCubed\Action
 */
class ShowCalendar extends ActionBase
{
    /** @var null|string Control ID of the calendar */
    protected ?string $strControlId = null;

    /**
     * @param ControlBase $calControl
     *
     * @throws Caller
     */
    public function __construct(ControlBase $calControl)
    {
        if (!($calControl instanceof Calendar)) {
            throw new Caller('The First parameter of constructor is expecting an object of type QCalendar');
        }
        $this->strControlId = $calControl->ControlId;
    }

    /**
     * Returns the JavaScript to be executed on the client side
     * @param ControlBase $objControl
     *
     * @return string JavaScript to be executed on the client side
     */
    public function RenderScript(ControlBase $objControl): string
    {
        return sprintf("qc.getC('%s').showCalendar();", $this->strControlId);
    }
}
