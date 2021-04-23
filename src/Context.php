<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed;

use QCubed\Project\Control\FormBase as QForm;

/**
 * Class Context
 *
 * The Context singleton reports information about the current environment the script is running in.
 *
 * Scripts could be running in command line mode (CLI), or in response to web server requests. Web server requests
 * can come in the form of Ajax or Standard requests. This class encapsulates that information and makes it available
 * to the application as needed. All processed information is cached so multiple requests for the same thing can be fast.
 *
 * @package QCubed
 */
class Context
{
    const INTERNET_EXPLORER = 1;
    const FIREFOX = 0x10;
    const SAFARI = 0x200;
    const OPERA = 0x2000;
    const KONQUEROR = 0x20000;
    const CHROME = 0x100000;

    const WINDOWS = 0x800000;
    const LINUX = 0x1000000;
    const MACINTOSH = 0x2000000;

    const MOBILE = 0x4000000;    // some kind of mobile browser

    /** We don't know this gentleman...err...gentlebrowser */
    const UNSUPPORTED = 0x8000000;

    const REQUEST_MODE_QCUBED_SERVER = 'Server'; // calling back in to currently showing page using a standard form post
    const REQUEST_MODE_HTTP = 'Http';   // new page request
    const REQUEST_MODE_QCUBED_AJAX = 'Ajax';  // calling back in to an currently showing page using an ajax request
    const REQUEST_MODE_AJAX = 'AjaxNonQ';   // calling an entry point from ajax, but not through qcubed.js. REST API perhaps?
    const REQUEST_MODE_CLI = 'Cli'; // command line call

    /** @var  bool Are we running in command line mode? */
    protected $blnCliMode;
    /** @var  string */
    protected $strServerAddress;
    /** @var  string */
    protected $strScriptFileName;
    /** @var  string */
    protected $strScriptName;
    /** @var string */
    protected $strPathInfo;
    /** @var  string */
    protected $strQueryString;
    /** @var  string */
    protected $strRequestUri;
    /** @var  integer */
    protected $intBrowserType;
    /** @var  float */
    protected $fltBrowserVersion;
    /** @var  string */
    protected $strRequestMode;


    /**
     * Context constructor.
     */
    public function __construct()
    {
        $this->blnCliMode = (PHP_SAPI == 'cli');

    }

    /**
     * @return bool Whether we are in command line mode.
     */
    public function cliMode()
    {
        return $this->blnCliMode;
    }

