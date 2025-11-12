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
     * Class Highlight
     *
     * Highlight control
     *
     * @package QCubed\Jqui\Action
     */
    class Highlight extends ActionBase
    {
        protected mixed $strOptions = null;
        protected mixed $strSpeed = null;

        /**
         * Highlight constructor.
         * @param ControlBase $objControl
         * @param string|null $strOptions
         * @param int $strSpeed
         * @throws Caller
         * @throws InvalidCast
         */
        public function __construct(ControlBase $objControl, ?string $strOptions = "", int $strSpeed = 1000)
        {
            $this->strOptions = Type::cast($strOptions, Type::STRING);
            $this->strSpeed = Type::cast($strSpeed, Type::STRING);

            parent::__construct($objControl, 'highlight');
        }

        /**
         * @param ControlBase $objControl
         * @return string
         */
        public function renderScript(ControlBase $objControl): string
        {
            return sprintf('$j("#%s").effect("highlight", {%s}, %s);', $this->strControlId, $this->strOptions, $this->strSpeed);
        }
    }