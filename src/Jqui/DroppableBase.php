<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Jqui;

use QCubed\ApplicationBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class DroppableBase
 *
 * The DroppableBase class defined here provides an interface between the generated
 * DroppableGen class and QCubed. This file is part of the core and will be overwritten
 * when you update QCubed. To override, make your changes to the Droppable.php file instead.
 *
 * This class is designed to work as a kind of add-on class to a QCubed Control, giving its capabilities
 * to the control. To make a QCubed Control droppable, simply set $ctl->Droppable = true. You can then
 * get to this class to further manipulate the aspects of the droppable through $ctl->DropObj.
 *
 * @property string $DroppedId ControlId of a control that was dropped onto this
 *
 * @link http://jqueryui.com/droppable/
 * @package QCubed\Jqui
 */
class DroppableBase extends DroppableGen
{
    /** @var string|null */
    protected ?string $strDroppedId = null;

    // redirect all JS requests to the parent control
    public function getJqControlId(): string
    {
        return $this->objParentControl->ControlId;
    }

    public function render(bool|array $blnDisplayOutput = true): string
    {
        return '';
    }

    protected function getControlHtml(): string
    {
        return '';
    }

    public function validate(): bool
    {
        return true;
    }

    public function parsePostData(): void
    {
    }

    protected function makeJqWidget(): void
    {
        parent::makeJqWidget();
        Application::executeJsFunction('qcubed.droppable', $this->getJqControlId(), $this->ControlId,
            ApplicationBase::PRIORITY_HIGH);
    }

    /**
     * PHP __set magic method implementation
    * /**
     * @param string $strName
     * @param mixed $mixValue
     * @return void
     *@throws InvalidCast
     * @throws Caller
     */
    public function __set(string $strName, mixed $mixValue): void
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
    public function __get(string $strName): mixed
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
