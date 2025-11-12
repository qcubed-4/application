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
     * Class BlurControl
     *
     * Blurs (JS blur, not visual blur) a control on the server side (i.e., removes focus from that control)
     *
     * @package QCubed\Action
     */
    class BlurControl extends ActionBase
    {
        /** @var null|string Control ID of the control from which focus has to be removed */
        protected ?string $strControlId = null;

        /**
         * Constructor
         *
         * @param ControlBase $objControl
         *
         */
        public function __construct(ControlBase $objControl)
        {

            $this->strControlId = $objControl->ControlId;
        }

        /**
         * Returns the JavaScript to be executed on the client side
         *
         * @param ControlBase $objControl
         *
         * @return string JavaScript to be executed on the client side
         */
        public function renderScript(ControlBase $objControl): string
        {
            return sprintf("qc.getW('%s').blur();", $this->strControlId);
        }
    }
