<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\FormState;

    use Exception;
    use QCubed\Cryptography;
    use QCubed\Exception\Caller;
    use QCubed\ObjectBase;
    use QCubed\Project\Application;

    /**
     * Class RedisHandler
     *
     * This will store the formstate in a Redis DB.
     *
     * This offers a significant speed advantage over PHP SESSION because EACH form state
     * is saved using a unique key with Redis, and only the form state that is needed for loading will
     * be accessed (as opposed to with a session, ALL the form states are loaded into memory
     * every time). This is very similar to how DbBackedFormStateHandler works, only this one uses
     * Redis for storing the FormStates
     *
     * Garbage collection is handled by Redis automatically because we set the FormState keys with
     * an expiration time. The expiration time is determined by self::$intExpireFormstatesAfterDays
     *  variable, which is the number of days after which the FormState is automatically deleted by
     * Redis. As such, the minimum value should be 1.
     *
     * This handler can handle asynchronous calls.
     *
     * @package QCubed\FormState
     */
    class RedisHandler extends ObjectBase
    {
        /**
         * The number of days after which the formstate will expire.
         * Expiration will happen at the exact time (number specified here * 24 hours) after the creation.
         *
         * Value must be a positive integer
         *
         * @var integer
         */
        public static int $intExpireFormstatesAfterDays = 7;

        /** @var bool Whether to compress the formstate data. */
        public static bool $blnCompress = true;

        /**
         * @var bool Whether to base64 encode the formstate data.
         */
        public static bool $blnBase64 = false;

        /**
         * Returns the client using which the formstate handler can work
         *
         * @return \Predis\Client
         * @throws Caller
         */
        private static function GetClient(): \Predis\Client
        {
            if (!class_exists('Predis\Client')) {
                throw new Caller('Predis\' library needs to be installed for Redis Formstate Handler to work');
            }

            // There must be keys named 'parameters' and 'options' in the configuration
            if (defined('__REDIS_BACKED_FORM_STATE_HANDLER_CONFIG__')) {
                $objOptionsArray = unserialize(REDIS_BACKED_FORM_STATE_HANDLER_CONFIG);
                if (!array_key_exists('parameters', $objOptionsArray) || !array_key_exists('options', $objOptionsArray)) {
                    // The necessary keys do not exist
                    throw new Caller('The configuration parameters for creating predis client in the configuration file are wrong. The config array must contain the "parameters" and "options" keys');
                }

                return new Predis\Client($objOptionsArray['parameters'], $objOptionsArray['options']);
            } else {
                return new Predis\Client();
            }
        }

        /**
         * Save the formstate to the data store.
         *
         * @param string $strFormState The form state to be saved.
         * @param bool $blnBackButtonFlag Indicates whether the back button functionality is enabled.
         *
         * @return string The unique page ID associated with the saved form state.
         * @throws Exception If there is a failure in base64 encoding of the form state.
         */
        public static function save(string $strFormState, bool $blnBackButtonFlag): string
        {
            $objClient = self::GetClient();

            $strOriginal = $strFormState;

            // compress (if available)
            if (function_exists('gzcompress') && self::$blnCompress) {
                $strFormState = gzcompress($strFormState, 9);
            }

            if (
                defined('REDIS_BACKED_FORM_STATE_HANDLER_ENCRYPTION_KEY') &&
                defined('REDIS_BACKED_FORM_STATE_HANDLER_IV_HASH_KEY')
            ) {
                try {
                    $crypt = new Cryptography(REDIS_BACKED_FORM_STATE_HANDLER_ENCRYPTION_KEY, false, null,
                        REDIS_BACKED_FORM_STATE_HANDLER_IV_HASH_KEY);
                    $strFormState = $crypt->encrypt($strFormState);
                } catch (Exception $e) {
                }
            }

            if (self::$blnBase64) {
                $encoded = base64_encode($strFormState);
                if ($strFormState && !$encoded) {
                    throw new Exception ("Base64 Encoding Failed on " . $strOriginal);
                } else {
                    $strFormState = $encoded;
                }
            }

            $strPageId = '';
            if (!empty($_POST['Qform__FormState']) && Application::isAjax()) {
                // update the current form state if possible
                $strPageId = $_POST['Qform__FormState'];
            } else {
                // Figure Out Session Id (if applicable)
                $strSessionId = session_id();

                // Calculate a new unique Page Id
                $strPageId = md5(microtime());

                // Figure Out Page ID to be saved onto the database
                $strPageId = sprintf('%s_%s', $strSessionId, $strPageId);
            }

            if (self::$intExpireFormstatesAfterDays < 1 || self::$intExpireFormstatesAfterDays > 365) {
                self::$intExpireFormstatesAfterDays = 7;
            }

            $objClient->set('qc_formstate:' . $strPageId, $strFormState, 'ex',
                (self::$intExpireFormstatesAfterDays * 86400));

            // Return the Page Id
            return $strPageId;
        }

        /**
         * Loads and retrieves a serialized form state from a storage system using the provided form state identifier.
         *
         * @param string $strPostDataState The identifier for the form state to be loaded.
         *                                 This is typically a key that identifies the serialized data in the storage system.
         *                                 If the data does not exist or has expired, the method returns null.
         *
         * @return string|null The deserialized form state if successfully retrieved and processed, or null if it does not exist.
         * @throws Exception If the base64 decoding of the form state fails.
         *                   If decompression of the form state fails.
         *
         */
        public static function load(string $strPostDataState): ?string
        {
            $objClient = self::GetClient();

            $strPageId = $strPostDataState;

            $strSerializedForm = $objClient->get('qc_formstate:' . $strPageId);

            if (!$strSerializedForm) {
                // Form does not exist or it expired
                return null;
            }

            if (self::$blnBase64) {
                $strSerializedForm = base64_decode($strSerializedForm);

                if ($strSerializedForm === false) {
                    throw new Exception(message: "Failed decoding formstate " . false);
                }
            }

            if (defined('REDIS_BACKED_FORM_STATE_HANDLER_ENCRYPTION_KEY') && defined('__REDIS_BACKED_FORM_STATE_HANDLER_IV_HASH_KEY__')) {
                try {
                    $crypt = new Cryptography(REDIS_BACKED_FORM_STATE_HANDLER_ENCRYPTION_KEY, false, null,
                        REDIS_BACKED_FORM_STATE_HANDLER_IV_HASH_KEY);
                    $strSerializedForm = $crypt->decrypt($strSerializedForm);
                } catch (Exception $e) {
                }
            }

            if (function_exists('gzcompress') && self::$blnCompress) {
                try {
                    $strSerializedForm = gzuncompress($strSerializedForm);
                } catch (Exception $e) {
                    print ("Error on uncompress of page id " . $strPageId);
                    throw $e;
                }
            }

            return $strSerializedForm;
        }

        /**
         * @static
         *
         * If PHP SESSION is enabled, then this method will delete all formstate files specifically
         * for this SESSION user (and no one else). This can be used in lieu of or in addition to the
         * standard interval-based garbage collection mechanism.
         * Also, for standard web applications with logins, it might be a good idea to call
         * this method whenever the user logs out.
         * @throws Caller
         */
        public static function DeleteFormStateForSession(): void
        {
            $objClient = self::GetClient();
            // Figure Out Session Id (if applicable)
            $strSessionId = session_id();

            $objClient->eval("for i, name in impairs(redis.call('KEYS', KEYS[1])) do redis.call('DEL', name); end", 1,
                'qc_formstate:' . $strSessionId . '*');
        }
    }
