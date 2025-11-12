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
     * Class Bounce
     *
     * Make a control bounce up and down.
     *
     * @package QCubed\Jqui\Action
     */
    class Bounce extends ActionBase
    {
        protected mixed $strOptions = null;
        protected mixed $strSpeed = null;

        /**
         * Constructor method for initializing the object.
         *
         * @param ControlBase $objControl The control base object.
         * @param string|null $strOptions Optional string options, defaults to an empty string.
         * @param int $strSpeed Optional speed value, defaults to 1000.
         * @throws Caller
         * @throws InvalidCast
         */
        public function __construct(ControlBase $objControl, ?string $strOptions = "", int $strSpeed = 1000)
        {
            $this->strOptions = Type::cast($strOptions, Type::STRING);
            $this->strSpeed = Type::cast($strSpeed, Type::STRING);

            parent::__construct($objControl, 'bounce');
        }

        /**
         * @param ControlBase $objControl
         * @return string
         */
        public function renderScript(ControlBase $objControl): string
        {
            return sprintf('$j("#%s_ctl").effect("bounce", {%s}, %s);', $this->strControlId, $this->strOptions, $this->strSpeed);
        }
    }