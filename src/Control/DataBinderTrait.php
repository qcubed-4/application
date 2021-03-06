<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Exception\Caller;


/**
 * Trait DataBinderTrait
 *
 * The DataBinder trait encapsulates the functionality of data binding for multiple controls that would like to make
 * use of it. Data binding lets you only recall data during draw time, thus saving space, because the data is not
 * saved with the formstate. Controls that use this will have to be sure to unload the data after drawing as needed.
 *
 * There are a couple of modes of this. A legacy mode use strings to record the function names. The more modern way uses
 * an array as a callable.
 *
 * @was QDataBinder
 * @package QCubed\Control
 */
trait DataBinderTrait
{
    protected $objDataBinder;

    /**
     * Sets the data binder. Allows it to be sent in a couple of different ways:
     * - legacy mode: method name followed by optional control to call the method on. If not specified, will call on the form.
     * - modern mode: a php callable.
     * @param callable|string $mixMethodName
     * @param null|FormBase|ControlBase $objParentControl
     */
    public function setDataBinder($mixMethodName, $objParentControl = null)
    {
        if (is_callable($mixMethodName)) {
            $this->objDataBinder = $mixMethodName;
        } elseif (is_string($mixMethodName)) {
            if (!$objParentControl) {
                $objParentControl = $this->getForm();
            }
            $this->objDataBinder = array($objParentControl, $mixMethodName);
        } else {
            assert(false);    // Improperly specified data binder
        }
    }

    /**
     * Bind the data by calling the data binder. Will pass the current control as the parameter to the data binder.
     * @throws Caller
     */
    public function callDataBinder()
    {
        if ($this->objDataBinder) {
            if (is_array($this->objDataBinder)) {
                if ($this->objDataBinder[0] === $this) {
                    call_user_func($this->objDataBinder); // assume data binder is in a qcontrol, or is public
                } elseif ($this->objDataBinder[0] instanceof FormBase) {
                    $this->objDataBinder[0]->callDataBinder($this->objDataBinder,
                        $this); // Let form call the data binder, so that binder can be private to form
                } else {
                    call_user_func($this->objDataBinder, $this); // assume data binder is in a qcontrol, or is public
                }
            } else {
                try {
                    call_user_func($this->objDataBinder);    // calling databinder on self, so do not pass the control as param
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            }
        }
    }

    /**
     * Check the binder for a reference to the form.
     */
    public function sleep()
    {
        $this->objDataBinder = ControlBase::sleepHelper($this->objDataBinder);
        parent::sleep();
    }

    /**
     * @param FormBase $objForm
     */
    public function wakeup(FormBase $objForm)
    {
        parent::wakeup($objForm);
        $this->objDataBinder = ControlBase::wakeupHelper($objForm, $this->objDataBinder);
    }

    /**
     * @return bool
     */
    public function hasDataBinder()
    {
        return $this->objDataBinder ? true : false;
    }

    /**
     * Returns the FormBase. All ControlBases implement this.
     * @return FormBase
     */
    abstract function getForm();
}