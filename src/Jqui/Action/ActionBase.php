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
     * Class ActionBase
     *
     * Base class for all jQuery-based effects.
     *
     * @package QCubed\Jqui\Action
     */
    abstract class ActionBase extends \QCubed\Action\ActionBase
    {
        /** @var string|null */
        protected ?string $strControlId = '';
        /** @var string|null */
        protected ?string $strMethod = '';

        /**
         * Constructor for initializing the object with control and method.
         *
         * @param ControlBase $objControl The control base instance.
         * @param string $strMethod The method name to be associated with the object.
         * @throws Caller
         */
        protected function __construct(ControlBase $objControl, string $strMethod)
        {
            $this->strControlId = $objControl->ControlId;
            $this->strMethod = Type::cast($strMethod, Type::STRING);
            $this->setJavaScripts($objControl);
        }

        /**
         * @param ControlBase $objControl
         * @throws Caller
         */
        private function setJavaScripts(ControlBase $objControl): void
        {
            $objControl->addJavascriptFile(QCUBED_JQUI_JS);
        }
    }