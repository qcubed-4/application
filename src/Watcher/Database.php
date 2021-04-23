<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Watcher;

/**
 * Class Watcher\Database
 *
 * This is a helper class that allows controls to watch a database table
 * and automatically update the UI when changes are detected. It works together with the codegened
 * model classes, the controls, and the Form class to draw when needed.
 *
 * It relies on the presence of a SQL database table in the system. Define the following
 * in your Watcher subclass file to tell it which tables to use:
 *        static::$intDbIndex - The database index to look for the table
 *        static::$strTableName - The name of the table.
 *
 * To create the database, use the following SQL:
 * CREATE TABLE IF NOT EXISTS qc_watchers (
 * table_key varchar(200) NOT NULL,
 * ts varchar(40) NOT NULL,
 * PRIMARY KEY (table_key)
 * );
 *
 *
 * @package QCubed\Watcher
 * @was QWatcherDB
 */

abstract class Database extends WatcherBase
{
    /*** The following two variables must be initialized by a subclass **/
    /** @var  integer The database index to use */
    protected static $intDbIndex;
    /** 
     * @var  string
     * The table name which will keep info about changed tables. It must have the following columns:
     * 1. table_key: varchar(largest key size)
     * 2. time: varchar(30)     */
    protected static $strTableName;

    /**
     * @var string[] Caches results of database lookups. Will not be saved with the formstate.
     */
    private static $strKeyCaches = null;

    /**
     * Override
     */
    public function makeCurrent()
    {
        $objDatabase = \QCubed\Database\Service::getDatabase(static::$intDbIndex);
        $strIn = implode(',', $objDatabase->escapeValues(array_keys($this->strWatchedKeys)));
        $strSQL = sprintf("SELECT * FROM %s WHERE %s in (%s)",
            $objDatabase->escapeIdentifier(static::$strTableName),
            $objDatabase->escapeIdentifier("table_key"),
            $strIn);

        $objDbResult = $objDatabase->query($strSQL);

        while ($strRow = $objDbResult->fetchRow()) {
            $this->strWatchedKeys[$strRow[0]] = $strRow[1];
        }
    }

    /**
     * Returns true if the watcher is up to date, and false if something has
     * changed. Caches the results so it only hits the database minimally for each
     * read.
     *
     * @return bool
     */
    public function isCurrent()
    {
        // check cache
        $ret = true;

        foreach ($this->strWatchedKeys as $key => $ts) {
            if (!isset (self::$strKeyCaches[$key])) {
                $ret = false;
                break;
            }
            if (self::$strKeyCaches[$key] !== $ts) {
                return false;
            }
        }
        if ($ret) {
            return true;
        } // cache had everything we were looking for

        // cache did not have what we were looking for, so check database
        $objDatabase = \QCubed\Database\Service::getDatabase(static::$intDbIndex);
        $strIn = implode(',', $objDatabase->escapeValues(array_keys($this->strWatchedKeys)));
        $strSQL = sprintf("SELECT * FROM %s WHERE %s in (%s)",
            $objDatabase->escapeIdentifier(static::$strTableName),
            $objDatabase->escapeIdentifier("table_key"),
            $strIn);

        $objDbResult = $objDatabase->query($strSQL);

        // fill cache and check result
        while ($strRow = $objDbResult->fetchRow()) {
            self::$strKeyCaches[$strRow[0]] = $strRow[1];
            if ($ret && $this->strWatchedKeys[$strRow[0]] !== $strRow[1]) {
                $ret = false;
            }
        }

        return $ret;
    }

    /**
     *
     * @param string $strDbName
     * @param string $strTableName
     */
    static public function markTableModified($strDbName, $strTableName)
    {
        $key = static::getKey($strDbName, $strTableName);
        $objDatabase = \QCubed\Database\Service::getDatabase(static::$intDbIndex);
        $time = microtime();

        $objDatabase->insertOrUpdate(static::$strTableName,
            array(
                'table_key' => $key,
                'ts' => $time
            ));
    }
}
