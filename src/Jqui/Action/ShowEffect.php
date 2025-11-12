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
    use QCubed\Type;

    /**
     * Class ShowEffect
     *
     * Show a control (if it's hidden) using additional visual effects.
     *
     * @package QCubed\Jqui\Action
     */
    class ShowEffect extends ActionBase
    {
        protected mixed $strOptions = null;
        protected mixed $intSpeed = null;

        /**
         * Constructor for initializing the object with control and method.
         *
         * @param ControlBase $objControl The control base instance.
         * @param string $strMethod The method name to be associated with the object.
         * @throws Caller
         */
        public function __construct(ControlBase $objControl, string $strMethod = "default", ?string $strOptions = "", int $intSpeed = 1000)
        {
            $this->strOptions = Type::cast($strOptions, Type::STRING);
            $this->intSpeed = Type::cast($intSpeed, Type::INTEGER);

            parent::__construct($objControl, $strMethod);
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
            return sprintf('$j("#%s").show("%s", {%s}, %d);', $this->strControlId, $this->strMethod, $this->strOptions, $this->intSpeed);
        }
    }
