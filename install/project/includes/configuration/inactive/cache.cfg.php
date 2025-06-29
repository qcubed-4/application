<?php
/*
* Determines which class is used as a Cache Provider for long-term caching. It should be a subclass of CacheBase.
* Setting it to null will disable long-term caching. Current implementations are
*
* "CacherMemcache": this will use Memcache as the caching provider.
*   You must have the 'php5-memcache' package installed for this provider to work.
*
* "LocalMemoryCache": a local memory cache provider with a lifespan of the request
*   or session (if KeepInSession is configured).
*
* "NoCache": a provider which does no caching at all
*
*/
const CACHE_PROVIDER_CLASS = null;

/*
 * Options passed to the constructor of the Caching Provider class above.
 * For Memache, it's an array, where each item is an associative array of
 * server configuration options.
 * A description of the accepted criteria can be found in the documentation of each provider's constructor.
 * options.
 */
define ('CACHE_PROVIDER_OPTIONS' , serialize(
	array(
		array('host' => '127.0.0.1', 'port' => 11211, ),
		//array('host' => '10.0.2.2', 'port' => 11211, ), // adds a second server
	)
) );



