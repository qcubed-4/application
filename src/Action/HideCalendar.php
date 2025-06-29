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
use QCubed\Control\ControlBase;

/**
 * Class HideCalendar
 *
 * Hides calendar control
 *
 * @package QCubed\Action
 */
class HideCalendar extends ActionBase
{
    /** @var null|string Control ID of the calendar control */
    protected ?string $strControlId = null;

    /**
     * Constructor
     * @param Calendar $calControl
     *
     */
    public function __construct(Calendar $calControl)
    {
        $this->strControlId = $calControl->ControlId;
    }

    /**
     * Returns the JavaScript to be executed on the client side
     * @param ControlBase $objControl
     *
     * @return string JavaScript to be executed on the client side
     */
    public function renderScript(ControlBase $objControl): string
    {
        return sprintf("qc.getC('%s').hideCalendar();", $this->strControlId);
    }
}
