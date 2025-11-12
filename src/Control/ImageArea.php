<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Control;

    use Exception;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Control\ControlBase;
    use QCubed\Type;

    /**
     * Class ImageArea
     *
     * An AREA tag that is to be used specifically as a child control of an Image control. Creates an image map for
     * the parent image to detect specific areas of an image. You can attach actions and events to this control like any
     * other QCubed Control.
     *
     * @property string $Shape a shape type. Use ImageArea::SHAPE_RECT, SHAPE_CIRCLE, or SHAPE_POLY
     * @property int[] $Coordinates is the url of the image to be used
     * @package QCubed\Control
     */
    class ImageArea extends ControlBase
    {
        const string SHAPE_RECT = "rect";
        const string SHAPE_CIRCLE = "circle";
        const string SHAPE_POLY = "poly";

        /** @var  string */
        protected string $strShape;
        /** @var  int[] */
        protected array $coordinates;

        /**
         * Generates the HTML for the control.
         *
         * @return string The generated control HTML.
         * @throws Exception If the shape is not defined or coordinates are missing.
         */
        protected function getControlHtml(): string
        {
            $this->blnUseWrapper = false;   // make sure we do not use a wrapper to draw!
            if (!$this->strShape) {
                throw new Exception("Shape is required for ImageArea controls.");
            }
            if (!$this->coordinates) {
                throw new Exception("Coordinates are required for ImageArea controls.");
            }

            $attributes = ["shape" => $this->strShape, "coords" => implode(",", $this->coordinates)];
            return $this->renderTag('area', $attributes, null, null, true);
        }

        /**
         * Validates the current state or data.
         *
         * @return bool True if the validation is successful, false otherwise.
         */
        public function validate(): bool
        {
            return true;
        }

        /**
         * Parses and processes the data received via a POST request.
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
                case "Shape":
                    return $this->strShape;
                case "Coordinates":
                    return $this->coordinates;

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
                case "Shape":
                    try {
                        $this->blnModified = true;
                        $this->strShape = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "Coordinates":
                    try {
                        $this->blnModified = true;
                        $this->coordinates = Type::cast($mixValue, Type::ARRAY_TYPE);
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
