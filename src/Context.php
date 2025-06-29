<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed;

/**
 * Class Context
 *
 * The Context singleton reports information about the current environment the script is running in.
 *
 * Scripts could be running in command line mode (CLI) or in response to web server requests. Web server requests
 * can come in the form of Ajax or Standard requests. This class encapsulates that information and makes it available
 * to the application as needed. All processed information is cached so multiple requests for the same thing can be fast.
 *
 * @package QCubed
 */
class Context
{
    public const int INTERNET_EXPLORER = 1;
    public const int FIREFOX = 0x10;
    public const int SAFARI = 0x200;
    public const int OPERA = 0x2000;
    public const int KONQUEROR = 0x20000;
    public const int CHROME = 0x100000;

    public const int WINDOWS = 0x800000;
    public const int LINUX = 0x1000000;
    public const int MACINTOSH = 0x2000000;

    public const int MOBILE = 0x4000000;    // some kind of mobile browser

    /** We don't know this gentleman...err...gentle browser */
    public const int UNSUPPORTED = 0x8000000;

    public const string REQUEST_MODE_QCUBED_SERVER = 'Server'; // calling back in to currently showing page using a standard form post
    public const string REQUEST_MODE_HTTP = 'Http';   // new page request
    public const string REQUEST_MODE_QCUBED_AJAX = 'Ajax';  // calling back in to use currently showing page using an ajax request
    public const string REQUEST_MODE_AJAX = 'AjaxNonQ';   // calling an entry point from ajax, but not through qcubed.js. REST API perhaps?
    public const string REQUEST_MODE_CLI = 'Cli'; // command line call

    /** @var  bool Are we running in command line mode? */
    protected bool $blnCliMode;
    /** @var  string */
    protected string $strServerAddress = '';
    /** @var  string */
    protected string $strScriptFileName = '';
    /** @var  string */
    protected string $strScriptName = '';
    /** @var string */
    protected string $strPathInfo = '';
    /** @var  string */
    protected string $strQueryString = '';
    /** @var  string */
    protected string $strRequestUri = '';
    /** @var  integer|null */
    protected ?int $intBrowserType = null;
    /** @var  float|null */
    protected ?float $fltBrowserVersion = null;
    /** @var  string */
    protected string $strRequestMode = '';


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
    public function cliMode(): bool
    {
        return $this->blnCliMode;
    }

    /**
     * The address of the server making a request.
     *
     * @return string
     */
    public function serverAddress(): string
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
     * @return string
     */
    public function scriptFileName(): string
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
    public function scriptName(): string
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
    public function pathInfo(): string
    {
        if (!$this->strPathInfo) {
            // PATH_INFO not available - we use REQUEST_URI
            if (isset($_SERVER['REQUEST_URI'])) {
                // Clean up the REQUEST_URI by removing the script name and query string
                $requestUri = $_SERVER['REQUEST_URI'];
                $scriptName = $_SERVER['SCRIPT_NAME']; // For example '/index.php'

                // Remove script name and query string to get only the PATH part
                $path = strtok($requestUri, '?'); // Remove query string
                $pathInfo = str_replace($scriptName, '', $path); // Remove script name

                // If PATH_INFO is empty or starts with false, follow the clean path part
                $this->strPathInfo = urlencode(trim($pathInfo));
                $this->strPathInfo = str_ireplace('%2f', '/', $this->strPathInfo);
            } else {
                $this->strPathInfo = ''; // If REQUEST_URI is not available, leave blank.
            }
        }
        return $this->strPathInfo;
    }

