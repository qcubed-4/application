<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Jqui\Action;

    use QCubed\Control\ControlBase;
    use QCubed\Exception\Caller;

    /**
     * Class Toggle
     *
     * Toggle visibility of a control, using additional visual effects
     *
     * @package QCubed\Jqui\Action
     */
    class Toggle extends ActionBase
    {
        /**
         * Toggle constructor.
         * @param ControlBase $objControl
         * @param string $strMethod
         * @throws Caller
         */
        public function __construct(ControlBase $objControl, string $strMethod = "slow")
        {
            parent::__construct($objControl, $strMethod);
        }

        /**
         * @param ControlBase $objControl
         * @return string
         */
        public function renderScript(ControlBase $objControl): string
        {
            return sprintf('$j("#%s").toggle("%s");', $this->strControlId, $this->strMethod);
        }
    }