<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Session;

use QCubed\Exception\Caller;
use QCubed\ObjectBase;
use QCubed\Type;
use QCubed;

/**
 * Class DatabaseHandler
 *
 * Created by vaibhav on 1/28/12 (3:34 AM).
 *
 * This file contains the QDbBackedSessionHandler class.
 *
 * Relies on a SQL database table with the following columns:
 *    id - STRING primary key
 *  last_access_time - INT
 *  data - can be a BLOB or BINARY or VARBINARY or a TEXT. If TEXT, be sure to leave $blnBase64 on. If you are using
 *        a binary field, you can turn that off to save space. Make sure your column is capable of holding the maximum size of
 *        session data for your app, which depends on what you are putting in the $_SESSION variable.
 *
 * @package Sessions
 * @was QDbBackedSessionHandler
 */
class DatabaseHandler extends ObjectBase
{

    /**
     * @var int The index in the database array
     */
    protected static $intDbIndex;

    /**
     * @var string The table name to be used for saving sessions.
     */
    protected static $strTableName;

    /**
     * @var string The session name to be used for saving sessions.
     */
    protected static $strSessionName = '';

    /** @var bool Whether to base64 the session data. Required when storing data in a TEXT field. */
    public static $blnBase64 = true;

    /** @var bool Whether to compress the session data. */
    public static $blnCompress = true;


    /**
     * @static
     *
     * @param int $intDbIndex The index in the database array
     * @param string $strTableName The table name to be used for saving sessions.
     *
     * @return bool
     * @throws Exception|Caller|\QCubed\Exception\InvalidCast
     */
    public static function initialize($intDbIndex = 1, $strTableName = "qc_session")
    {
        self::$intDbIndex = Type::cast($intDbIndex, Type::INTEGER);
        self::$strTableName = Type::cast($strTableName, Type::STRING);
        // If the database index exists
        $objDatabase = QCubed\Database\Service::getDatabase(self::$intDbIndex);
        if (!$objDatabase) {
            throw new Caller('No database defined at DB_CONNECTION index ' . self::$intDbIndex . '. Correct your settings in configuration.inc.php.');
        }
        // see if the database contains a table with desired name
        if (!in_array(self::$strTableName, $objDatabase->getTables())) {
            throw new Caller('Table ' . self::$strTableName . ' not found in database at DB_CONNECTION index ' . self::$intDbIndex . '. Correct your settings in configuration.inc.php.');
        }
        // Set session handler functions
        $session_ok = session_set_save_handler(
            'QDbBackedSessionHandler::sessionOpen',
            'QDbBackedSessionHandler::sessionClose',
            'QDbBackedSessionHandler::sessionRead',
            'QDbBackedSessionHandler::sessionWrite',
            'QDbBackedSessionHandler::sessionDestroy',
            'QDbBackedSessionHandler::sessionGarbageCollect'
        );
        // could not register the session handler functions
        if (!$session_ok) {
            throw new Caller("session_set_save_handler function failed");
        }
        // Will be called before session ends.
        register_shutdown_function('session_write_close');
        return $session_ok;
    }

    /**
     * Open the session (used by PHP when the session handler is active)
     * @param string $save_path
     * @param string $session_name
     *
     * @return bool
     */
    public static function sessionOpen($save_path, $session_name)
    {
        self::$strSessionName = $session_name;
        // Nothing to do
        return true;
    }

    /**
     * Close the session (used by PHP when the session handler is active)
     * @return bool
     */
    public static function sessionClose()
    {
        // Nothing to do.
        return true;
    }

    /**
     * Read the session data (used by PHP when the session handler is active)
     * @param string $id
     *
     * @return string the session data, base64 decoded
     * @throws Caller
     */
    public static function sessionRead($id)
    {
        $id = self::$strSessionName . '.' . $id;
        $objDatabase = QCubed\Database\Service::getDatabase(self::$intDbIndex);
        $query = '
            SELECT
                ' . $objDatabase->escapeIdentifier('data') . '
            FROM
                ' . $objDatabase->escapeIdentifier(self::$strTableName) . '
            WHERE
                ' . $objDatabase->escapeIdentifier('id') . ' = ' . $objDatabase->sqlVariable($id);

        $result = $objDatabase->query($query);

        $result_row = $result->fetchRow();


        if (!$result_row) { // either the data was empty or the row was not found
            return '';
        }
        $strData = $result_row[0];

        /** A kludge to fix a particular problem. Would require a complete rewrite of our database adapters to do this right. */
        if (!static::$blnBase64 && strstr($objDatabase->Adapter, 'PostgreSql')) {
            if (function_exists('pg_unescape_bytea')) {
                $strData = pg_unescape_bytea($strData);
            } else {
                throw new Caller('pg_unescape_bytea method needed for DbBackedSessionHandler to operate on a PostgreSQL database. Please install the "pgsql" PHP extension.');
            }
        }

        if (!$strData) {
            return '';
        }

        if (self::$blnBase64) {
            $strData = base64_decode($strData);

            if ($strData === false) {
                throw new Exception("Failed decoding formstate " . $strData);
            }
        }

        // The session exists and was accessed. Return the data.
        if (defined('DB_BACKED_SESSION_HANDLER_ENCRYPTION_KEY')) {
            try {
                $crypt = new QCubed\Cryptography(DB_BACKED_SESSION_HANDLER_ENCRYPTION_KEY, false, null,
                    DB_BACKED_SESSION_HANDLER_HASH_KEY);
                $strData = $crypt->decrypt($strData);
            } catch (Exception $e) {
            }
        }

        if (self::$blnCompress) {
            $strData = gzuncompress($strData);
        }


        return $strData;
    }

