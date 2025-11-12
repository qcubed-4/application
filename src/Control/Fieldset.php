<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Control;

    use QCubed\Css\DisplayType;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Type;

    /**
     * Class Fieldset
     *
     * Encapsulates a fieldset, which has a legend that acts as a label. HTML5 defines a new Name element, which
     * is not yet supported in IE of this writing, but other browsers support it. So, if it's defined, we will output
     * it in the HTML, but it will not affect what appears on the screen unless you draw the Name too.
     *
     * @package Controls\Base
     *
     * @property string $Legend is the legend that will be output for the fieldset.
     * @was QFieldset
     * @package QCubed\Control
     */
    class Fieldset extends BlockControl
    {
        /** @var string HTML tag to the used for the Block Control */
        protected string $strTagName = 'fieldset';
        /** @var string Default display style for the control. See QCubed\Css\Display class for an available list */
        protected string $strDefaultDisplayStyle = DisplayType::BLOCK;
        /** @var bool Is the control a block element? */
        protected bool $blnIsBlockElement = true;
        /** @var bool Use htmlentities for the control? */
        protected bool $blnHtmlEntities = false;
        /** @var  string legend */
        protected string $strLegend;

        /**
         * We will output style tags and such, but fieldset styling is not well-supported across browsers.
         */
        protected function getInnerHtml(): string
        {
            $strHtml = parent::getInnerHtml();

            if (!empty($this->strLegend)) {
                $strHtml = '<legend>' . $this->strLegend . '</legend>' . _nl() . $strHtml;
            }

            return $strHtml;
        }

        /////////////////////////
        // Public Properties: GET
        /////////////////////////

        /**
         * Magic method to retrieve the value of a property dynamically by its name.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property.
         * @throws Caller Throws an exception if the property does not exist or is inaccessible.
         * @throws \Exception
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                // APPEARANCE
                case "Legend":
                    return $this->strLegend;

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
         * Sets a property value for the object. Updates the 'Legend' property if matched,
         * otherwise delegates the property setting to the parent class.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the property.
         *
         * @return void
         * @throws InvalidCast Thrown when the value provided for 'Legend' cannot be cast to a string.
         * @throws Caller Thrown when the property is not recognized by the parent class.
         * @throws \Exception
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                // APPEARANCE
                case "Legend":
                    try {
                        $this->strLegend = Type::cast($mixValue, Type::STRING);
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
