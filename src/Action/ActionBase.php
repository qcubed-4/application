<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Action;

    use QCubed\Event\EventBase;
    use QCubed\Exception\Caller;
    use QCubed\ObjectBase;
    use QCubed\Type;
    use QCubed\Control\ControlBase;

    /**
     * Base class for all other Actions.
     *
     * @package Actions
     * @property EventBase $Event Any Event derived class instance
     */
    abstract class ActionBase extends ObjectBase
    {
        /**
         * Renders the script for the specified control. This method should be implemented
         * to generate and return the specific script content tied to the given control instance.
         *
         * @param ControlBase $objControl The control instance for which the script is to be rendered
         *
         * @return mixed The rendered script content or any other relevant output as defined by the implementation
         */
        abstract public function renderScript(ControlBase $objControl): mixed;

        /** @var EventBase|null Event object which will fire this action */

        protected ?EventBase $objEvent = null;

        /**
         * PHP Magic function to set the property values of an object of the class
         * In this case, we only have the 'Event' property to be set
         *
         * @param string $strName Name of the property
         * @param mixed $mixValue Value of the property
         *
         * @return void
         * @throws Caller
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case 'Event':
                    $this->objEvent = Type::cast($mixValue, '\QCubed\Event\EventBase');
                    break;

                default:
                    try {
                        parent::__set($strName, $mixValue);
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
            }
        }

        /**
         * PHP Magic function to get the property values of an object of the class
         * In this case, we only have 'Event' property to be set
         *
         * @param string $strName Name of the property
         *
         * @return mixed
         * @throws Caller
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case 'Event':
                    return $this->objEvent;
                default:
                    try {
                        return parent::__get($strName);
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
            }
        }
    }
