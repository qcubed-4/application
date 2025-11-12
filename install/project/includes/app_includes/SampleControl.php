<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Control\ControlBase as QControl;
    use QCubed\Project\Control\FormBase as QForm;
    use QCubed\QString;
    use QCubed\Type;

    /**
     * This is a SAMPLE of a custom Control that you could write.  Think of this as a "starting point".
     * Remember: EVERYTHING here is meant to be customized!  To use, simply make a copy of this file,
     * rename the file, and edit the renamed file.  Remember to specify a control Class name which matches the
     * name of your file.  And then implement your own logic for getControlHtml().
     *
     *
     */
    class SampleControl extends QControl
    {
        protected int $intExample;
        protected string $strFoo;

        /**
         * Constructor for this control
         * @param QForm|QControl $objParentObject Parent QForm or QControl that is responsible for rendering this control
         * @param null|string $strControlId
         * @throws Caller
         */
        public function __construct(QForm|QControl $objParentObject, ?string $strControlId = null)
        {
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }

            // Set up Control-specific CSS and JS files to be loaded
            // Paths are relative to the __CSS_ASSETS__ and __JS_ASSETS__ directories
            // Multiple files can be specified, as well, by separating with a comma
            // $this->strJavaScripts = 'custom.js, ../path/to/prototype.js, etc.js';
            // $this->strStyleSheets = 'custom.css';

            // Additional Setup Performed here
            $this->intExample = 28;
            $this->strFoo = 'Hello!';
        }

        /**
         * If this control needs to update itself from the $_POST data, the logic to do so
         * will be performed in this method.
         */
        public function parsePostData(): void
        {
        }

        /**
         * If this control has validation rules, the logic to do so
         * will be performed in this method.
         * @return boolean
         */
        public function validate(): bool
        {
            return true;
        }

        /**
         * Get the HTML for this Control.
         * @return string
         */
        public function getControlHtml(): string
        {
            return $this->renderTag('span', null, null, $this->getInnerHtml());
        }

        /**
         * @return string
         */
        protected function getInnerHtml(): string
        {
            return QString::htmlEntities("Sample Control " . $this->intExample . ' - ' . $this->strFoo);
        }


        // For any JavaScript calls that need to be made whenever this control is rendered or re-rendered
        //public function getEndScript() {
        //  $strToReturn = parent::getEndScript();
        //  return $strToReturn;
        //}

        // For any HTML code that needs to be rendered at the END of the QForm when this control is INITIALLY rendered.
//		public function getEndHtml() {
//			$strToReturn = parent::getEndHtml();
//			return $strToReturn;
//		}

        /////////////////////////
        // Public Properties: GET
        /////////////////////////

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
                case 'Example': return $this->intExample;
                case 'Foo': return $this->strFoo;

                default:
                    try {
                        return parent::__get($strName);
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
            }
        }

        /////////////////////////
        // Public Properties: SET
        /////////////////////////

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
            $this->blnModified = true;

            switch ($strName) {

                case 'Example':
                    try {
                        $this->intExample = Type::cast($mixValue, Type::INTEGER);
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;
                case 'Foo':
                    try {
                        $this->strFoo = Type::cast($mixValue, Type::STRING);
                    } catch (Caller $objExc) {
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
            }
        }
    }