    /**
     * Tells whether a session by given name exists or not (used by PHP when the session handler is active)
     * @param string $id Session ID
     *
     * @return bool does the session exist or not
     */
    public static function sessionExists($id)
    {
        $id = self::$strSessionName . '.' . $id;
        $objDatabase = QCubed\Database\Service::getDatabase(self::$intDbIndex);
        $query = '
            SELECT 1
            FROM
                ' . $objDatabase->escapeIdentifier(self::$strTableName) . '
            WHERE
                ' . $objDatabase->escapeIdentifier('id') . ' = ' . $objDatabase->sqlVariable($id);

        $result = $objDatabase->query($query);

        $result_row = $result->fetchArray();

        // either the data was empty or the row was not found
        return !empty($result_row);
    }

    /**
     * Write data to the session
     *
     * @param string $id The session ID
     * @param string $strSessionData Data to be written to the Session whose ID was supplied
     *
     * @return bool
     */
    public static function sessionWrite($id, $strSessionData)
    {
        if (empty($strSessionData)) {
            static::sessionDestroy($id);
            return true;
        }

        $strEncoded = $strSessionData;

        if (self::$blnCompress) {
            $strEncoded = gzcompress($strSessionData);
        }

        if (defined('DB_BACKED_SESSION_HANDLER_ENCRYPTION_KEY')) {
            try {
                $crypt = new QCubed\Cryptography(DB_BACKED_SESSION_HANDLER_ENCRYPTION_KEY, false, null,
                    DB_BACKED_SESSION_HANDLER_HASH_KEY);
                $strEncoded = $crypt->encrypt($strEncoded);
            } catch (Exception $e) {
            }
        }

        if (self::$blnBase64) {
            $encoded = base64_encode($strEncoded);
            if ($strEncoded && !$encoded) {
                throw new Exception("Base64 Encoding Failed on " . $strSessionData);
            } else {
                $strEncoded = $encoded;
            }
        }

        assert(!empty($strEncoded));

        $id = self::$strSessionName . '.' . $id;
        $objDatabase = QCubed\Database\Service::getDatabase(self::$intDbIndex);
        $objDatabase->insertOrUpdate(
            self::$strTableName,
            array(
                'data' => $strEncoded,
                'last_access_time' => time(),
                'id' => $id
            ),
            'id');
        return true;
    }

    /**
     * Destroy the session for a given session ID
     *
     * @param string $id The session ID
     *
     * @return bool
     */
    public static function sessionDestroy($id)
    {
        $id = self::$strSessionName . '.' . $id;
        $objDatabase = QCubed\Database\Service::getDatabase(self::$intDbIndex);
        $query = '
            DELETE FROM
                ' . $objDatabase->escapeIdentifier(self::$strTableName) . '
            WHERE
                ' . $objDatabase->escapeIdentifier('id') . ' = ' . $objDatabase->sqlVariable($id);

        $objDatabase->nonQuery($query);
        return true;
    }

    /**
     * Garbage collect session data (delete/destroy sessions which are older than the max allowed lifetime)
     *
     * @param int $intMaxSessionLifetime The max session lifetime (in seconds)
     *
     * @return bool
     */
    public static function sessionGarbageCollect($intMaxSessionLifetime)
    {
        $objDatabase = QCubed\Database\Service::getDatabase(self::$intDbIndex);
        $old = time() - $intMaxSessionLifetime;

        $query = '
            DELETE FROM
                ' . $objDatabase->escapeIdentifier(self::$strTableName) . '
            WHERE
                ' . $objDatabase->escapeIdentifier('last_access_time') . ' < ' . $objDatabase->sqlVariable($old);

        $objDatabase->nonQuery($query);
        return true;
    }
}
