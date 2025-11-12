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
    use QCubed\Type;

    /**
     * Class ImageInput
     *
     * This class will render an HTML Image input <input type="image">.
     *
     * Image inputs act like buttons, but specifically also produce an x and y coordinate for where the image was clicked.
     * There are other ways to produce image buttons, including using a Button control and adding an Image
     * control to it, or adding a background image to a Button. You can also just use an Image control and add an onClick handler.
     * Each produces different HTML, and you can pick which one is more suitable to your needs.
     *
     * @property string $AlternateText is rendered as the HTML "alt" tag
     * @property string $ImageUrl is the url of the image to be used
     * @property boolean $PrimaryButton     Set to true if you want this button to submit the form
     * @property-read integer $ClickX
     * @property-read integer $ClickY
     * @package QCubed\Control
     */
    class ImageInput extends ActionControl
    {
        protected ?string $strAlternateText = null;
        protected ?string $strImageUrl = null;
        protected int $intClickX;
        protected int $intClickY;
        /** @var bool True to make this button submit the form, which is the default for HTML input images */
        protected bool $blnPrimaryButton = true;

        // SETTINGS
        protected bool $blnActionsMustTerminate = true;

        /**
         * MUST be used in conjunction with RegisterClickPosition Action to work.
         */
        public function parsePostData(): void
        {
            $strKeyX = sprintf('%s_x', $this->strControlId);
            $strKeyY = sprintf('%s_y', $this->strControlId);
            if (isset ($_POST[$strKeyX]) && $_POST[$strKeyX] !== '') {
                $this->intClickX = $_POST[$strKeyX];
                $this->intClickY = $_POST[$strKeyY];
            }
        }

        /**
         * Renders the HTML representation of the control.
         *
         * This method generates the necessary HTML for the control, including overrides for a name, type, alt text, source,
         * and hidden inputs for capturing x and y click positions.
         *
         * @return string The concatenated HTML string for the control and its associated hidden inputs for click position tracking.
         */
        protected function getControlHtml(): string
        {
            $overrides = [
                'name'=>$this->strControlId,
                'type'=>'image',
                'alt'=>$this->strAlternateText,
                'src'=>$this->strImageUrl
            ];

            $strToReturn = $this->renderTag('input', $overrides,
                null, null, true);

            $strToReturn .= sprintf('<input type="hidden" name="%s_x" id="%s_x" value=""/><input type="hidden" name="%s_y" id="%s_y" value=""/>',
                $this->strControlId,
                $this->strControlId,
                $this->strControlId,
                $this->strControlId);

            return $strToReturn;
        }

        /////////////////////////
        // Public Properties: GET
        /////////////////////////

        /**
         * Magic method to retrieve the value of a property by its name.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property, if it exists.
         * @throws Caller If the property does not exist or cannot be accessed.
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

                // BEHAVIOR
                case "PrimaryButton":
                    return $this->blnPrimaryButton;
                case "ClickX":
                    return $this->intClickX;
                case "ClickY":
                    return $this->intClickY;

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
         * Used to set the value of a property dynamically. Updates the property value
         * by casting the provided value to the appropriate type and sets the modification
         * flag to true.
         *
         * @param string $strName The name of the property to be set.
         * @param mixed $mixValue The value to assign to the specified property.
         *
         * @return void
         *
         * @throws InvalidCast Thrown when the provided value cannot be cast to the required type.
         * @throws Caller Thrown when attempting to set an undefined property or if the parent
         * @throws \Exception
         * method throws this exception.
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            $this->blnModified = true;

            switch ($strName) {
                // APPEARANCE
                case "AlternateText":
                    try {
                        $this->strAlternateText = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "ImageUrl":
                    try {
                        $this->strImageUrl = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                // BEHAVIOR
                case "PrimaryButton":
                    try {
                        $this->blnPrimaryButton = Type::cast($mixValue, Type::BOOLEAN);
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
