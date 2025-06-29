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
use QCubed\Type;

/**
 * Class ToggleEnable
 *
 * Toggle the 'enabled' status of a control
 * NOTE: It does not change the Enabled property on the server side
 *
 * @package QCubed\Action
 */
class ToggleEnable extends ActionBase
{
    /** @var null|string Control ID of the control to be Enabled/Disabled */
    protected ?string $strControlId = null;
    /** @var boolean|null Enforce the Enabling or Disabling action */
    protected mixed $blnEnabled = null;

    /**
     * @param ControlBase $objControl
     * @param bool|null $blnEnabled
     *
     * @throws Caller
     */
    public function __construct(ControlBase $objControl, ?bool $blnEnabled = null)
    {
        $this->strControlId = $objControl->ControlId;

        if (!is_null($blnEnabled)) {
            $this->blnEnabled = Type::cast($blnEnabled, Type::BOOLEAN);
        }
    }

    /**
     * Returns the JavaScript to be executed on the client side
     *
     * @param ControlBase $objControl
     *
     * @return string Client side JS
     */
    public function renderScript(ControlBase $objControl): string
    {
        if ($this->blnEnabled === true) {
            $strEnableOrDisable = 'enable';
        } else {
            if ($this->blnEnabled === false) {
                $strEnableOrDisable = 'disable';
            } else {
                $strEnableOrDisable = '';
            }
        }

        return sprintf("qc.getW('%s').toggleEnabled('%s');", $this->strControlId, $strEnableOrDisable);
    }
}
