<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Jqui;

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class DroppableBase
 *
 * The DroppableBase class defined here provides an interface between the generated
 * DroppableGen class, and QCubed. This file is part of the core and will be overwritten
 * when you update QCubed. To override, make your changes to the Droppable.php file instead.
 *
 * This class is designed to work as a kind of add-on class to a QCubed Control, giving its capabilities
 * to the control. To make a QCubed Control droppable, simply set $ctl->Droppable = true. You can then
 * get to this class to further manipulate the aspects of the droppable through $ctl->DropObj.
 *
 * @property String $DroppedId ControlId of a control that was dropped onto this
 *
 * @link http://jqueryui.com/droppable/
 * @was QDroppableBase
 * @package QCubed\Jqui
 */
class DroppableBase extends DroppableGen
{
    /** @var string */
    protected $strDroppedId = null;

    // redirect all js requests to the parent control
    public function getJqControlId()
    {
        return $this->objParentControl->ControlId;
    }

    public function render($blnDisplayOutput = true)
    {
    }

    protected function getControlHtml()
    {
    }

    public function validate()
    {
        return true;
    }

    public function parsePostData()
    {
    }

    protected function makeJqWidget()
    {
        parent::makeJqWidget();
        Application::executeJsFunction('qcubed.droppable', $this->getJqControlId(), $this->ControlId,
            Application::PRIORITY_HIGH);
    }

    /**
     * PHP __set magic method implementation
    /**
     * @param string $strName
     * @param string $mixValue
     * @throws Caller
     * @throws InvalidCast
     * @return void
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case '_DroppedId': // Internal only. Do not use. Used by JS above to track user actions.
                try {
                    $this->strDroppedId = Type::cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }

    /**
     * PHP __get magic method implementation
     * @param string $strName Property Name
     *
     * @return mixed
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case 'DroppedId':
                return $this->strDroppedId;

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
