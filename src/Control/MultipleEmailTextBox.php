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
    use QCubed\Exception\InvalidCast;
    use Throwable;
    use QCubed\Project\Control\TextBox;
    use QCubed\Type;

    /**
     * The MultipleEmailTextBox class validates and sanitizes emails.
     *
     * @property array $AllowedTlds
     * An array of allowed top-level domains (TLDs) used to validate email addresses.
     * Can be set manually or automatically loaded from the IANA list or a fallback list.
     *
     * @package QCubed\Control
     */
    class MultipleEmailTextBox extends TextBox
    {
        /** @var string */
        protected const string TLD_FILE = QCUBED_CACHE_DIR . '/iana-tld-list.txt';

        /** @var array|null */
        protected ?array $arrAllowedTlds = null;

        /**
         * Ensures that the TLD file exists locally. If the file does not exist, it attempts to fetch data from the IANA URL
         * and creates the file. If fetching the data fails, a default set of TLDs is written to the file.
         *
         * @return void
         */
        private function ensureTldFileExists(): void
        {
            $file = self::TLD_FILE;
            if (!is_file($file)) {
                $ianaUrl = 'https://data.iana.org/TLD/tlds-alpha-by-domain.txt';
                $data = file_get_contents($ianaUrl);

                if (!$data) {
                    $data = implode("\n", ['com', 'org', 'net', 'info', 'io', 'tv', 'edu', 'gov']);
                }
                file_put_contents($file, $data);
            }
        }

        /**
         * Retrieves the list of allowed top-level domains (TLDs).
         *
         * This method checks if a predefined list of TLDs is available.
         * If not, it attempts to read from a specific file. If the file
         * is readable, it processes the file content to build the list of TLDs.
         * If the file is not accessible, a default list of TLDs is returned as a fallback.
         *
         * @return array An array of allowed TLDs, in lowercase.
         */
        public function getAllowedTlds(): array
        {
            if ($this->arrAllowedTlds !== null) {
                return $this->arrAllowedTlds;
            }

            $this->ensureTldFileExists();
            $file = self::TLD_FILE;

            if (is_readable($file)) {
                $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                return array_map('strtolower', array_filter(array_map('trim', $lines), fn($x) => $x && $x[0] !== '#'));
            }

            // Last backup
            return ['com','org','net','info','io','tv','edu','gov'];
        }

        /**
         * Constructor method for initializing the object.
         *
         * @param ControlBase|FormBase $objParentObject The parent control or form object.
         * @param string|null $strControlId Optional control ID for the instance.
         *
         * @throws Caller
         */
        public function __construct(ControlBase|FormBase $objParentObject, ?string $strControlId = null)
        {
            parent::__construct($objParentObject, $strControlId);
            $this->strTextMode = TextBoxBase::EMAIL;
        }

        /**
         * Validates the current state or condition.
         *
         * @return bool Returns true if validation is successful, otherwise false.
         */
        public function validate(): bool
        {
            // The truth is: everyone uses the outputs of getGroupedEmails() themselves,
            // do not write or set anything automatically in this method!
            return true;
        }

        /**
         * Groups input emails into valid and invalid categories.
         *
         * This method processes a text input containing email addresses,
         * validates each email, and categorizes them based on their validity
         * and allowed top-level domains (TLDs).
         *
         * @return array An associative array with two keys:
         *               - 'valid': an array of valid email addresses.
         *               - 'invalid': an array of invalid email addresses or those with disallowed TLDs.
         */
        public function getGroupedEmails(): array
        {
            $result = ['valid' => [], 'invalid' => []];
            $input = trim($this->strText ?? '');
            if ($input === '') return $result;

            $allowedTlds = $this->getAllowedTlds();
            $emails = preg_split('/[\s,;\r\n]+/', $input, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($emails as $email) {
                $email = trim($email);
                if ($email === '') continue;
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $domain = substr(strrchr($email, "@"), 1);
                    $tld = strtolower(pathinfo($domain, PATHINFO_EXTENSION));
                    if (in_array($tld, $allowedTlds, true)) {
                        $result['valid'][] = $email;
                    } else {
                        $result['invalid'][] = $email;
                    }
                } else {
                    $result['invalid'][] = $email;
                }
            }
            return $result;
        }

        /**
         * Magic method to retrieve the value of a property.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property.
         * @throws Caller If the property does not exist or is inaccessible.
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case "AllowedTlds": return $this->arrAllowedTlds;

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
         * Magic method to set the value of a property.
         *
         * @param string $strName The name of the property being set.
         * @param mixed $mixValue The value to assign to the property.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         * @throws Throwable
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case "AllowedTlds":
                    try {
                        $this->arrAllowedTlds = Type::cast($mixValue, Type::ARRAY_TYPE);
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

        /**
         * Cron script or manual update for TLD list.
         *
         * Example usage (can be scheduled with cron or run manually):
         * Downloads the list of TLDs to the QCubed CACHE directory.
         *
         * $ianaUrl = 'https://data.iana.org/TLD/tlds-alpha-by-domain.txt';
         * $cacheFile = QCUBED_CACHE_DIR . '/iana-tld-list.txt';
         *
         * $data = @file_get_contents($ianaUrl);
         * if ($data) {
         *     file_put_contents($cacheFile, $data);
         * } else {
         *     // Add your logging here if needed
         *     echo "Failed to load IANA TLD list.\n";
         * }
         *
         */

    }
