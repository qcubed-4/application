<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed;

use QCubed\Project\Application;

/**
 * class AuthBase
 *
 * This is currently an under-built class of routines that can help you authorize the current user. Feel free to
 * use or ignore.
 *
 * Authorization is often very unique to each application, and many times is not even needed. There have been a few
 * calls to develop an authorization and authentication module for QCubed, but its very difficult to come up with a
 * generic one. Likely it should be a plugin.
 *
 * PHP has some new built-in functions for aiding with passwords as of PHP 5.5. See password_hash() and password_verify(). There
 * are so many bad ways to authenticate passwords, and these routines handle those issues well.
 */

class AuthBase
{
    /**
     * Checks whether the web page is being accessed remotely (as opposed to accessing from localhost or a known safe
     * ip location.
     *
     * This used to be the default, but is now just here in case you need it and for backward compatibility.
     *
     * @return void
     */
    public static function checkRemoteAdmin()
    {
        if (!self::isRemoteAdminSession()) {
            return;
        }

        // If we're here -- then we're not allowed to access.  Present the Error/Issue.
        header($_SERVER['SERVER_PROTOCOL'] . ' 401 Access Denied');
        header('Status: 401 Access Denied', true);

        // throw new QRemoteAdminDeniedException(); ?? Really, throw an exception??
        Application::setProcessOutput(false);
        exit();
    }

    /**
     * Checks whether the current request was made by an ADMIN
     * This does not refer to your Database admin or an Admin user defined in your application but an IP address
     * (or IP address range) defined in configuration.inc.php.
     *
     * The function can be used to restrict access to sensitive pages to a list of IPs (or IP ranges), such as the LAN to which
     * the server hosting the QCubed application is connected.
     * @static
     * @return bool
     */
    public static function isRemoteAdminSession()
    {
        // Allow Remote?
        if (ALLOW_REMOTE_ADMIN === true) {
            return false;
        }

        // Are we localhost?
        if (substr($_SERVER['REMOTE_ADDR'], 0, 4) == '127.' || $_SERVER['REMOTE_ADDR'] == '::1') {
            return false;
        }

        // Are we the correct IP?
        if (is_string(ALLOW_REMOTE_ADMIN)) {
            foreach (explode(',', ALLOW_REMOTE_ADMIN) as $strIpAddress) {
                if (self::isIPv4InRange($_SERVER['REMOTE_ADDR'], $strIpAddress) ||
                    (array_key_exists('HTTP_X_FORWARDED_FOR',
                            $_SERVER) && (self::isIPv4InRange($_SERVER['HTTP_X_FORWARDED_FOR'], $strIpAddress)))
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @deprecated. This is a temporary stub for backwards compatibiity
     *
     * @param $ip
     * @param $range
     * @return bool
     *
     */
    public static function isIPInRange($ip, $range) {
        return self::isIpv4InRange($ip, $range);
    }
    /**
     * Checks whether the given IP falls into the given IP range. Note that when this was written, IPv6 did not exist.
     * It may not be appropriate to use this in your situation. Be safe!
     *
     * @static
     * @param string $ip the IP number to check
     * @param string $range the IP number range. The range could be in 'IP/mask' or 'IP - IP' format. mask could be a simple
     * integer or a dotted netmask.
     * @return bool
     */
    public static function isIpv4InRange($ip, $range)
    {
        $ip = trim($ip);
        if (strpos($range, '/') !== false) {
            // we are given a IP/mask
            list($net, $mask) = explode('/', $range);
            $net = ip2long(trim($net));
            $mask = trim($mask);
            //$ip_net = ip2long($net);
            if (strpos($mask, '.') !== false) {
                // mask has the dotted notation
                $ip_mask = ip2long($mask);
            } else {
                // mask is an integer
                $ip_mask = ~((1 << (32 - $mask)) - 1);
            }
            $ip = ip2long($ip);
            return ($net & $ip_mask) == ($ip & $ip_mask);
        }
        if (strpos($range, '-') !== false) {
            // we are given an IP - IP range
            list($first, $last) = explode('-', $range);
            $first = ip2long(trim($first));
            $last = ip2long(trim($last));
            $ip = ip2long($ip);
            return $first <= $ip && $ip <= $last;
        }

        // $range is a simple IP
        return $ip == trim($range);
    }
}