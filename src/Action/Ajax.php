<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Action;

    use QCubed\Control\WaitIcon;
    use QCubed\Exception\Caller;
    use QCubed\Js\Closure;
    use QCubed\Control\ControlBase;

    /**
     * Class Ajax
     *
     * The AjaxAction responds to events with ajax calls, which refresh a portion of a web page without reloading
     * the entire page. They generally are faster than server requests and give a better user experience.
     *
     * The QAjaxAction will associate a callback (strMethodName) with an event as part of an AddAction call. The callback will be
     * a method in the current QForm object. To associate a method that is part of a ControlBase, or any kind of a callback,
     * use a AjaxControlAction.
     *
     * The wait icon is a spinning gif file that can be overlay on top of the control to show that the control is in
     * a "loading" state. TODO: Convert this to a FontAwesome animated icon.
     *
     * mixCausesValidationOverride allows you to selectively say whether this action causes a validation, and on what subset of controls.
     *
     * strJsReturnParam is a javascript string that specifies what the action parameter will be, if you don't want the default.
     *
     * blnAsync lets you respond to the event asynchronously. Use care when setting this to true. Normally, qcubed will
     * put events in a queue and wait for each event to return a result before executing the next event. Most of the time,
     * the user experience is fine with this. However, there are times when events might be firing quickly and you do
     * not want to wait. However, your QFormState handler must be able to handle asynchronous events.
     * The default QFormStateHandler cannot do this, so you will need to use a different one.
     *
     * @property-read $MethodName Name of the (event-handler) method to be called
     *              the event handler - function containing the actual code for the Ajax action
     * @property-read WaitIcon $WaitIconControl          the waiting icon control for this Ajax Action
     * @property-read mixed     $CausesValidationOverride what kind of validation over-ride is to be implemented
     *              on this action.(See the QCausesValidation class and QFormBase class to understand in greater depth)
     * @property-read string    JsReturnParam             The line of JavaScript which would set the 'strParameter' value on the
     *              client-side when the action occurs!
     *              (see /assets/_core/php/examples/other_controls/js_return_param_example.php for example)
     * @property-read string    Id                        The Ajax Action ID for this action.
     * @package     Actions
     * @package QCubed\Action
     */
    class Ajax extends ActionBase
    {
        /** @var string|null Ajax Action ID */
        protected ?string $strId = null;
        /** @var string|null The event handler function name */
        protected ?string $strMethodName = null;

        /** @var string|WaitIcon|null Wait Icon to be used for this particular action */
        protected string|WaitIcon|null $objWaitIconControl;

        protected ?bool $blnAsync = false;
        /**
         * @var mixed what kind of validation over-ride is to be implemented
         *              (See the QCausesValidation class and QFormBase class to understand in greater depth)
         */
        protected mixed $mixCausesValidationOverride;
        /**
         * @var string the line of JavaScript which would set the 'strParameter' value on the
         *              Client-side when the action occurs!
         */
        protected mixed $strJsReturnParam = "";

        /**
         * AjaxAction constructor.
         * @param callable|string|null $strMethodName Name of the event handler function to be called, or a callable on the form or control
         * @param string|WaitIcon|null $objWaitIconControl Wait Icon for the action
         * @param mixed|null $mixCausesValidationOverride what kind of validation over-ride is to be implemented
         * @param string $strJsReturnParam the line of JavaScript which would set the 'strParameter' value on the
         *                                                      client-side when the action occurs!
         * @param boolean $blnAsync True to have the events for this action fire asynchronously.
         *                                                        Be careful when setting this to true. See class description.
         * @throws Caller
         */
        public function __construct(
            mixed $strMethodName = null,
            string|WaitIcon|null    $objWaitIconControl = 'default',
            mixed                   $mixCausesValidationOverride = null,
            mixed                   $strJsReturnParam = "",
            ?bool                   $blnAsync = false
        ) {
            global $_FORM;

            if (is_string($strMethodName)) {
                if (!method_exists($_FORM, $strMethodName)) {
                    throw new Caller("If a method name is a string, the method must belong to a form.");
                }
            }
            elseif (is_array($strMethodName) && is_callable($strMethodName)) {
                // Assume the first item is a control or form
                if ($strMethodName[0] instanceof ControlBase) {
                    if (!$strMethodName[0]->ControlId) {
                        throw new Caller('You must add a control to the form before giving it an action.');
                    }
                    $strMethodName = $strMethodName[0]->ControlId . ':' . $strMethodName[1];
                }
                elseif (!method_exists($_FORM, $strMethodName[1])) {
                    throw new Caller("If a method name is a string, the method must belong to a form.");
                }
                else {
                    $strMethodName = $strMethodName[1];
                }
            }
            else if ($strMethodName !== null) {
                throw new Caller ("Unknown method.");
            }

            $this->strId = null;
            $this->strMethodName = $strMethodName;
            $this->objWaitIconControl = $objWaitIconControl;
            $this->mixCausesValidationOverride = $mixCausesValidationOverride;
            $this->strJsReturnParam = $strJsReturnParam;
            $this->blnAsync = $blnAsync;
        }

        /**
         * Handles cloning of the object, resetting specific properties to ensure a unique instance.
         *
         * @return void
         */
        public function __clone(): void
        {
            $this->strId = null; //we are a fresh clone, let's reset the id and get our own later (in RenderScript)
        }

        /**
         * PHP Magic function to get the property values of a class object
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
                case 'WaitIconControl':
                    return $this->objWaitIconControl;
                case 'CausesValidationOverride':
                    return $this->mixCausesValidationOverride;
                case 'JsReturnParam':
                    return $this->strJsReturnParam;
                case 'Id':
                    return $this->strId;
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
         * Retrieves the action parameter associated with a control.
         *
         * @param ControlBase $objControl The control from which to retrieve the action parameter.
         *
         * @return string The JavaScript representation of the action parameter for the given control.
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
            if ($objActionParameter instanceof Closure) {
                return '(' . $objActionParameter->toJsObject() . ').call(this)';
            }

            // Check if $objActionParameter is null and replace with an empty string if it is
            if ($objActionParameter === null) {
                $objActionParameter = '';
            }

            return "'" . addslashes($objActionParameter) . "'";
        }

        /**
         * Returns the RenderScript script for the action.
         * The returned script is to be executed on the client side when the action is executed
         * (in this case qc.pA function is executed)
         *
         * @param ControlBase $objControl
         *
         * @return string
         */
        public function renderScript(ControlBase $objControl): string
        {
            $strWaitIconControlId = null;
            if ($this->strId == null) {
                $this->strId = $objControl->Form->generateAjaxActionId();
            }

            if ((gettype($this->objWaitIconControl) == 'string') && ($this->objWaitIconControl == 'default')) {
                if ($objControl->Form->DefaultWaitIcon) {
                    $strWaitIconControlId = $objControl->Form->DefaultWaitIcon->ControlId;
                }
            } else {
                if ($this->objWaitIconControl) {
                    $strWaitIconControlId = $this->objWaitIconControl->ControlId;
                }
            }

            return sprintf("qc.pA('%s', '%s', '%s#%s', %s, '%s', %s)",
                $objControl->Form->FormId, $objControl->ControlId, addslashes(get_class($this->objEvent)), $this->strId,
                $this->getActionParameter($objControl), $strWaitIconControlId, $this->blnAsync ? 'true' : 'false');
        }
    }