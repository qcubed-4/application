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
use QCubed\Project\Control\FormBase as QForm;
use QCubed\Cryptography;

/**
 * Class SessionHandler
 *
 * Session-based FormState handler. Use PHP Sessions to store the form state.
 *
 * Store the variables in the following format:
 * $_SESSION[static::SESSION_KEY][form_uniquid][state#]
 *   where the form uniquid is a unique id that sticks with the window that the
 *   form is on, and state# is the formstate associated with that window. Multiple
 *   formstates need to be saved to support the browser back button.
 *
 * If requested by QForm, the index will be encrypted.
 *
 * This incorporates a system of garbage collection that will allow for at most BackButtonMax
 * formstates to be saved in the session.
 *
 * This handler is compatible with asynchronous ajax calls.
 *
 * @package QCubed\FormState
 */
class SessionHandler extends ObjectBase
{
    const SESSION_KEY = 'qformstate';
    
    public static int $BackButtonMax = 20; // maximum number of back button states we remember

    public static string $EncryptionHashKey = '23c&jhd';   // Change this if using encryption is you want. Changing it does not enhance security all that much.

    /**
     * Saves the given form state into the session and returns an identifier for the saved state.
     *
     * This method compresses the form state if compression is available, manages the session data
     * for the form state, supports back button functionality, and can encrypt/decrypt the form state
     * using a configured encryption key.
     *
     * @param string $strFormState The serialized form state to save.
     * @param bool $blnBackButtonFlag Whether to allow back button usage to reuse or discard the state.
     * @return string The identifier for the saved form state, encrypted if an encryption key is defined.
     * @throws \QCubed\Exception\Cryptography
     */
    public static function save(string $strFormState, bool $blnBackButtonFlag): string
    {
        // Compress (if available)
        if (function_exists('gzcompress')) {
            $strFormState = gzcompress($strFormState, 9);
        }

        if (empty($_POST['Qform__FormState'])) {
            // no prior form state, so create a new one.
            $strFormInstance = uniqid();
            $intFormStateIndex = 1;
        } else {
            $strPriorState = $_POST['Qform__FormState'];

            if (!is_null(QForm::$EncryptionKey)) {
                // Use \QCubed\Cryptography to Decrypt
                $objCrypto = new Cryptography(QForm::$EncryptionKey, true);
                $strPriorState = $objCrypto->decrypt($strPriorState);
            }

            $a = explode('_', $strPriorState);
            if (count($a) == 2 &&
                is_numeric($a[1]) &&
                !empty($_SESSION[static::SESSION_KEY][$a[0]]['index'])
            ) {
                $strFormInstance = $a[0];
                $intFormStateIndex = $_SESSION[static::SESSION_KEY][$a[0]]['index'];
                if ($blnBackButtonFlag) { // can we reuse current state info?
                    $intFormStateIndex++; // nope

                    // try to garbage collect
                    if (count($_SESSION[static::SESSION_KEY][$a[0]]) > self::$BackButtonMax) {
                        foreach ($_SESSION[static::SESSION_KEY][$a[0]] as $key => $val) {
                            if (is_numeric($key) && $key < $_SESSION[static::SESSION_KEY][$a[0]]['index'] - self::$BackButtonMax) {
                                unset ($_SESSION[static::SESSION_KEY][$a[0]][$key]);
                            }
                        }
                    }
                }
            } else {
                // couldn't find old session variables, so create a new one
                $strFormInstance = uniqid();
                $intFormStateIndex = 1;
            }
        }

        // Setup current state variable
        if (empty($_SESSION[static::SESSION_KEY])) {
            $_SESSION[static::SESSION_KEY] = array();
        }
        if (empty($_SESSION[static::SESSION_KEY][$strFormInstance])) {
            $_SESSION[static::SESSION_KEY][$strFormInstance] = array();
        }

        $_SESSION[static::SESSION_KEY][$strFormInstance]['index'] = $intFormStateIndex;
        $_SESSION[static::SESSION_KEY][$strFormInstance][$intFormStateIndex] = $strFormState;

        $strPostDataState = $strFormInstance . '_' . $intFormStateIndex;

        // Return StateIndex
        if (!is_null(QForm::$EncryptionKey)) {
            // Use \QCubed\Cryptography to Encrypt
            $objCrypto = new Cryptography(QForm::$EncryptionKey, true, null, self::$EncryptionHashKey);
            return $objCrypto->encrypt($strPostDataState);
        } else {
            return $strPostDataState;
        }
    }

    /**
     * Loads the serialized form data from the session based on the provided encoded state string.
     *
     * @param string $strPostDataState The encoded state string containing the reference to the serialized form data.
     * @return string|null Returns the serialized form data as a string or null if the data cannot be retrieved or is invalid.
     * @throws \QCubed\Exception\Cryptography
     */
    public static function load(string $strPostDataState): ?string
    {
        // Pull Out intStateIndex
        if (!is_null(QForm::$EncryptionKey)) {
            // Use \QCubed\Cryptography to Decrypt
            $objCrypto = new Cryptography(QForm::$EncryptionKey, true, null, self::$EncryptionHashKey);
            $strPostDataState = $objCrypto->decrypt($strPostDataState);
        }

        $a = explode('_', $strPostDataState);
        if (count($a) == 2 &&
            is_numeric($a[1]) &&
            !empty($_SESSION[static::SESSION_KEY][$a[0]][$a[1]])
        ) {
            $strSerializedForm = $_SESSION[static::SESSION_KEY][$a[0]][$a[1]];
        } else {
            return null;
        }

        // Uncompress (if available)
        // NOTE: if gzcompress is used, we are restoring the *BINARY* data stream of the compressed formstate
        // In theory, this SHOULD work.  But if there is a webserver/os/php version that doesn't like
        // binary session streams, you can first base64_decode before restoring from a session (see note above).
        if (function_exists('gzcompress')) {
            $strSerializedForm = gzuncompress($strSerializedForm);
        }

        return $strSerializedForm;
    }
}