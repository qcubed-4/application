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
    use QCubed\Exception\DataBind;


    /**
     * Trait DataBinderTrait
     *
     * The DataBinder trait encapsulates the functionality of data binding for multiple controls that would like to make
     * use of it. Data binding lets you only recall data during draw time, thus saving space, because the data is not
     * saved with the formstate. Controls that use this will have to be sure to unload the data after drawing as needed.
     *
     * There are a couple of modes of this. A legacy mode uses strings to record the function names. The more modern way uses
     * an array as a callable.
     *
     * @was QDataBinder
     * @package QCubed\Control
     */
    trait DataBinderTrait
    {
        /**
         * @var callable|array|null $objDataBinder
         */
        protected $objDataBinder = null;

        /**
         * Sets the data binder method for the control.
         *
         * @param callable|string $mixMethodName A callable function or a method name (as a string) used for data binding.
         * @param object|null $objParentControl Optional. The parent control object, required if $mixMethodName is a string and references a method.
         * @return void
         */
        public function setDataBinder(callable|string $mixMethodName, ?object $objParentControl = null): void
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
         * Executes the data binder callback associated with the control or delegates it
         * to the form if necessary. Supports various forms of data binder invocation,
         * including array-style callbacks and error handling for invalid calls.
         *
         * @return void
         * @throws DataBind
         * @throws Caller
         */
        public function callDataBinder(): void
        {
            if ($this->objDataBinder) {
                if (is_array($this->objDataBinder)) {
                    if ($this->objDataBinder[0] === $this) {
                        call_user_func($this->objDataBinder); // assume the data binder is in control or is public
                    } elseif ($this->objDataBinder[0] instanceof FormBase) {
                        $this->objDataBinder[0]->callDataBinder($this->objDataBinder,
                            $this); // Let form call the data binder, so that binder can be private to form
                    } else {
                        call_user_func($this->objDataBinder, $this); // assume the data binder is in control or is public
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
        public function sleep(): array
        {

            $this->objDataBinder = ControlBase::sleepHelper($this->objDataBinder);

            return [];

//        $this->objDataBinder = ControlBase::sleepHelper($this->objDataBinder);
//
//        of (is_callable([get_parent_class($this), 'sleep'])) {
//            $parentSleepResult = parent::sleep();
//            return is_array($parentSleepResult) ? $parentSleepResult : [];
//        }
//
//        return [];

//        $this->objDataBinder = ControlBase::sleepHelper($this->objDataBinder);
//
//        $parentSleepResult = parent::sleep();
//
//        return is_array($parentSleepResult) ? $parentSleepResult : [];
        }

        /**
         * Restores the object state by reinitializing the binder with a reference to the form.
         *
         * @param FormBase $objForm The form instance to be used for reinitializing the binder.
         * @return void
         */
        public function wakeup(FormBase $objForm): void
        {
            $this->objDataBinder = ControlBase::wakeupHelper($objForm, $this->objDataBinder);

//        parent::wakeup($objForm);
//        $this->objDataBinder = ControlBase::wakeupHelper($objForm, $this->objDataBinder);
        }

        /**
         * @return bool
         */
        public function hasDataBinder(): bool
        {
            return $this->objDataBinder !== null;
        }

        /**
         * Returns the FormBase. All ControlBases implement this.
         */
        abstract function getForm(): FormBase;
    }
