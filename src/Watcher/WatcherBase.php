<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Watcher;

use QCubed\Exception\Caller;
use QCubed\ObjectBase;
use QCubed\Query\Node\NodeBase;

/**
 * Class WatcherBase
 *
 * Watcher is a helper class that allows controls and forms to watch database tables
 * and automatically redraw when changes are detected. It works together with the codegen
 * model classes, the controls, and the QForm class to draw or refresh when needed.
 *
 * Static functions handle the database updating, while member variables store the current state
 * of a control's watched tables.
 *
 * This Base class is a template on which to build watchers that use specific caching mechanisms.
 * See Watcher to select the caching mechanism you would like to use.
 *
 * @package QCubed\Watcher
 */
abstract class WatcherBase extends ObjectBase
{
    public const QCUBED_WATCHER_CLASS = 'Watcher';

    /** @var  boolean */
    private static bool $blnWatcherChanged = false;

    protected array $strWatchedKeys = array();    // gets saved with form state

    /**
     * Initializes the required components or settings for the application or system.
     *
     * @return void
     */
    public static function initialize(): void
    {
    }

    /**
     * Returns a unique key corresponding to the given table in the given database.
     * Override this function to return a key value that will define a subset of the table to
     * watch. For example, if you have records associated with User Ids,
     * combine the user ID with the table name, and then
     * only records associated with that user ID will be watched.
     *
     * @param string $strDbName
     * @param string $strTableName
     * @return string
     */
    protected static function getKey(string $strDbName, string $strTableName): string
    {
        return $strDbName . '.' . $strTableName;
    }

    /**
     * Call from control to watch a node. Watches all tables associated with the node.
     *
     * @param NodeBase $objNode
     * @throws Caller
     */
    public function watch(NodeBase $objNode): void
    {
        $strClassName = $objNode->_ClassName;

        if (!$strClassName::$blnWatchChanges) {
            throw new Caller ($strClassName . '$blnWatchChanges is false. To be able to watch this table, you should set it to true in your ' . $strClassName . '.class.php file.');
        }

        if ($strClassName) {
            $objDatabase = $strClassName::getDatabase();
            $this->registerTable($objDatabase->Database, $objNode->_TableName);
        }
        $objParentNode = $objNode->_ParentNode;
        if ($objParentNode) {
            $this->watch($objParentNode);
        }
    }

    /**
     *
     * Internal function to watch a single table.
     *
     * @param string $strDbName
     * @param string $strTableName
     */
    protected function registerTable(string $strDbName, string $strTableName): void
    {
        $key = static::getKey($strDbName, $strTableName);
        if (empty($this->strWatchedKeys[$key])) {
            $this->strWatchedKeys[$key] = true;
        }
    }

    /**
     * Controls should call this function just after rendering. Updates strWatchedTables
     * to the current state of the database.
     *
     */
    abstract public function makeCurrent(): void;

    /**
     * QControlBase uses this from IsModified to detect if it should redraw.
     * Returns false if the database has been changed since the last draw.
     * @return bool
     */
    abstract public function isCurrent(): bool;

    /**
     * Models call into here to mark a particular table modified.
     *
     * @param string $strDbName
     * @param string $strTableName
     */
    public static function markTableModified(string $strDbName, string $strTableName): void
    {
        self::$blnWatcherChanged = true;
    }

    /**
     * Support function for the FormBase to determine if any of the watchers have changed since the last time
     * it checked. Since this is relying on a global variable, the variable is reset upon program entry, including
     * ajax entry. So really, we are just detecting if any operation we have currently done has changed a watcher so
     * that the form can broadcast that fact to other browser windows that might be looking.
     *
     * @return bool
     */
    static public function watchersChanged(): bool
    {
        $blnChanged = self::$blnWatcherChanged;
        self::$blnWatcherChanged = false;
        return $blnChanged;
    }
}