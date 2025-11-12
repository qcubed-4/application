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
    use QCubed\Exception\InvalidCast;
    use QCubed\Type;

    /**
     * Class Transfer
     *
     * Transfer the border of a control to another control
     *
     * @package QCubed\Jqui\Action
     */
    class Transfer extends ActionBase
    {
        protected ?string $strTargetControlId = null;
        protected mixed $strOptions = null;
        protected mixed $intSpeed = null;

        /**
         * Transfer constructor.
         * @param ControlBase $objControl
         * @param ControlBase $objTargetControl
         * @param string|null $strOptions
         * @param int $intSpeed
         * @throws Caller
         * @throws InvalidCast
         */
        public function __construct(ControlBase $objControl, ControlBase $objTargetControl, ?string $strOptions = "", int $intSpeed = 1000)
        {
            $this->strTargetControlId = $objTargetControl->ControlId;

            $this->strOptions = Type::cast($strOptions, Type::STRING);
            $this->intSpeed = Type::cast($intSpeed, Type::INTEGER);

            parent::__construct($objControl, 'transfer');
        }

        /**
         * Renders the script for the specified control. This method should be implemented
         * to generate and return the specific script content tied to the given control instance.
         *
         * @param ControlBase $objControl The control instance for which the script is to be rendered
         *
         * @return mixed The rendered script content or any other relevant output as defined by the implementation
         */
        public function renderScript(ControlBase $objControl): string
        {
            return sprintf('$j("#%s").effect("transfer", {to: "#%s_ctl" %s}, %d);', $this->strControlId, $this->strTargetControlId, $this->strOptions, $this->intSpeed);
        }
    }
