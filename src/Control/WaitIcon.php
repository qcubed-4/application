<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Control;

    require_once(dirname(__DIR__, 2) . '/i18n/i18n-lib.inc.php');

    use QCubed\Project\Control\ControlBase;
    use QCubed\Project\Control\FormBase;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Type;

    /**
     * @package QCubed\Control
     *
     * @property string $Text
     * @property string $TagName
     * @property string $Width
     * @property string $Height
     * @property string $SpinnerType
     */
    class WaitIcon extends ControlBase
    {
        /** @var string String to be displayed as alt text (e.g. "Please wait") */
        protected string $strText;

        /** @var string HTML tag name to be used for rendering the text */
        protected string $strTagName = 'span';

        /** @var bool */
        protected bool $blnDisplay = false;

        /** @var string Specifies the spinner type: 'default'|'classic'|'ripple'|'bar' */
        protected string $strSpinnerType = 'default';

        protected string $strWidth = '1.5em';
        protected string $strHeight = '1.5em';

        /**
         * Constructor method for initializing the control.
         *
         * @param FormBase|ControlBase $objParentObject The parent object of the control.
         * @param string|null $strControlId Optional ID for the control.
         *
         * @return void
         * @throws Caller
         */
        public function __construct(FormBase|ControlBase $objParentObject, ?string $strControlId = null)
        {
            parent::__construct($objParentObject, $strControlId);
            $this->strText = t('Please wait...');
        }

        /**
         * Processes and parses POST data submitted to the application.
         * This method is responsible for handling input processing and
         * ensuring valid data extraction from the POST request.
         *
         * @return void
         */
        public function parsePostData(): void
        {
        }

        /**
         * Validates the current state or data.
         *
         * @return bool Returns true if the validation is successful.
         */
        public function validate(): bool
        {
            return true;
        }

        /**
         * Returns the HTML we have to send to the browser to render this wait icon.
         */
        protected function getControlHtml(): string
        {
            $style = sprintf(
                'width:%s;height:%s;',
                htmlspecialchars($this->strWidth ?: '1.5em'),
                htmlspecialchars($this->strHeight ?: '1.5em')
            );

            switch ($this->strSpinnerType) {
                case 'classic':
                    $spinnerHtml =
                        '<span class="qc-wait-spinner-classic" style="' . $style .
                        '" role="status" aria-label="' . htmlspecialchars($this->strText) . '"></span>';
                    break;

                case 'ripple':
                    $spinnerHtml =
                        '<span class="qc-wait-spinner-ripple" style="' . $style .
                        '" role="status" aria-label="' . htmlspecialchars($this->strText) . '">' .
                        '<span class="qc-wait-spinner-ripple-circle"></span>' .
                        '<span class="qc-wait-spinner-ripple-circle"></span>' .
                        '</span>';
                    break;

                case 'bar':
                    $spinnerHtml =
                        '<span class="qc-wait-bar-wrap" role="status" aria-label="' . htmlspecialchars($this->strText) . '">' .
                        '<span class="qc-wait-bar" aria-hidden="true"></span>' .
                        '<span class="qc-wait-text">' . htmlspecialchars($this->strText) . '</span>' .
                        '</span>';
                    break;

                case 'default':
                default:
                    // Three-color pastel ray spinner (12 pieces)
                    $bars = str_repeat('<span class="bar"></span>', 12);
                    $spinnerHtml =
                        '<span class="qc-wait-spinner-colors" style="' . $style .
                        '" role="status" aria-label="' . htmlspecialchars($this->strText) . '">' .
                        $bars .
                        '</span>';
                    break;
            }

            return $this->renderTag($this->strTagName, null, null, $spinnerHtml);
        }

        /**
         * Magic method to retrieve the value of a property.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property.
         * @throws Caller If the property does not exist.
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case "Text":
                    return $this->strText;
                case "TagName":
                    return $this->strTagName;
                case "Width":
                    return $this->strWidth;
                case "Height":
                    return $this->strHeight;
                case "SpinnerType":
                    return $this->strSpinnerType;

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
         * Magic method to set property values dynamically.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the property.
         *
         * @return void
         * @throws Caller Thrown if the property is not valid in the current context.
         * @throws InvalidCast Thrown if the provided value cannot be cast to the expected type.
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            $this->blnModified = true;

            switch ($strName) {
                case "Text":
                    try {
                        $this->strText = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "TagName":
                    try {
                        $this->strTagName = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "Width":
                    try {
                        $this->strWidth = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "Height":
                    try {
                        $this->strHeight = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

                case "SpinnerType":
                    try {
                        $this->strSpinnerType = Type::cast($mixValue, Type::STRING);
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