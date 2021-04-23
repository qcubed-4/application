<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Action;

use QCubed\Exception\Caller;
use QCubed\ObjectBase;
use QCubed\Type;
use QCubed\Control\ControlBase;


/**
 * Base class for all other Actions.
 *
 * @package Actions
 * @property \QCubed\Event\EventBase $Event Any Event derived class instance
 * @was QAction
 */
abstract class ActionBase extends ObjectBase
{
    /**
     * Abstract method, implemented in derived classes. Returns the JS needed for the action to work
     *
     * @param ControlBase $objControl
     *
     * @return mixed
     */
    abstract public function renderScript(ControlBase $objControl);

    /** @var \QCubed\Event\EventBase Event object which will fire this action */
    protected $objEvent;


    /**
     * PHP Magic function to set the property values of an object of the class
     * In this case, we only have 'Event' property to be set
     *
     * @param string $strName Name of the property
     * @param string $mixValue Value of the property
     *
     * @throws Caller
     * @return void
     */
    public function __set($strName, $mixValue)
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
    public function __get($strName)
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
