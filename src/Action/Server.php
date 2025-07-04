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
use QCubed\Js;

/**
 * Class Server
 *
 * Server actions are handled through a full-page postback.
 *
 * @package                                        Actions
 * @property-read string $MethodName               Name of the associated action handling method
 * @property-read mixed $CausesValidationOverride An override for CausesValidation property (if supplied)
 * @property-read string $JsReturnParam            The parameter to be returned
 *                                                 (overrides the Control's ActionParameter)
 * @package QCubed\Action
 */
class Server extends ActionBase
{
    /** @var string|null Name of the method in the form to be called */
    protected ?string $strMethodName = null;
    /**
     * @var mixed A constant from the CausesValidation enumeration class
     *            It is set in the constructor via the corresponding argument
     */
    protected mixed $mixCausesValidationOverride;
    /** @var string An override for the Control's ActionParameter */
    protected string $strJsReturnParam;

    /**
     * Server constructor.
     *
     * @param string|null $strMethodName The method name which is to be assigned as the event handler
     *                                             (for the event being created). If blank, the whole page will just refresh.
     * @param string|null $mixCausesValidationOverride A constant from CausesValidation
     *                                             (or $this or an array of QControls)
     * @param string $strJsReturnParam The parameter to be returned when this event occurs
     * @throws Caller
     */
    public function __construct(
        mixed $strMethodName = null,
        mixed $mixCausesValidationOverride = null,
        string $strJsReturnParam = ""
    ) {
        global $_FORM;
        if (is_string($strMethodName)) {
            if (!method_exists($_FORM, $strMethodName)) {
                throw new Caller("If the method name is a string, the method must belong to the form.");
            }
        }
        elseif (is_array($strMethodName) && is_callable($strMethodName)) {
            // Assume the first item is a control or form
            if ($strMethodName[0] instanceof ControlBase) {
                $strMethodName = $strMethodName[0]->ControlId . ':' . $strMethodName[1];
            }
            elseif (!method_exists($_FORM, $strMethodName[1])) {
                throw new Caller("If a method name is a string, the method must belong to a form.");
            }
            else {
                $strMethodName = $strMethodName[1];
            }
        }
        elseif ($strMethodName !== null) {
            throw new Caller ("Unknown method.");
        }

        $this->strMethodName = $strMethodName;
        $this->mixCausesValidationOverride = $mixCausesValidationOverride;
        $this->strJsReturnParam = $strJsReturnParam;
    }

    /**
     * PHP Magic function to get the property values of an object of the class
     *
     * @param string $strName Name of the property
     *
     * @return mixed|null|string
     * @throws Caller
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'MethodName':
                return $this->strMethodName;
            case 'CausesValidationOverride':
                return $this->mixCausesValidationOverride;
            case 'JsReturnParam':
                return $this->strJsReturnParam;
            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * Determines the ActionParameter associated with the action and returns it
     *
     * @param ControlBase $objControl
     *
     * @return string The action parameter
     */
    protected function getActionParameter(ControlBase $objControl): string
    {
        if ($objActionParameter = $this->strJsReturnParam) {
            return $objActionParameter;
        }
        if ($objActionParameter = $this->objEvent->JsReturnParam) {
            return $objActionParameter;
        }
        $objActionParameter = $objControl->ActionParameter;
        if ($objActionParameter instanceof Js\Closure) {
            return '(' . $objActionParameter->toJsObject() . ').call(this)';
        }

        // Check if $objActionParameter is null and replace with an empty string if it is
        if ($objActionParameter === null) {
            $objActionParameter = '';
        }

        return "'" . addslashes($objActionParameter) . "'";
    }

    /**
     * Returns the JS which will be called on the client side
     * which will result in the event handler being called
     *
     * @param ControlBase $objControl
     *
     * @return string
     */
    public function renderScript(ControlBase $objControl): string
    {
        return sprintf("qc.pB('%s', '%s', '%s', %s);",
            $objControl->Form->FormId, $objControl->ControlId, addslashes(get_class($this->objEvent)),
            $this->getActionParameter($objControl));
    }
}
