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
     * Class FocusControl
     *
     * Puts focus on a Control (on the client side/browser)
     *
     * @package QCubed\Action
     */
    class FocusControl extends ActionBase
    {
        /** @var null|string Control ID of the control on which focus is to be put */
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
            // for firefox focus is special when in a blur or in a focusout event
            // http://stackoverflow.com/questions/7046798/jquery-focus-fails-on-firefox/7046837#7046837
            return sprintf("setTimeout(function(){qc.getW('%s').focus();},0);", $this->strControlId);
        }
    }
