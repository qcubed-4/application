<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Control;

    use QCubed\Project\Control\ControlBase;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Html;
    use QCubed\Type;

    /**
     * Class Image
     *
     * A basic img tag.
     *
     * You can turn this into an image map by adding ImageArea controls as child controls of this object.
     *
     * @property string $AlternateText is rendered as the HTML "alt" tag
     * @property string $ImageUrl is the url of the image to be used
     * @property string $Height Height in pixels
     * @property string $Width Width in pixels
     * @package QCubed\Control
     */
    class Image extends ControlBase
    {
        /** @var  string */
        protected string $strAlternateText = '';
        /** @var  string */
        protected string $strImageUrl = '';
        /** @var  integer */
        protected int $intHeight = 0;
        /** @var  integer */
        protected int $intWidth = 0;

        /**
         * Generates the HTML representation of the control, including attributes and child controls if applicable.
         *
         * @return string The generated HTML for the control.
         */
        protected function getControlHtml(): string
        {
            $attributes = [];
            if ($this->strAlternateText) {
                $attributes['alt'] = $this->strAlternateText;
            }
            if ($this->strImageUrl) {
                $attributes['src'] = $this->strImageUrl;
            }
            if ($this->intHeight) {
                $attributes['height'] = (string)$this->intHeight;
            }
            if ($this->intWidth) {
                $attributes['width'] = (string)$this->intWidth;
            }

            $strMap = "";
            if ($this->getChildControls()) {    // These should only be ImageArea controls!
                $attributes["usemap"] = "#" . $this->ControlId . "_map";
                $strMap = Html::renderTag("map", ["name"=>$this->ControlId . "_map"], $this->renderChildren(false));
            }

            return $this->renderTag('img', $attributes, null, null, true) . $strMap;
        }

        /**
         * Adds a child control to the current control.
         * Only instances of ImageArea are allowed as child controls for Image controls.
         *
         * @param \QCubed\Project\Control\ControlBase|\QCubed\Control\ControlBase $objControl The control to be added as a child.
         *
         * @return void
         * @throws \QCubed\Exception\Caller If the provided control is not an instance of ImageArea.
         */
        public function addChildControl(ControlBase|\QCubed\Control\ControlBase $objControl): void
        {
            if (!$objControl instanceof ImageArea) {
                throw new Caller("Only ImageArea controls are allowed as children of Image controls");
            }
            parent::addChildControl($objControl);
        }

        /**
         * Validates the current state or data.
         *
         * @return bool Returns true if validation is successful.
         */
        public function validate(): bool
        {
            return true;
        }

        /**
         * Parses the POST data and processes it accordingly.
         *
         * @return void
         */
        public function parsePostData(): void
        {
        }

        /**
         * @param string $strName
         *
         * @return mixed|null
         * @throws Caller
         * @throws \Exception
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                // APPEARANCE
                case "AlternateText":
                    return $this->strAlternateText;
                case "ImageUrl":
                    return $this->strImageUrl;
                case "Height":
                    return $this->intHeight;
                case "Width":
                    return $this->intWidth;


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
         * @param string $strName
         * @param mixed $mixValue
         *
         * @return void
         * @throws InvalidCast
         * @throws Caller
         * @throws \Exception
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                // APPEARANCE
                case "AlternateText":
                    try {
                        $this->blnModified = true;
                        $this->strAlternateText = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "ImageUrl":
                    try {
                        $this->blnModified = true;
                        $this->strImageUrl = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "Height":
                    try {
                        $this->blnModified = true;
                        $this->intHeight = Type::cast($mixValue, Type::INTEGER);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "Width":
                    try {
                        $this->blnModified = true;
                        $this->intWidth = Type::cast($mixValue, Type::INTEGER);
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
