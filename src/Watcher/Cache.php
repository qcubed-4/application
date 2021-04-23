<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Watcher;

use Psr\SimpleCache\CacheInterface;

/**
 * Class Cache
 *
 * This is a watcher that can use PSR-16 SimpleCache compliant caches.
 *
 * Note that if you want to be able to detect changes by other users, you should use a
 * shared caching mechanism, like APC or Memcache. LocalMemoryCache will
 * not work for this.
 *
 * The PSR-16 spec says caches must support at a minimum 64 characters in the key. If your cache limits the number
 * of characters, you will need to override getKey to hash the resulting key, or somehow create a key less than the
 * max number of chars permitted by your caching system.
 *
 * You must subclass this and put a cache object in the $objCache variable.
 *
 * @was QWatcherCache
 */
abstract class Cache extends WatcherBase
{
    /** @var  CacheInterface */
    protected static $objCache = null;  // must be initialized by subclass at app startup time

    /**
     * Records the current state of the watched tables.
     */
    public function makeCurrent()
    {
        $curTime = microtime();

        $values = static::$objCache->getMultiple(array_keys($this->strWatchedKeys), false);
        foreach ($values as $key => $time2) {
            if ($time2 === false) {
                // if dropped from cache, or not yet cached
                static::$objCache->set($key, $curTime);
                $time2 = $curTime;
            }
            $this->strWatchedKeys[$key] = $time2;
        }
        /*
                foreach ($this->strWatchedKeys as $key => $val) {
                    $time2 = static::$objCache->get($key, false);

                    if ($time2 === false) {
                        // if dropped from cache, or not yet cached
                        static::$objCache->set($key, $curTime);
                        $time2 = $curTime;
                    }
                    $this->strWatchedKeys[$key] = $time2;
                }*/
    }

    /**
     *
     * @return bool
     */
    public function isCurrent()
    {
        foreach ($this->strWatchedKeys as $key => $time) {
            $time2 = static::$objCache->get($key);
            if (false === $time2 || $time2 != $time) {
                return false;
            }
        }

        return true;
    }

    /**
     * Model Save() method should call this to indicate that a table has changed.
     *
     * @param string $strDbName
     * @param string $strTableName
     */
    public static function markTableModified($strDbName, $strTableName)
    {
        parent::markTableModified($strDbName, $strTableName);
        $key = static::getKey($strDbName, $strTableName);
        $time = microtime();

        static::$objCache->set($key, $time);
    }
}