    /**
     * The address of the server making a request.
     *
     * @return string
     */
    public function serverAddress()
    {
        if (!$this->strServerAddress) {
            if (isset($_SERVER['LOCAL_ADDR'])) {
                $this->strServerAddress = $_SERVER['LOCAL_ADDR'];
            } else {
                if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $this->strServerAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } elseif (isset($_SERVER['SERVER_ADDR'])) {
                    $this->strServerAddress = $_SERVER['SERVER_ADDR'];
                }
            }
        }
        return $this->strServerAddress;
    }

    /**
     * The name of the script file currently running. If a file is included, this would be the topmost file.
     * @return bool|string
     */
    public function scriptFileName()
    {
        if (!$this->strScriptFileName) {
            // Setup ScriptFilename and ScriptName
            $this->strScriptFileName = $_SERVER['SCRIPT_FILENAME'];
            // Work around a special case so this is always a full path
            if (
                $this->blnCliMode &&
                $this->strScriptFileName[0] != '/' // relative path
            ) {
                $this->strScriptFileName = realpath(getcwd() . '/' . $this->strScriptFileName);
            }
        }
        return $this->strScriptFileName;
    }

    /**
     * The current file executing. This would be the same as the __FILE__ global.
     *
     * @return string
     */
    public function scriptName()
    {
        if (!$this->strScriptName) {
            $this->strScriptName = $_SERVER['SCRIPT_NAME'];
        }
        return $this->strScriptName;
    }

    /**
     * The path of the http request.
     *
     * @return string
     */
    public function pathInfo()
    {
        if ($this->strPathInfo === null) {
            if (isset($_SERVER['PATH_INFO'])) {
                $this->strPathInfo = urlencode(trim($_SERVER['PATH_INFO']));
                $this->strPathInfo = str_ireplace('%2f', '/', $this->strPathInfo);
            } else {
                $this->strPathInfo = '';    // no path info given
            }
        }
        return $this->strPathInfo;
    }

    /**
     * The query part of the http request.
     *
     * @return string
     */
    public function queryString()
    {
        if (!$this->strQueryString) {
            if (isset($_SERVER['QUERY_STRING'])) {
                $this->strQueryString = $_SERVER['QUERY_STRING'];
            }
        }
        return $this->strQueryString;
    }

    /**
     * The entire requested Uri
     *
     * @return string
     */
    public function requestUri()
    {
        if (!$this->strRequestUri) {
            // Setup RequestUri
            if (defined('QCUBED_URL_REWRITE')) {
                switch (strtolower(QCUBED_URL_REWRITE)) {
                    case 'apache':
                        $this->strRequestUri = $_SERVER['REQUEST_URI'];
                        break;

                    case 'none':
                        $this->strRequestUri = sprintf('%s%s%s',
                            $this->scriptName(), $this->pathInfo(),
                            ($this->queryString()) ? sprintf('?%s', $this->queryString()) : null);
                        break;

                    default:
                        throw new Exception('Invalid URL Rewrite type: ' . QCUBED_URL_REWRITE);
                }
            } else {
                $this->strRequestUri = sprintf('%s%s%s',
                    $this->scriptName(), $this->pathInfo(),
                    ($this->queryString()) ? sprintf('?%s', $this->queryString()) : null);
            }
        }
        return $this->strRequestUri;
    }

    /**
     * Gets the value of the PathInfo item at index $intIndex.  Will return null if it doesn't exist.
     *
     * The way pathItem index is determined is, for example, given a URL '/folder/page.php/id/15/blue',
     * 0 - will return 'id'
     * 1 - will return '15'
     * 2 - will return 'blue'
     *
     * @param int $intIndex index
     * @return string|null
     * @was QApplication::PathInfo
     */
    public function pathItem($intIndex)
    {
        // TODO: Cache PathInfo
        $strPathInfo = urldecode($this->pathInfo());

        if (!$strPathInfo) {
            return null;
        }

        // Remove Starting '/'
        if ($strPathInfo[0] == '/') {
            $strPathInfo = substr($strPathInfo, 1);
        }

        $strPathInfoArray = explode('/', $strPathInfo);

        if (isset($strPathInfoArray[$intIndex])) {
            return $strPathInfoArray[$intIndex];
        } else {
            return null;
        }
    }

    /**
     * Gets the value of the QueryString item $strItem.  Will return NULL if it doesn't exist.
     *
     * @param string $strItem the parameter name
     *
     * @return string value of the parameter
     * @was QApplication::QueryString
     */
    public function queryStringItem($strItem)
    {
        if (array_key_exists($strItem, $_GET)) {
            return $_GET[$strItem];
        } else {
            return null;
        }
    }

    /**
     * Returns a bit mask representing the current browser. See consts at top of this file.
     *
     * @return int
     */
    public function browserType()
    {
        if (!$this->intBrowserType) {
            $this->browserInit();
        }
        return $this->intBrowserType;
    }

    /**
     * Return the browser version as a float.
     *
     * @return float
     */
    public function browserVersion()
    {
        if (!$this->fltBrowserVersion) {
            $this->browserInit();
        }
        return $this->fltBrowserVersion;
    }

    /**
     * Internal function to get browser info.
     *
     * @internal
     */
    protected function browserInit()
    {
        // Setup Browser Type
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $strUserAgent = trim(strtolower($_SERVER['HTTP_USER_AGENT']));

            $this->intBrowserType = 0;

            // INTERNET EXPLORER (versions 6 through 10)
            if (strpos($strUserAgent, 'msie') !== false) {
                $this->intBrowserType = $this->intBrowserType | static::INTERNET_EXPLORER;

                // just major version number. Will not see IE 10.6.
                $matches = array();
                preg_match('#msie\s(.\d)#', $strUserAgent, $matches);
                if ($matches) {
                    $this->fltBrowserVersion = (int)$matches[1];
                }
            } else {
                if (strpos($strUserAgent, 'trident') !== false) {
                    // IE 11 significantly changes the user agent, and no longer includes 'MSIE'
                    $this->intBrowserType = $this->intBrowserType | static::INTERNET_EXPLORER;

                    $matches = array();
                    preg_match('/rv:(.+)\)/', $strUserAgent, $matches);
                    if ($matches) {
                        $this->fltBrowserVersion = (float)$matches[1];
                    }
                    // FIREFOX
                } else {
                    if ((strpos($strUserAgent, 'firefox') !== false) || (strpos($strUserAgent,
                                'iceweasel') !== false)
                    ) {
                        $this->intBrowserType = $this->intBrowserType | static::FIREFOX;
                        $strUserAgent = str_replace('iceweasel/', 'firefox/', $strUserAgent);

                        $matches = array();
                        preg_match('#firefox/(.+)#', $strUserAgent, $matches);
                        if ($matches) {
                            $this->fltBrowserVersion = (float)$matches[1];
                        }
                    } // CHROME, must come before safari because it also includes a safari string
                    elseif (strpos($strUserAgent, 'chrome') !== false) {
                        $this->intBrowserType = $this->intBrowserType | static::CHROME;

                        // find major version number only
                        $matches = array();
                        preg_match('#chrome/(\d+)#', $strUserAgent, $matches);
                        if ($matches) {
                            $this->fltBrowserVersion = (int)$matches[1];
                        }
                    } // SAFARI
                    elseif (strpos($strUserAgent, 'safari') !== false) {
                        $this->intBrowserType = $this->intBrowserType | static::SAFARI;

                        $matches = array();
                        preg_match('#version/(.+)\s#', $strUserAgent, $matches);
                        if ($matches) {
                            $this->fltBrowserVersion = (float)$matches[1];
                        }
                    } // KONQUEROR
                    elseif (strpos($strUserAgent, 'konqueror') !== false) {
                        $this->intBrowserType = $this->intBrowserType | static::KONQUEROR;

                        // only looking at major version number on this one
                        $matches = array();
                        preg_match('#konqueror/(\d+)#', $strUserAgent, $matches);
                        if ($matches) {
                            $this->fltBrowserVersion = (int)$matches[1];
                        }
                    } // OPERA
                    elseif (strpos($strUserAgent, 'opera') !== false) {
                        $this->intBrowserType = $this->intBrowserType | static::OPERA;

                        // two different patterns;
                        $matches = array();
                        preg_match('#version/(\d+)#', $strUserAgent, $matches);
                        if ($matches) {
                            $this->fltBrowserVersion = (int)$matches[1];
                        } else {
                            preg_match('#opera\s(.+)#', $strUserAgent, $matches);
                            if ($matches) {
                                $this->fltBrowserVersion = (float)$matches[1];
                            }
                        }
                    }
                }
            }

            // Unknown
            if ($this->intBrowserType == 0) {
                $this->intBrowserType = $this->intBrowserType | static::UNSUPPORTED;
            }

            // OS (supporting Windows, Linux and Mac)
            if (strpos($strUserAgent, 'windows') !== false) {
                $this->intBrowserType = $this->intBrowserType | static::WINDOWS;
            } elseif (strpos($strUserAgent, 'linux') !== false) {
                $this->intBrowserType = $this->intBrowserType | static::LINUX;
            } elseif (strpos($strUserAgent, 'macintosh') !== false) {
                $this->intBrowserType = $this->intBrowserType | static::MACINTOSH;
            }

            // Mobile version of one of the above browsers, or some other unknown browser
            if (strpos($strUserAgent, 'mobi') !== false) // opera is just 'mobi', everyone else uses 'mobile'
            {
                $this->intBrowserType = $this->intBrowserType | static::MOBILE;
            }
        }
    }

    /**
     * Checks for the type of browser in use by the client.
     * @param int $intBrowserType
     * @return bool
     * @was QApplication::IsBrowser
     */
    public function isBrowser($intBrowserType)
    {
        return ($intBrowserType & $this->browserType()) != 0;
    }

    /**
     * Returns either Standard or Ajax for the request mode.
     *
     * @return string
     */
    public function requestMode()
    {
        if (!$this->strRequestMode) {
            if (isset($_POST[QForm::POST_CALL_TYPE])) { // call is being made by qcubed.js
                if ($_POST[QForm::POST_CALL_TYPE] == 'Ajax') {
                    $this->strRequestMode = self::REQUEST_MODE_QCUBED_AJAX;
                } else {
                    $this->strRequestMode = self::REQUEST_MODE_QCUBED_SERVER;
                }
            } elseif (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $this->strRequestMode = self::REQUEST_MODE_AJAX;
            } elseif (php_sapi_name() === 'cli') {
                $this->strRequestMode = self::REQUEST_MODE_CLI;
            } else {
                $this->strRequestMode = self::REQUEST_MODE_HTTP;
            }
        }
        return $this->strRequestMode;
    }
}