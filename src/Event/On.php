<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Event;

    use QCubed\Exception\Caller;

    /**
     * Class On
     *
     * Respond to any custom JavaScript event.
     *
     * Note, at one time, this event was required to react to bubbled events, but now every event
     * has a $strSelector to trigger on bubbled events.
     *
     * @param string $strEventName the name of the event i.e.: "click"
     * @param string $strSelector i.e.: "#myselector" ==> results in: $('#myControl').on("myevent","#myselector",function()...
     *
     * @package QCubed\Event
     */
    class On extends EventBase
    {
        /** @var string Name of the event */
        protected string $strEventName;

        /**
         * Constructs a new instance of the class.
         *
         * @param string $strEventName The name of the event.
         * @param int $intDelay Optional delay before the event is triggered. Defaults to 0.
         * @param string|null $strCondition Optional condition for the event. Defaults to null.
         * @param string|null $strSelector An optional selector associated with the event. Defaults to null.
         *
         * @return void
         * @throws \Exception
         * @throws Caller
         */
        public function __construct(string $strEventName, int $intDelay = 0, ?string $strCondition = null, ?string $strSelector = null)
        {
            $this->strEventName = $strEventName;
            if ($strSelector) {
                $strSelector = addslashes($strSelector);
                $this->strEventName .= '","' . $strSelector;
            }

            try {
                parent::__construct($intDelay, $strCondition, $strSelector);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Magic method to retrieve the value of a property.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property.
         * @throws Caller If the property does not exist or an error occurs during retrieval.
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case 'EventName':
                    return $this->strEventName;
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
