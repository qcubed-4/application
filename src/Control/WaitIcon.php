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
     */
    class WaitIcon extends ControlBase
    {
        /** @var string String to be displayed as alt text (e.g. "Please wait")  */
        protected string $strText;
        /** @var string HTML tag name to be used for rendering the text */
        protected string $strTagName = 'span';
        /** @var bool */
        protected bool $blnDisplay = false;

        /** @var string Specifies the spinner type: 'default'|'classic'|'ripple' */
        protected string $strSpinnerType = 'default';
        protected string $strWidth = '1.5em';
        protected string $strHeight = '1.5em';


        /**
         * Constructor for the class.
         *
         * @param mixed $objParentObject The parent object to which this control belongs.
         * @param string|null $strControlId Optional control ID. If null, an ID will be generated automatically.
         * @return void
         * @throws Caller
         */
        public function __construct(FormBase|ControlBase $objParentObject, ?string $strControlId = null)
        {
            parent::__construct($objParentObject, $strControlId);
            $this->strText = t('Please wait...');
        }

        /**
         * Parses the post-data and updates the control's state accordingly.
         *
         * @return void
         */
        public function parsePostData(): void
        {
        }

        /**
         * Validates the wait icon (for now it just returns true)
         *
         * @return bool
         */
        public function validate(): bool
        {
            return true;
        }

        /**
         * Returns the HTML we have to send to the browser to render this wait icon
         * @return string HTML to be returned
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

                case 'default':
                default:
                // Three-color pastel ray spinner (12 pieces)
                    $bars = str_repeat('<span class="bar"></span>', 12);
                    $spinnerHtml =
                        '<span class="qc-wait-spinner-colors" style="' . $style .
                        '" role="status" aria-label="' . htmlspecialchars($this->strText) . '">' . $bars . '</span>';
                    break;
            }
            return $this->renderTag($this->strTagName, null, null, $spinnerHtml);
        }

        /**
         * Magic method to get the value of a property by its name.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property.
         * @throws Caller If the property does not exist or an error occurs during retrieval.
         * @throws \Exception
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                // APPEARANCE
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
         * Magic method to set the value of a property dynamically.
         *
         * @param string $strName The name of the property to be set.
         * @param mixed $mixValue The value to assign to the property.
         *
         * @return void
         * @throws InvalidCast If the value cannot be cast to the required type.
         * @throws Caller If the property does not exist or cannot be set.
         * @throws \Exception
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