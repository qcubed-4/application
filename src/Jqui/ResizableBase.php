<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Jqui;

    use Exception;
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
         * @throws Exception
         * @return string
         */
        public function render(bool|array $blnDisplayOutput = true): string
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
         *
         * @return mixed
         * @throws Caller
         * *@throws \Exception
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
