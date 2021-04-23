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
 * Respond to any custom javascript event.
 *
 * Note, at one time, this event was required to react to bubbled events, but now every event
 * has a $strSelector to trigger on bubbled events.
 *
 * @param string $strEventName the name of the event i.e.: "click"
 * @param string $strSelector i.e.: "#myselector" ==> results in: $('#myControl').on("myevent","#myselector",function()...
 * @was QOnEvent
 * @package QCubed\Event
 */
class On extends EventBase
{
    /** @var string Name of the event */
    protected $strEventName;

    /**
     * Constructor
     * @param int $strEventName
     * @param int $intDelay
     * @param string $strCondition
     * @param string $strSelector
     * @throws Exception|Caller
     */
    public function __construct($strEventName, $intDelay = 0, $strCondition = null, $strSelector = null)
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
     * PHP Magic function implementation
     * @param string $strName
     *
     * @return int|mixed|null|string
     * @throws Exception|Caller
     */
    public function __get($strName)
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
