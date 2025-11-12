<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\FormState;

    use QCubed\ObjectBase;
    use QCubed\Project\Application;

    /**
     * Class FileHandler
     *
     * This will store the formstate in a pre-specified directory on the file system.
     * This offers a significant speed advantage over PHP SESSION because EACH form state
     * is saved in its own file, and only the form state that is needed for loading will
     * be accessed (as opposed to with a session, ALL the form states are loaded into memory
     * every time).
     *
     * The downside is that because it doesn't utilize PHP's session management subsystem,
     * this class must take care of its own garbage collection/deleting of old/outdated
     * formstate files.
     *
     * Because the index is randomly generated and MD5-hashed, there is no benefit from
     * encrypting it -- therefore, the QForm encryption preferences are ignored when using
     * FileHandler.
     *
     * This formstate handler is compatible with asynchronous ajax calls.
     *
     * @package QCubed\FormState
     */
    class FileHandler extends ObjectBase
    {
        /**
         * The PATH where the FormState files should be saved
         *
         * @var string StatePath
         */
        public static string $StatePath = FILE_FORM_STATE_HANDLER_PATH;

        /**
         * The filename prefix to be used by all FormState files
         *
         * @var string FileNamePrefix
         */
        public static string $FileNamePrefix = 'qformstate_';

        /**
         * The interval of hits before the garbage collection should kick in to delete
         * old FormState files, or 0 if it should never be run.  The higher the number,
         * the less often it runs (better aggregated-average performance, but requires more
         * hard drive space).  The lower the number, the more often it runs (slower aggregated-average
         * performance, but requires less hard drive space).
         *
         * @var integer GarbageCollectInterval
         */
        public static int $GarbageCollectInterval = 200;

        /**
         * The minimum age (in days) a formstate file has to be in order to be considered old enough
         * to be garbage collected.  So if set to "1.5", then all formstate files older than 1.5 days
         * will be deleted when the GC interval is kicked off.
         *
         * Obviously, if the GC Interval is set to 0, then this GC Days Old value will never be used.
         *
         * @var integer GarbageCollectDaysOld
         */
        public static int $GarbageCollectDaysOld = 2;

        /**
         * If PHP SESSION is enabled, then this method will delete all formstate files specifically
         * for this SESSION user (and no one else).  This can be used in lieu of or in addition to the
         * standard interval-based garbage collection mechanism.
         *
         * Also, for standard web applications with logins, it might be a good idea to call
         * this method whenever the user logs out.
         */
        public static function deleteFormStateForSession(): void
        {
            // Figure Out Session Id (if applicable)
            $strSessionId = session_id();

            $strPrefix = self::$FileNamePrefix . $strSessionId;

            // Go through all the files
            if (strlen($strSessionId)) {
                $objDirectory = dir(self::$StatePath);
                while (($strFile = $objDirectory->read()) !== false) {
                    $intPosition = strpos($strFile, $strPrefix);
                    if (($intPosition !== false) && ($intPosition == 0)) {
                        unlink(sprintf('%s/%s', self::$StatePath, $strFile));
                    }
                }
            }
        }

        /**
         * This will delete all the formstate files that are older than $GarbageCollectDaysOld
         * days old.
         */
        public static function garbageCollect(): void
        {
            // Go through all the files
            $objDirectory = dir(self::$StatePath);
            while (($strFile = $objDirectory->read()) !== false) {
                if (!self::$FileNamePrefix) {
                    $intPosition = 0;
                } else {
                    $intPosition = strpos($strFile, self::$FileNamePrefix);
                }
                if (($intPosition !== false) && ($intPosition == 0)) {
                    $strFile = sprintf('%s/%s', self::$StatePath, $strFile);
                    $intTimeInterval = time() - (60 * 60 * 24 * self::$GarbageCollectDaysOld);
                    $intModifiedTime = filemtime($strFile);

                    if ($intModifiedTime < $intTimeInterval) {
                        unlink($strFile);
                    }
                }
            }
        }

        /**
         * Saves the given form state to the file system and returns the generated Page ID.
         *
         * @param string $strFormState The serialized form state data to be saved.
         * @param bool $blnBackButtonFlag Indicates whether the save operation is being performed for the back button functionality.
         * @return string The generated Page ID for the saved form state.
         */
        public static function save(string $strFormState, bool $blnBackButtonFlag): string
        {
            // First, see if we need to perform garbage collection
            if (self::$GarbageCollectInterval > 0) {
                // This is a crude interval-tester, but it works
                if (rand(1, self::$GarbageCollectInterval) == 1) {
                    self::garbageCollect();
                }
            }

            // Compress (if available)
            if (function_exists('gzcompress')) {
                $strFormState = gzcompress($strFormState, 9);
            }

            // Figure Out Session Id (if applicable)
            $strSessionId = session_id();

            if (!empty($_POST['Qform__FormState']) && Application::isAjax()) {
                $strPageId = $_POST['Qform__FormState'];    // reuse old page id
            } else {
                // Calculate a new unique Page Id
                $strPageId = md5(microtime());
            }

            // Figure Out the FilePath
            $strFilePath = sprintf('%s/%s%s_%s',
                self::$StatePath,
                self::$FileNamePrefix,
                $strSessionId,
                $strPageId);

            // Save THIS formstate to the file system
            // NOTE: if gzcompress is used, we are saving the *BINARY* data stream of the compressed formstate
            // In theory, this SHOULD work.  But if there is a webserver/os/php version that doesn't like
            // binary session streams, you can first base64_encode before saving to session (see note below).
            file_put_contents($strFilePath, $strFormState);

            // Return the Page Id
            // Because of the MD5-random nature of the Page ID, there is no need/reason to encrypt it
            return $strPageId;
        }

        /**
         * Loads the serialized form state from the file system based on the provided post-data state.
         *
         * @param string $strPostDataState The state string representing the post-data, used to identify the corresponding form state file.
         * @return string|null The deserialized form state as a string if the file exists, or null if the file is not found.
         */
        public static function load(string $strPostDataState): ?string
        {
            // Pull Out strPageId
            $strPageId = $strPostDataState;

            // Figure Out Session Id (if applicable)
            $strSessionId = session_id();

            // Figure Out the FilePath
            $strFilePath = sprintf('%s/%s%s_%s',
                self::$StatePath,
                self::$FileNamePrefix,
                $strSessionId,
                $strPageId);

            if (file_exists($strFilePath)) {
                // Pull FormState from file system
                // NOTE: if gzcompress is used, we are restoring the *BINARY* data stream of the compressed formstate
                // In theory, this SHOULD work.  But if there is a webserver/os/php version that doesn't like
                // binary session streams, you can first base64_decode before restoring from a session (see note above).
                $strSerializedForm = file_get_contents($strFilePath);

                // Uncompress (if available)
                if (function_exists('gzcompress')) {
                    $strSerializedForm = gzuncompress($strSerializedForm);
                }

                return $strSerializedForm;
            } else {
                return null;
            }
        }
    }
