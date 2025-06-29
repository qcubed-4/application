<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Watcher;

use QCubed\Database\Mysqli5\MysqliException;
use QCubed\Database\Service;
use QCubed\Exception\Caller;

/**
 * Class Watcher\Database
 *
 * This is a helper class that allows controls to watch a database table
 * and automatically update the UI when changes are detected. It works together with the codegen
 * model classes, the controls, and the Form class to draw when needed.
 *
 * This relies on the existence of an SQL database table on the system. Define the following
 * in your Watcher subclass file to tell it which tables to use:
 * static::$intDbIndex - the database index to look up the table
 * static::$strTableName - the name of the table.
 *
 * To create the database, use the following SQL:
 * CREATE TABLE IF NOT EXISTS qc_watchers (* table_key varchar(200) NOT NULL,
 * ts varchar(40) NOT NULL,
 * PRIMARY KEY (table_key)
 * );
 *
 *
 * @package QCubed\Watcher
 */

abstract class Database extends WatcherBase
{
    /*** The following two variables must be initialized by a subclass **/
    /** @var  integer The database indexes to use */
    protected static int $intDbIndex;
    /** 
     * @var  string
     * The table name which will keep info about changed tables. It must have the following columns:
     * 1. table_key: varchar(largest key size)
     * 2. time: varchar(30)     */
    protected static string $strTableName;

    /**
     * @var string[] Caches results of database lookups. Will not be saved with the formstate.
     */
    private static ?array $strKeyCaches = null;

    /**
     * Override
     */
    public function makeCurrent(): void
    {
        $objDatabase = Service::getDatabase(static::$intDbIndex);
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
     * @throws Caller
     */
    public function isCurrent(): bool
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
        } // the cache had everything we were looking for

        // cache did not have what we were looking for, so check databases
        $objDatabase = Service::getDatabase(static::$intDbIndex);
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
     * @throws MysqliException
     */
    static public function markTableModified(string $strDbName, string $strTableName): void
    {
        $key = static::getKey($strDbName, $strTableName);
        $objDatabase = Service::getDatabase(static::$intDbIndex);
        $time = microtime();

        $objDatabase->insertOrUpdate(static::$strTableName,
            array(
                'table_key' => $key,
                'ts' => $time
            ));
    }
}
