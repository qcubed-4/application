<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Action;

use QCubed\Control\FormBase;
use QCubed\Exception\Caller;
use QCubed\Control\ControlBase;
use QCubed\ObjectBase;

/**
 * Class ActionParams
 *
 * Encapsulated information that is passed to Server and Ajax methods triggered by actions.
 *
 * @property-read ActionBase $Action
 * @property mixed $Param
 * @property mixed $ActionParameter
 * @property-read mixed $OriginalParam
 * @property-read ControlBase $Control
 * @property-read string $FormId
 *
 * @package QCubed\Action
 */
class ActionParams extends ObjectBase
{
    /** @var  ActionBase Action that triggered the method */
    protected $objAction;
    /** @var  mixed Parameters coming from javascript. If a javascript object is sent, this will be an indexed array */
    protected $mixParam;
    /** @var  mixed Controls can alter the parameters coming from javascript. This is the original parameter. */
    protected $mixOriginalParam;
    /** @var  ControlBase The control that originated the action. */
    protected $objControl;
    /** @var  string The form id of the form triggering the action. Since we only allow one form currently, this is for future expansion. */
    protected $strFormId;


    /**
     * ActionParams constructor.
     * @param ActionBase $objAction
     * @param FormBase $objForm
     * @param string $strControlId
     * @param mixed $mixParam
     */
    public function __construct(ActionBase $objAction, FormBase $objForm, $strControlId, $mixParam)
    {
        $this->objAction = $objAction;
        $this->mixOriginalParam = $mixParam;
        $this->mixParam = $mixParam;
        $this->strFormId = $objForm->FormId;
        $this->objControl = $objForm->getControl($strControlId);
    }

    /**
     * PHP Magic function to get the property values of a class object
     *
     * @param string $strName Name of the property
     *
     * @return mixed|null|string
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case 'Action':
                return $this->objAction;
            case 'Param':
            case 'ActionParameter':
                return $this->mixParam;
            case 'OriginalParam':
                return $this->mixOriginalParam;
            case 'FormId':
                return $this->strFormId;
            case 'Control':
                return $this->objControl;
            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * PHP Magic function to set the property values of an object of the class
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
            case 'Param':
                $this->mixParam = $mixValue;
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
}
