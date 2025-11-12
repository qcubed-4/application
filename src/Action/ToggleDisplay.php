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
     * Class ToggleDisplay
     *
     * Toggle the Display of control
     *
     * @package QCubed\Action
     */
    class ToggleDisplay extends ActionBase
    {
        /** @var string|null Control ID of the control */
        protected ?string $strControlId = null;
        /** @var boolean|null Enforce 'show' or 'hide' action */
        protected mixed $blnDisplay = null;

        /**
         * @param ControlBase $objControl
         * @param bool|null $blnDisplay
         *
         * @throws Caller
         */
        public function __construct(ControlBase $objControl, mixed $blnDisplay = null)
        {
            $this->strControlId = $objControl->ControlId;

            if (!is_null($blnDisplay)) {
                $this->blnDisplay = Type::cast($blnDisplay, Type::BOOLEAN);
            }
        }

        /**
         * Returns the JavaScript to be executed on the client side
         *
         * @param ControlBase $objControl
         *
         * @return string Returns the JavaScript to be executed on the client side
         */
        public function renderScript(ControlBase $objControl): string
        {
            if ($this->blnDisplay === true) {
                $strShowOrHide = 'show';
            } else {
                if ($this->blnDisplay === false) {
                    $strShowOrHide = 'hide';
                } else {
                    $strShowOrHide = '';
                }
            }

            return sprintf("qc.getW('%s').toggleDisplay('%s');",
                $this->strControlId, $strShowOrHide);
        }
    }
