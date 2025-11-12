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
    use QCubed\Exception\InvalidCast;
    use QCubed\QString;
    use QCubed\Type;


    /**
     * Class LinkButton
     *
     * This class will render an HTML link <a href>, but will act like a Button or ImageButton.
     * (it is a subclass of action control)
     * Therefore, you cannot define a "URL/HREF" destination for this LinkButton.  It simply links
     * to "#".  And then if a ClientAction is defined, it will execute that when clicked.  If a ServerAction
     * is defined, it will execute PostBack and execute that when clicked.
     *
     * @property string $Text is the text of the Link
     * @property string $HtmlEntities
     * @package QCubed\Control
     */
    class LinkButton extends ActionControl
    {
        ///////////////////////////
        // Private Member Variables
        ///////////////////////////

        // APPEARANCE
        /** @var string|null The text on the button */
        protected ?string $strText = null;
        /** @var bool Should htmlentities be used on this control? */
        protected bool $blnHtmlEntities = true;

        //////////
        // Methods
        //////////
        /**
         * Function to return the formatted HTML for the control
         * @return string The control's HTML
         */
        protected function getControlHtml(): string
        {
            $strText = $this->strText;
            if ($this->blnHtmlEntities) {
                $strText = QString::htmlEntities($strText);
            }

            return $this->renderTag('a', ['href'=>'#'], null, $strText);
        }

        /////////////////////////
        // Public Properties: GET
        /////////////////////////

        /**
         * The PHP __get magic method
         *
         * @param string $strName Name of the property
         *
         * @return mixed
         * @throws Caller
         * @throws \Exception
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                // APPEARANCE
                case "Text":
                    return $this->strText;
                case "HtmlEntities":
                    return $this->blnHtmlEntities;
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
         * The PHP __set magic method implementation
         *
         * @param string $strName Name of the property
         * @param mixed $mixValue Value of the property
         *
         * @return void
         * @throws InvalidCast
         * @throws Caller
         * *@throws \Exception
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                // APPEARANCE
                case "Text":
                    try {
                        if ($this->strText !== ($mixValue = Type::cast($mixValue, Type::STRING))) {
                            $this->blnModified = true;
                            $this->strText = $mixValue;
                        }
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "HtmlEntities":
                    try {
                        $this->blnHtmlEntities = Type::cast($mixValue, Type::BOOLEAN);
                        $this->blnModified = true;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

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
    }