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
        const true REVERT_ON = true;                // always revert
        const false REVERT_OFF = false;            // never revert
        const string REVERT_VALID = 'valid';        // revert if dropped successfully
        const string REVERT_INVALID = 'invalid';    // revert if isn't dropped successfully

        /** @var array|null */
        protected ?array $aryOriginalPosition = null;
        /** @var array|null */
        protected ?array $aryNewPosition = null;

        // redirect all JS requests to the parent control

        /**
         * Used by jQuery UI wrapper controls to find the element on which to apply the jQuery function
         *
         * NOTE: Some controls that use jQuery will get wrapped with extra divs by the jQuery library.
         * If such a control then gets replaced by Ajax during a redrawing, the jQuery effects will be deleted. To solve this,
         * the corresponding QCubed control should set UseWrapper to true, attach the jQuery effect to
         * the wrapper, and override this function to return the ID of the wrapper. See DialogBase.php for
         * an example.
         *
         * @return string DOM element ID to apply jQuery UI function to
         */
        public function getJqControlId(): string
        {
            return $this->objParentControl->ControlId;
        }

        /**
         * This render method is the most basic render-method available.
         * It will perform an attribute overriding (if any) and will either display the rendered
         * HTML (if blnDisplayOutput is true, which it is by default), or it will return the
         * rendered HTML as a string.
         *
         * @param boolean $blnDisplayOutput render the control or return as string
         *
         * @return string
         */
        public function render($blnDisplayOutput = true): string
        {
            return '';
        }

        /**
         * This method will render the control, itself, and will return the rendered HTML as a string
         *
         * As an abstract method, any class extending ControlBase must implement it.  This ensures that
         * each control has its own specific HTML.
         *
         * When outputting HTML, you should call GetHtmlAttributes to get the attributes for the main control.
         *
         * If you are outputting a complex control and need to include IDs in sub controls, your IDs should be of the form:
         *    $parentControl->ControlId. '_' . $strSubcontrolId.
         * The underscore indicates that actions and posting data should first be directed to parent control and parent
         * management will handle the rest.
         *
         * @return string
         */
        protected function getControlHtml(): string
        {
            return '';
        }

        /**
         * Checks if this control contains a valid value.
         *
         * This abstract method defines how a control should validate itself based on the value/
         * properties it has. It should also include the handling of ensuring the "Required"
         * requirements are obeyed if this control's "Required" flag is set to true.
         *
         * For Controls that can't realistically be "validated" (e.g., labels, datagrids, etc.),
         * those controls should simply have Validate() return true.
         *
         */
        public function validate(): bool
        {
            return true;
        }

        /**
         * ParsePostData parses the value of this control from FormState
         *
         * This abstract method must be implemented by all controls.
         *
         * When utilizing formgen, the programmer should never access form variables directly (e.g.,
         * via the $_FORM array). It can be assumed that at *ANY* given time, a control's
         * values/properties will be "up to date" with whatever the webserver has entered in.
         *
         * When a Form is Created via Form::create(string), the form will go through to check and
         * see if it is a first-run of a form, or if it is a post-back.  If it is a postback, it
         * will go through its own private array of controls and call ParsePostData on EVERY control
         * it has.  Each control is responsible for "knowing" how to parse the $_POST data to update
         * its own values/properties based on what was returned to via the postback.
         */
        public function parsePostData(): void
        {
        }


        /**
         * Attaches the JQueryUI widget to the HTML object if a widget is specified.
         */
        protected function makeJqWidget(): void
        {
            parent::makeJqWidget();
            Application::executeJsFunction('qcubed.draggable', $this->getJqControlId(), $this->ControlId,
                ApplicationBase::PRIORITY_HIGH);
        }


        /**
         * PHP __set magic method implementation
         *
         * @param string $strName Property Name
         * @param string $mixValue Property Value
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws \Exception
         */
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

        /**
         * PHP __get magic method implementation
         *
         * @param string $strName Property Name
         *
         * @return mixed
         * @throws Caller
         * *@throws \Exception
         */
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
