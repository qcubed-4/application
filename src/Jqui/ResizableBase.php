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
use Throwable;

/**
 * Class ResizableBase
 *
 * Implements the JQuery UI Resizable capabilities into a QCubed Control
 *
 * The ResizableBase class defined here provides an interface between the generated
 * ResizableGen class and QCubed. This file is part of the core and will be overwritten
 * when you update QCubed. To override, make your changes to the QResizable.class.php file instead.
 *
 * This class is designed to work as a kind of add-on class to a QCubed Control, giving its capabilities
 * to the control. To make a QCubed Control resizable, simply set $ctl->Resizable = true. You can then
 * get to this class to further manipulate the aspects of the resizable through $ctl->ResizeObj.
 *
 * @property-read Integer $DeltaX Amount of change in width that happened on the last drag
 * @property-read Integer $DeltaY Amount of change in height that happened on the last drag
 *
 * @link http://jqueryui.com/resizable/
 * @package QCubed\Jqui
 */
class ResizableBase extends ResizableGen
{
    /** @var array|null */
    protected ?array $aryOriginalSize = null;
    /** @var array|null */
    protected ?array $aryNewSize = null;

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

    /**
     * Attach the JavaScript to the control.
     */
    protected function makeJqWidget(): void
    {
        parent::makeJqWidget();
        Application::executeJsFunction('qcubed.resizable', $this->getJqControlId(), $this->ControlId,
            ApplicationBase::PRIORITY_HIGH);
    }


    /**
     * @param string $strName
     * @param mixed $mixValue
     * @return void
     * @throws InvalidCast
     * @throws Caller
     * @throws Throwable Exception
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case '_ResizeData': // Internal only. Do not use. Called by qcubed.resizable to keep track of changes.
                try {
                    $data = Type::cast($mixValue, Type::ARRAY_TYPE);
                    $this->aryOriginalSize = $data['originalSize'];
                    $this->aryNewSize = $data['size'];

                    // update dimensions
                    $this->Width = $this->aryNewSize['width'];
                    $this->Height = $this->aryNewSize['height'];
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

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

    /**
     * @param string $strName
     * @return mixed
     * @throws Caller
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'DeltaX':
                if ($this->aryOriginalSize) {
                    return $this->aryNewSize['width'] - $this->aryOriginalSize['width'];
                } else {
                    return 0;
                }

            case 'DeltaY':
                if ($this->aryOriginalSize) {
                    return $this->aryNewSize['height'] - $this->aryOriginalSize['height'];
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