    /**
     * The query part of the http request.
     *
     * @return string
     */
    public function queryString(): string
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
    public function requestUri(): string
    {
        if (!$this->strRequestUri) {
            // We use REQUEST_URI whenever available
            if (isset($_SERVER['REQUEST_URI'])) {
                $this->strRequestUri = $_SERVER['REQUEST_URI'];
            } else {
                // If REQUEST_URI is not available, combine alternative
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
    public function pathItem(int $intIndex): ?string
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

        return $strPathInfoArray[$intIndex] ?? null;
    }

    /**
     * Gets the value of the QueryString item $strItem.  Will return NULL if it doesn't exist.
     *
     * @param string $strItem the parameter name
     *
     * @return string|null value of the parameter
     * @was QApplication::QueryString
     */
    public function queryStringItem(string $strItem): ?string
    {
        if (array_key_exists($strItem, $_GET)) {
            return $_GET[$strItem];
        } else {
            return null;
        }
    }

    /**
     * Returns a bit mask representing the current browser. See consts at the top of this file.
     *
     * @return int
     */
    public function browserType(): int
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
    public function browserVersion(): float
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
    protected function browserInit(): void
    {
        // Setup Browser Type
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $strUserAgent = trim(strtolower($_SERVER['HTTP_USER_AGENT']));

            $this->intBrowserType = 0;

            // INTERNET EXPLORER (versions 6 through 10)
            if (str_contains($strUserAgent, 'msie')) {
                $this->intBrowserType = $this->intBrowserType | static::INTERNET_EXPLORER;

                // just a major version number. Will not see IE 10.6.
                $matches = array();
                preg_match('#msie\s(.\d)#', $strUserAgent, $matches);
                if ($matches) {
                    $this->fltBrowserVersion = (int)$matches[1];
                }
            } else {
                if (str_contains($strUserAgent, 'trident')) {
                    // IE 11 significantly changes the user agent, and no longer includes 'MSIE'
                    $this->intBrowserType = $this->intBrowserType | static::INTERNET_EXPLORER;

                    $matches = array();
                    preg_match('/rv:(.+)\)/', $strUserAgent, $matches);
                    if ($matches) {
                        $this->fltBrowserVersion = (float)$matches[1];
                    }
                    // FIREFOX
                } else {
                    if ((str_contains($strUserAgent, 'firefox')) || (str_contains($strUserAgent, 'iceweasel'))
                    ) {
                        $this->intBrowserType = $this->intBrowserType | static::FIREFOX;
                        $strUserAgent = str_replace('iceweasel/', 'firefox/', $strUserAgent);

                        $matches = array();
                        preg_match('#firefox/(.+)#', $strUserAgent, $matches);
                        if ($matches) {
                            $this->fltBrowserVersion = (float)$matches[1];
                        }
                    } // CHROME must come before safari because it also includes a safari string
                    elseif (str_contains($strUserAgent, 'chrome')) {
                        $this->intBrowserType = $this->intBrowserType | static::CHROME;

                        // find major version number only
                        $matches = array();
                        preg_match('#chrome/(\d+)#', $strUserAgent, $matches);
                        if ($matches) {
                            $this->fltBrowserVersion = (int)$matches[1];
                        }
                    } // SAFARI
                    elseif (str_contains($strUserAgent, 'safari')) {
                        $this->intBrowserType = $this->intBrowserType | static::SAFARI;

                        $matches = array();
                        preg_match('#version/(.+)\s#', $strUserAgent, $matches);
                        if ($matches) {
                            $this->fltBrowserVersion = (float)$matches[1];
                        }
                    } // KONQUEROR
                    elseif (str_contains($strUserAgent, 'konqueror')) {
                        $this->intBrowserType = $this->intBrowserType | static::KONQUEROR;

                        // only looking at major version number on this one
                        $matches = array();
                        preg_match('#konqueror/(\d+)#', $strUserAgent, $matches);
                        if ($matches) {
                            $this->fltBrowserVersion = (int)$matches[1];
                        }
                    } // OPERA
                    elseif (str_contains($strUserAgent, 'opera')) {
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
            if (str_contains($strUserAgent, 'windows')) {
                $this->intBrowserType = $this->intBrowserType | static::WINDOWS;
            } elseif (str_contains($strUserAgent, 'linux')) {
                $this->intBrowserType = $this->intBrowserType | static::LINUX;
            } elseif (str_contains($strUserAgent, 'macintosh')) {
                $this->intBrowserType = $this->intBrowserType | static::MACINTOSH;
            }

            // Mobile version of one of the above browsers, or some other unknown browser
            if (str_contains($strUserAgent, 'mobi')) // opera is just 'mobi', everyone else uses 'mobile'
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
    public function isBrowser(int $intBrowserType): bool
    {
        return ($intBrowserType & $this->browserType()) != 0;
    }

    /**
     * Returns either Standard or Ajax for the request mode.
     *
     * @return string
     */
    public function requestMode(): string
    {
        if (!$this->strRequestMode) {
            if (isset($_POST[Control\FormBase::POST_CALL_TYPE])) { // call is being made by qcubed.js
                if ($_POST[Control\FormBase::POST_CALL_TYPE] == 'Ajax') {
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
