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
 * a watcher, you must specify a Watcher type in the Watcher class in your project /include/qcubed/Watcher directory
 *
 * @package QCubed\Watcher
 */
class None extends WatcherBase
{
    /**
     * Records the current state of the watched tables.
     */
    public function makeCurrent(): void
    {
    }

    /**
     *
     * @return bool
     */
    public function isCurrent(): bool
    {
        return true;
    }

    /**
     * Model save() method should call this to indicate that a table has changed.
     *
     * @param string $strDbName
     * @param string $strTableName
     */
    static public function markTableModified(string $strDbName, string $strTableName): void
    {
    }
}