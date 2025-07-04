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
use QCubed\Control\ControlBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class DraggableBase
 *
 * The DraggableBase class defined here provides an interface between the generated
 * DraggableGen class and QCubed. This file is part of the core and will be overwritten
 * when you update QCubed. To override, make your changes to the Draggable.php file instead.
 *
 * This class is designed to work as a kind of add-on class to a QCubed Control, giving its capabilities
 * to the control. To make a QCubed Control draggable, simply set $ctl->Draggable = true. You can then
 * get to this class to further manipulate the aspects of the draggable through $ctl->DragObj.
 *
 * @property-read Integer $DeltaX The amount of left shift during the last drag
 * @property-read Integer $DeltaY Amount of change in top that happened on the last drag
 * @property mixed $Handle A drag handle. Can be a control, a selector or array of controls or jQuery selectors.
 *
 * @link http://jqueryui.com/draggable/
 * @package QCubed\Jqui
 */
class DraggableBase extends DraggableGen
{
    /** Revert Modes */
    const REVERT_ON = true;                // always revert
    const REVERT_OFF = false;            // never revert
    const REVERT_VALID = 'valid';        // revert if dropped successfully
    const REVERT_INVALID = 'invalid';    // revert if isn't dropped successfully

    /** @var array|null */
    protected ?array $aryOriginalPosition = null;
    /** @var array|null */
    protected ?array $aryNewPosition = null;

    // redirect all JS requests to the parent control
    public function getJqControlId(): string
    {
        return $this->objParentControl->ControlId;
    }

    public function render($blnDisplayOutput = true): string
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
        Application::executeJsFunction('qcubed.draggable', $this->getJqControlId(), $this->ControlId,
            ApplicationBase::PRIORITY_HIGH);
    }


    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case '_DragData': // Internal only. Do not use. Used by JS above to keep track of user selection.
                try {
                    $data = Type::cast($mixValue, Type::ARRAY_TYPE);
                    $this->aryOriginalPosition = $data['originalPosition'];
                    $this->aryNewPosition = $data['position'];

                    // update parent's coordinates
                    $this->objParentControl->getWrapperStyler()->Top = $this->aryNewPosition['top'];
                    $this->objParentControl->getWrapperStyler()->Left = $this->aryNewPosition['left'];
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Handle':
                // Override to let you set the handle to:
                //	a Control, or selector, or array of Controls or selectors
                if ($mixValue instanceof ControlBase) {
                    parent::__set($strName, '#' . $mixValue->ControlId);
                } elseif (is_array($mixValue)) {
                    $aHandles = array();
                    foreach ($mixValue as $mixItem) {
                        if ($mixItem instanceof ControlBase) {
                            $aHandles[] = '#' . $mixItem->ControlId;
                        } elseif (is_string($mixItem)) {
                            $aHandles[] = $mixItem;
                        }
                    }
                    parent::__set($strName, join(',', $aHandles));
                } else {
                    parent::__set($strName, $mixValue);
                }
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                    break;
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'DeltaX':
                if ($this->aryOriginalPosition) {
                    return $this->aryNewPosition['left'] - $this->aryOriginalPosition['left'];
                } else {
                    return 0;
                }

            case 'DeltaY':
                if ($this->aryOriginalPosition) {
                    return $this->aryNewPosition['top'] - $this->aryOriginalPosition['top'];
                } else {
                    return 0;
                }

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
