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
 * Class None
 *
 * WatcherNone is a watcher that turns off Watcher functionality. This is the default watcher. If you want to use
 * a watcher, you must specify a Watcher type in the Watcher class in you project/include/qcubed/Watcher directory
 *
 * @package QCubed\Watcher
 * @was QWatcherNone
 */
class None extends WatcherBase
{
    /**
     * Records the current state of the watched tables.
     */
    public function makeCurrent()
    {
    }

    /**
     *
     * @return bool
     */
    public function isCurrent()
    {
        return true;
    }

    /**
     * Model save() method should call this to indicate that a table has changed.
     *
     * @param string $strTableName
     * @throws \QCubed\Exception\Caller
     */
    static public function markTableModified($strDbName, $strTableName)
    {
    }
}