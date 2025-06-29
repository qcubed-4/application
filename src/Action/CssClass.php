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
 * Class CssClass
 *
 * Can add or remove an extra CSS class from a control.
 * It Should be used mostly for temporary purposes such as 'hovering' over a control
 *
 * @package QCubed\Action
 */
class CssClass extends ActionBase
{
    /** @var null|string The CSS class to be added to the control */
    protected ?string $strTemporaryCssClass = null;
    /** @var bool Should the CSS class be applied by removing the previous one? */
    protected ?bool $blnOverride = false;

    /**
     * Constructor
     *
     * @param string|null $strTemporaryCssClass The temporary class to be added to the control
     *                                          If null, it will reset the CSS classes to the previous set
     * @param bool $blnOverride Should the previously set classes be removed (true) or not (false)?
     *                                          This will not reset the CSS class on the server side
     */
    public function __construct(?string $strTemporaryCssClass = null, ?bool $blnOverride = false)
    {
        $this->strTemporaryCssClass = $strTemporaryCssClass;
        $this->blnOverride = $blnOverride;
    }

    /**
     * Returns the JavaScript to be executed on the client side
     *
     * @param ControlBase $objControl
     *
     * @return string The JavaScript to be executed on the client side
     */
    public function renderScript(ControlBase $objControl): string
    {
        // Specified a Temporary CSS Class to use?
        if (is_null($this->strTemporaryCssClass)) {
            // No Temporary CSS Class -- use the Control's already-defined one
            return sprintf("qc.getC('%s').className = '%s';", $objControl->ControlId, $objControl->CssClass);
        } else {
            // Are we overriding or are we displaying this temporary CSS class outright?
            if ($this->blnOverride) {
                // Overriding
                return sprintf("qc.getC('%s').className = '%s %s';", $objControl->ControlId, $objControl->CssClass,
                    $this->strTemporaryCssClass);
            } else {
                // Use Temp CSS Class Outright
                return sprintf("qc.getC('%s').className = '%s';", $objControl->ControlId, $this->strTemporaryCssClass);
            }
        }
    }
}
