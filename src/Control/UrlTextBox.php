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

    use QCubed\Exception\Caller;
    use QCubed\Project\Control\TextBox;

    /**
     * Class UrlTextBox
     *
     * A subclass of TextBox that validates and sanitizes urls.
     * @was QUrlTextBox
     * @package QCubed\Control
     */
    class UrlTextBox extends TextBox
    {
        /** @var int|null */
        protected ?int $intSanitizeFilter = FILTER_SANITIZE_URL;
        /** @var int|null */
        protected ?int $intValidateFilter = FILTER_VALIDATE_URL;

        /**
         * Constructor
         *
         * @param ControlBase|FormBase $objParentObject
         * @param null|string $strControlId
         * @throws Caller
         */
        public function __construct(ControlBase|FormBase $objParentObject, ?string $strControlId = null)
        {
            parent::__construct($objParentObject, $strControlId);
            $this->strTextMode = self::URL;
        }

        /**
         * Validates the current control's value.
         *
         * This method checks if the parent's validation passes and further evaluates the current value
         * based on specific criteria such as URL format and domain existence. If the validation fails,
         * an appropriate error message is set.
         *
         * @return bool Returns true if the value is valid, false otherwise.
         */
        public function validate(): bool
        {
            $blnValid = parent::validate();

            if ($this->intValidateFilter && $this->strText !== '') {
                $validateOptions = $this->mixValidateFilterOptions ?? 0;

                if (!filter_var($this->strText, FILTER_VALIDATE_URL)) {
                    $this->ValidationError = t('The URL is not in the correct format!');
                    $blnValid = false;
                } else {
                    $host = parse_url($this->strText, PHP_URL_HOST);
                    if (!$host || !(checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA'))) {
                        $this->ValidationError = t('Domain does not exist!');
                        $blnValid = false;
                    }
                }
            }

            return $blnValid;
        }
    }