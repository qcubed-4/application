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
    use QCubed\ObjectBase;
    use QCubed\Cryptography;
    use QCubed\Database;
    use QCubed\Project\Application;

    /**
     * Class DatabaseHandler
     *
     * This will store the formstate in a pre-specified table in the DB.
     * This offers a significant speed advantage over PHP SESSION because EACH form state
     * is saved in its own row in the DB, and only the form state that is needed for loading will
     * be accessed (as opposed to with a session, ALL the form states are loaded into memory
     * every time).
     *
     * The downside is that because it doesn't utilize PHP's session management subsystem,
     * this class must take care of its own garbage collection/deleting of old/outdated
     * formstate files.
     *
     * Because the index is randomly generated and MD5-hashed, there is no benefit from
     * encrypting it -- therefore, the QForm encryption preferences are ignored when using
     * QFileFormStateHandler.
     *
     * This handler can handle asynchronous calls.
     *
     * The table used should have the following fields:
     * 1. page_id: varchar(MAX_PAGE_SIZE) - Substitute the maximum size, which depends on your session_id algorithm (MAX_SESSION_SIZE + 33 is safe, see below).
     * 2. save_time: integer
     * 3. state_data: text
     * 4. session_id: varchar(MAX_SESSION_SIZE) - Substitute the maximum session id size, which depends on session id algorithm.
     *    PHP gives you some control over how you create session IDs, so be aware of the maximum size it might generate here.
     *      45 is probably safe for now, but if you add a prefix to your session_ids, then use a bigger number.
     *
     * @package QCubed\FormState
     * @was QDbBackedFormStateHandler
     */
    class DatabaseHandler extends ObjectBase
    {
        /**
         * The database index in configuration.inc.php where the formstates have to be managed
         */
        public static int $intDbIndex = DB_BACKED_FORM_STATE_HANDLER_DB_INDEX;

        /**
         * The table name which will handle the formstates. It must have the following columns:
         */
        public static string $strTableName = DB_BACKED_FORM_STATE_HANDLER_TABLE_NAME ;
        /**
         * The interval of hits before the garbage collection should kick in to delete
         * old FormState files, or 0 if it should never be run.  The higher the number,
         * the less often it runs (better aggregated-average performance, but requires more
         * hard drive space).  The lower the number, the more often it runs (slower aggregated-average
         * performance, but requires less hard drive space).
         * @var integer GarbageCollectInterval
         */
        public static int $intGarbageCollectOnHitCount = 20000;

        /**
         * The minimum age (in days) a formstate file has to be in order to be considered old enough
         * to be garbage collected.  So if set to "1.5", then all formstate files older than 1.5 days
         * will be deleted when the GC interval is kicked off.
         * Obviously, if the GC Interval is set to 0, then this GC Days Old value will never be used.
         * @var integer GarbageCollectDaysOld
         */
        public static int $intGarbageCollectDaysOld = 2;

        /** @var bool Whether to compress the formstate data. */
        public static bool $blnCompress = true;

        /** @var bool Whether to base64 encode the formstate data. Encoding is required if stored in a TEXT field. */
        public static bool $blnBase64 = false;


        /**
         * @static
         * This function is responsible for removing the old values from
         */
        public static function garbageCollect(): void
        {
            // It doesn't perfect and not sure but should be executed at expected intervals
            $objDatabase = Database\Service::getDatabase(self::$intDbIndex);
            $query = '
                                DELETE FROM
                                        ' . $objDatabase->escapeIdentifier(self::$strTableName) . '
                                WHERE
                                            ' . $objDatabase->escapeIdentifier('save_time') . ' < ' . $objDatabase->sqlVariable(time() - 60 * 60 * 24 * self::$intGarbageCollectDaysOld);

            $objDatabase->nonQuery($query);
        }

        /**
         * If PHP SESSION is enabled, then this method will delete all formstate files specifically
         * for this SESSION user (and no one else).  This can be used in lieu of or in addition to the
         * standard interval-based garbage collection mechanism.
         * Also, for standard web applications with logins, it might be a good idea to call
         * this method whenever the user logs out.
         */
        public static function deleteFormStateForSession(): void
        {
            // Figure Out Session Id (if applicable)
            $strSessionId = session_id();

            //Get a database
            $objDatabase = Database\Service::getDatabase(self::$intDbIndex);
            // Create the query
            $query = '
                            DELETE FROM
                                    ' . $objDatabase->escapeIdentifier(self::$strTableName) . '
                            WHERE
                                    ' . $objDatabase->escapeIdentifier('session_id') . ' = ' . $objDatabase->sqlVariable($strSessionId);

            $objDatabase->nonQuery($query);
        }

        /**
         * Saves the given form state into the database and returns a unique identifier for it.
         *
         * @param string $strFormState The form state data to be saved.
         * @param bool $blnBackButtonFlag A flag indicating whether the back button was pressed.
         * @return string The unique identifier (Page ID) of the saved form state.
         * @throws Exception If Base64 encoding fails.
         */
        public static function save(string $strFormState, bool $blnBackButtonFlag): string
        {
            $objDatabase = Database\Service::getDatabase(self::$intDbIndex);
            $strOriginal = $strFormState;

            // compress (if available)
            if (function_exists('gzcompress') && self::$blnCompress) {
                $strFormState = gzcompress($strFormState, 9);
            }

            if (defined('QCUBED_CRYPTOGRAPHY_DEFAULT_KEY')) {
                try {
                    $crypt = new Cryptography(QCUBED_CRYPTOGRAPHY_DEFAULT_KEY, false, null,
                        QCUBED_CRYPTOGRAPHY_DEFAULT_CIPHER);
                    $strFormState = $crypt->encrypt($strFormState);
                } catch (Exception) {
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

            if (!empty($_POST['Qform__FormState']) && Application::isAjax()) {
                // update the current form state if possible
                $strPageId = $_POST['Qform__FormState'];

                $strQuery = '
                            UPDATE
                                    ' . $objDatabase->escapeIdentifier(self::$strTableName) . '
                            SET
                                    ' . $objDatabase->escapeIdentifier('save_time') . ' = ' . $objDatabase->sqlVariable(time()) . ',
                                    ' . $objDatabase->escapeIdentifier('state_data') . ' = ' . $objDatabase->sqlVariable($strFormState) . '
                            WHERE
                                    ' . $objDatabase->escapeIdentifier('page_id') . ' = ' . $objDatabase->sqlVariable($strPageId);

                $objDatabase->nonQuery($strQuery);
                if ($objDatabase->AffectedRows > 0) {
                    return $strPageId;    // Successfully updated the current record. No need to create a new one.
                }
            }
            // First, see if we need to perform garbage collection
            // Decide for garbage collection
            if ((self::$intGarbageCollectOnHitCount > 0) && (rand(1, self::$intGarbageCollectOnHitCount) == 1)) {
                self::garbageCollect();
            }

            // Figure Out Session Id (if applicable)
            $strSessionId = session_id();

            // Calculate a new unique Page Id
            $strPageId = md5(microtime());

            // Figure Out Page ID to be saved onto the database
            $strPageId = sprintf('%s_%s',
                $strSessionId,
                $strPageId);

            // Save THIS formstate to the database
            //Get database
            // Create the query
            $strQuery = '
                            INSERT INTO
                                    ' . $objDatabase->escapeIdentifier(self::$strTableName) . '
                            (
                                    ' . $objDatabase->escapeIdentifier('page_id') . ',
                                    ' . $objDatabase->escapeIdentifier('session_id') . ',
                                    ' . $objDatabase->escapeIdentifier('save_time') . ',
                                    ' . $objDatabase->escapeIdentifier('state_data') . '
                            )
                            VALUES
                            (
                                    ' . $objDatabase->sqlVariable($strPageId) . ',
                                    ' . $objDatabase->sqlVariable($strSessionId) . ',
                                    ' . $objDatabase->sqlVariable(time()) . ',
                                    ' . $objDatabase->sqlVariable($strFormState) . '
                            )';

            $objDatabase->nonQuery($strQuery);

            // Return the Page Id
            // Because of the MD5-random nature of the Page ID, there is no need/reason to encrypt it
            return $strPageId;
        }

        /**
         * Load the serialized form state data for a given post-data state.
         *
         * @param string $strPostDataState The post-data state identifier used to fetch the stored form state data.
         * @return string|null Returns the unserialized form state data as a string, or null if no corresponding data is found.
         * @throws Exception Throws an exception if decoding or decompression of the form state data fails.
         */
        public static function load(string $strPostDataState): ?string
        {
            // Pull Out strPageId
            $strPageId = $strPostDataState;

            //Get a database
            $objDatabase = Database\Service::getDatabase(self::$intDbIndex);
            // The query to run
            $strQuery = '
                            SELECT
                                    ' . $objDatabase->escapeIdentifier('state_data') . '
            FROM
                                    ' . $objDatabase->escapeIdentifier(self::$strTableName) . '
                            WHERE
                                    ' . $objDatabase->escapeIdentifier('page_id') . ' = ' . $objDatabase->sqlVariable($strPageId);

            if ($strSessionId = session_id()) {
                $strQuery .= ' AND ' . $objDatabase->escapeIdentifier('session_id') . ' = ' . $objDatabase->sqlVariable($strSessionId);
            }


            // Perform the Query
            $objDbResult = $objDatabase->query($strQuery);

            $strFormStateRow = $objDbResult->fetchRow()[0];

            if (empty($strFormStateRow)) {
                // The formstate with that page ID was not found, or the session expired.
                return null;
            }
            $strSerializedForm = $strFormStateRow;


            if (self::$blnBase64) {
                $strSerializedForm = base64_decode($strSerializedForm);

                if ($strSerializedForm === false) {
                    throw new Exception("Failed decoding formstate " . false);
                }
            }

            if (defined('QCUBED_CRYPTOGRAPHY_DEFAULT_KEY')) {
                try {
                    $crypt = new Cryptography(QCUBED_CRYPTOGRAPHY_DEFAULT_KEY, false, null,
                        QCUBED_CRYPTOGRAPHY_DEFAULT_CIPHER);
                    $strSerializedForm = $crypt->decrypt($strSerializedForm);
                } catch (Exception) {
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
    }
