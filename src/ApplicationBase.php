<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed;

use QCubed\Exception\Caller;
use QCubed\Project\Application;
use QCubed\Js;
use QCubed\Database\Service as DatabaseService;
use QCubed as Q;

/**
 * Class ApplicationBase
 *
 * This is the base class for the singleton application object. It contains utility code and code to aid the communication
 * with the client. The difference between this and the QForm class is that anything in the application class must be
 * recreated on every entry into to the server, whereas the QForm class uses its built-in serialization mechanism to
 * recreate itself on each entry. This means that any information that should persist for the user as they move
 * through the application should go in the Form, and anything that is just used at the moment to build a response
 * should go here.
 * @package QCubed
 * //was QApplicationBase (do not put annotiation here, all transformations are manual)
 */
abstract class ApplicationBase extends ObjectBase
{
    // These constants help us to organize and build a list of responses to the client.
    const PRIORITY_STANDARD = '*jsMed*';
    const PRIORITY_HIGH = '*jsHigh*';
    const PRIORITY_LOW = '*jsLow*';
    /** Execute ONLY this command and exclude all others */
    const PRIORITY_EXCLUSIVE = '*jsExclusive*';
    /** Execute this command after all ajax commands have been completely flushed */
    const PRIORITY_LAST = '*jsFinal*';

    /**
     * @var bool Set to true to turn on short-term caching. This is an in-memory cache that caches database
     * objects only for as long as a single http request lasts. Depending on your application, this may speed
     * up your database accesses. It DOES increase the amount of memory used in a request.
     * */
    public static $blnLocalCache = false;

    /** @var string */
    protected static $strCacheControl = 'private';
    /** @var string */
    protected static $strContentType = "text/html";


    private static $instance = null;


    /** @var Context */
    protected $objContext;
    /** @var  JsResponse */
    protected $objJsResponse;
    /** @var  bool Current state of output, whether it should be minimized or not. */
    protected $blnMinimize = false;
    /** @var  Purifier The purifier service. */
    protected $objPurifier;
    /** @var bool */
    protected $blnProcessOutput = true;
    /** @var string  */
    protected $strEncodingType = QCUBED_ENCODING;


    /**
     * Return and possibly create the application instance, which is a subclass of this class. It will be treated as
     * a singleton.
     *
     * @return Application
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new Application();
        }
        return self::$instance;
    }


    public function __construct()
    {
        if (defined('QCUBED_MINIMIZE') && QCUBED_MINIMIZE) {
            $this->blnMinimize = true;
        }
    }

    /**
     * Set whether output should be minimized. Returns the prior state.
     *
     * @param $blnMinimize
     * @return bool
     */
    public function setMinimize($blnMinimize)
    {
        $blnRet = $this->blnMinimize;
        $this->blnMinimize = $blnMinimize;
        return $blnRet;
    }

    /**
     * Return true if all output should be minimized. Useful for production environments when you are trying to reduce
     * the amount of raw text you are sending to a browser. Minimize generally removes space and space-like characters.
     *
     * @return bool
     */
    public function minimize()
    {
        return $this->blnMinimize;
    }

    /**
     * Returns the singleton instance of the context, which has information about the environment the current script
     * is running in--things like: is it running in command line mode, or what are the server parameters if running
     * in response to an HTTP request, etc.
     *
     * @return Context
     */
    public function context()
    {
        if (!$this->objContext) {
            $this->objContext = new Context();
        }
        return $this->objContext;
    }

    /**
     * Returns a singleton jsResponse object. This is for internal use of the application class only. It manages
     * the javascript and json responses to requests.
     *
     * @return JsResponse
     */
    public function jsResponse()
    {
        if (!$this->objJsResponse) {
            $this->objJsResponse = new JsResponse();
        }
        return $this->objJsResponse;
    }

    /**
     * @return string   The application encoding type.
     */
    public static function encodingType()
    {
        return Application::instance()->strEncodingType;
    }

    /**
     * Allows temporary setting of the encoding type if loading a special page.
     *
     * @return string
     */
    public static function setEncodingType($strEncodingType)
    {
        $strOldValue = Application::instance()->strEncodingType;
        Application::instance()->strEncodingType = $strEncodingType;
        return $strOldValue;
    }

    /**
     * Returns true if this is a QCubed Ajax call. Note that if you are calling an entry point with ajax, but not through
     * qcubed.js, then it will return false. If you want to know whether a particular entry point is being called with
     * ajax that might be serving up a REST api for example, check requestMode() for Context::REQUEST_MODE_AJAX
     * @return bool
     */
    public static function isAjax()
    {
        return Application::instance()->context()->requestMode() == Context::REQUEST_MODE_QCUBED_AJAX;
    }

    /**
     * This is called by the PHP5 Autoloader.  This static method can be overridden.
     *
     *
     * @param $strClassName
     * @return boolean whether or not a class was found / included
     */
    public static function autoload($strClassName)
    {
        if (file_exists($strFilePath = sprintf('%s/plugins/%s.php', QCUBED_PROJECT_INCLUDES_DIR, $strClassName))) {
            require_once($strFilePath);
            return true;
        }
        return false;
    }

    /**
     * Temprorarily overrides the default error handling mechanism.  Remember to call
     * RestoreErrorHandler to restore the error handler back to the default.
     *
     * @param string $strName the name of the new error handler function, or NULL if none
     * @param integer $intLevel if a error handler function is defined, then the new error reporting level (if any)
     *
     * @deprecated Create a \QCubed\Error\Handler instead
     * @throws Caller
     */
    public static function setErrorHandler($strName, $intLevel = null)
    {
        throw new Caller("SetErrorHandler is deprecated. Create an Error\\Handler instead.");
    }

    /**
     * Restores the temporarily overridden default error handling mechanism back to the default.
     */
    public static function restoreErrorHandler()
    {
        throw new Caller("SetErrorHandler is deprecated. Create an Error\\Handler instead.");
    }

    /**
     * Use the purifier to purify text, initializing it if it does not exist.
     *
     * @param $strText
     * @param null|\HTMLPurifier_Config $objCustomConfig
     * @return string
     */
    public static function purify($strText, $objCustomConfig = null)
    {
        if (!Application::instance()->objPurifier) {
            Application::instance()->initPurifier();
        }
        return Application::instance()->objPurifier->purify($strText, $objCustomConfig);
    }

    abstract protected function initPurifier();

    /**
     * Whether or not we are currently trying to Process the Output of the page.
     * Used by the OutputPage PHP output_buffering handler.  As of PHP 5.2,
     * this gets called whenever ob_get_contents() is called.  Because some
     * classes like QFormBase utilizes ob_get_contents() to perform template
     * evaluation without wanting to actually perform OutputPage, this flag
     * can be set/modified by QFormBase::EvaluateTemplate accordingly to
     * prevent OutputPage from executing.
     *
     * Also set this to false if you are outputting custom headers, especially
     * if you send your own "Content-Type" header.
     *
     * @param $blnProcess
     * @return bool
     */
    public static function setProcessOutput($blnProcess)
    {
        $blnOldValue = Application::instance()->blnProcessOutput;
        Application::instance()->blnProcessOutput = $blnProcess;
        return $blnOldValue;
    }

    /**
     * Definition of CacheControl for the HTTP header.  In general, it is
     * recommended to keep this as "private".  But this can/should be overriden
     * for file/scripts that have special caching requirements.
     *
     * Returns old value.
     *
     * @param string $strControl The new value
     * @return string The old value
     */
    public static function setCacheControl($strControl)
    {
        $strOldValue = static::$strCacheControl;
        static::$strCacheControl = $strControl;
        return $strOldValue;
    }


    /**
     * The content type to output.
     *
     * @param string $strContentType
     * @return string The old value
     */
    public static function setContentType($strContentType)
    {
        $strOldValue = static::$strContentType;
        static::$strContentType = $strContentType;
        return $strOldValue;
    }

    /**
     * This will redirect the user to a new web location.  This can be a relative or absolute web path, or it
     * can be an entire URL.
     *
     * TODO: break this into two routines, since the resulting UI behavior is really different. Redirect and LoadPage??
     *
     * @param string $strLocation target patch
     * @param bool $blnAbortCurrentScript Whether to abort the current script, or finish it out so data gets saved.
     * @return void
     */
    public static function redirect($strLocation, $blnAbortCurrentScript = true)
    {
        if (!$blnAbortCurrentScript) {
            // Use the javascript command mechanism
            Application::instance()->jsResponse()->setLocation($strLocation);
        } else {
            /** @var \QCubed\Project\Control\FormBase */
            global $_FORM;

            if ($_FORM) {
                $_FORM->saveControlState();
            }

            // Clear the output buffer (if any)
            ob_clean();

            if (Application::isAjax()) {
                Application::sendAjaxResponse(array(JsResponse::LOCATION => $strLocation));
            } else {
                // Was "DOCUMENT_ROOT" set?
                if (array_key_exists('DOCUMENT_ROOT', $_SERVER) && ($_SERVER['DOCUMENT_ROOT'])) {
                    // If so, we're likely using PHP as a Plugin/Module
                    // Use 'header' to redirect
                    header(sprintf('Location: %s', $strLocation));
                    static::setProcessOutput(false);
                } else {
                    // We're likely using this as a CGI
                    // Use JavaScript to redirect
                    printf('<script type="text/javascript">document.location = "%s";</script>', $strLocation);
                }
            }

            // End the Response Script
            session_write_close();
            exit();
        }
    }


    /**
     * This will close the window.
     *
     * @param bool $blnAbortCurrentScript Whether to abort the current script, or finish it out so data gets saved.
     * @return void
     */
    public static function closeWindow($blnAbortCurrentScript = false)
    {
        if (!$blnAbortCurrentScript) {
            // Use the javascript command mechanism
            Application::instance()->jsResponse()->closeWindow();
        } else {
            // Clear the output buffer (if any)
            ob_clean();

            if (Application::isAjax()) {
                // AJAX-based Response
                Application::sendAjaxResponse(array(JsResponse::CLOSE => 1));
            } else {
                // Use JavaScript to close
                _p('<script type="text/javascript">window.close();</script>', false);
            }

            // End the Response Script
            exit();
        }
    }

    /**
     * Set a cookie. Allows setting of cookies in responses to ajax requests.
     *
     * @param string $strName
     * @param string $strValue
     * @param QDateTime $dttTimeout
     * @param string $strPath
     * @param null|string $strDomain
     * @param bool $blnSecure
     */
    public static function setCookie(
        $strName,
        $strValue,
        QDateTime $dttTimeout,
        $strPath = '/',
        $strDomain = null,
        $blnSecure = false
    ) {
        if (self::isAjax()) {
            self::executeJsFunction('qcubed.setCookie',
                $strName,
                $strValue,
                $dttTimeout,
                $strPath,
                $strDomain,
                $blnSecure);
        } else {
            setcookie($strName, $strValue, $dttTimeout->Timestamp, $strPath, $strDomain, $blnSecure);
        }
    }

    /**
     * Delete's the given cookie IF its set. In other words, you cannot set a cookie and then delete a cookie right away before the
     * cookie gets sent to the browser.
     *
     * @param $strName
     */
    public static function deleteCookie($strName)
    {
        if (isset($_COOKIE[$strName])) { // don't post a cookie if its not set
            $dttTimeout = QDateTime::now();
            $dttTimeout->addYears(-5);

            self::setCookie($strName, "", $dttTimeout);
        }
    }


    /**
     * Causes the browser to display a JavaScript alert() box with supplied message
     * @param string $strMessage Message to be displayed
     */
    public static function displayAlert($strMessage)
    {
        Application::instance()->jsResponse()->displayAlert($strMessage);
    }

    /**
     * This class can be used to call a Javascript function in the client browser from the server side.
     * Can be used inside event handlers to do something after verification  on server side.
     *
     * TODO: Since this is implemented with an "eval" on the client side in ajax, we should phase this out in favor
     * of specific commands sent to the client.
     *
     * @static
     *  Will be eventually removed. If you need to do something in javascript, add it to AjaxResponse.
     * @param string $strJavaScript the javascript to execute
     * @param string $strPriority
     * @throws Caller
     */
    public static function executeJavaScript($strJavaScript, $strPriority = self::PRIORITY_STANDARD)
    {
        Application::instance()->jsResponse()->executeJavaScript($strJavaScript, $strPriority);
    }

    /**
     * Execute a function on a particular control. Many javascript widgets are structured this way, and this gives us
     * a general purpose way of sending commands to widgets without an 'eval' on the client side.
     *
     * Commands will be executed in the order received, along with ExecuteJavaScript commands and ExecuteObjectCommands.
     * If you want to force a command to execute first, give it high priority, or last, give it low priority.
     *
     * @param string $strControlId Id of control to direct the command to.
     * @param string $strFunctionName Function name to call. For jQueryUI, this would be the widget name
     * @param string $strFunctionName,... Unlimited OPTIONAL parameters to use as a parameter list to the function. List can
     *                                        end with a PRIORITY_* to prioritize the command.
     */
    public static function executeControlCommand($strControlId, $strFunctionName /*, ..., PRIORITY_* */)
    {
        $args = func_get_args();
        call_user_func_array([Application::instance()->jsResponse(), 'executeControlCommand'], $args);
    }

    /**
     * Call a function on a jQuery selector. The selector can be a single string, or an array where the first
     * item is a selector specifying the items within the context of the second selector.
     *
     * @param array|string $mixSelector
     * @param string $strFunctionName
     * @param string $strFunctionName,... Unlimited OPTIONAL parameters to use as a parameter list to the function. List can
     *                                        end with a PRIORITY_* to prioritize the command.
     * @throws Caller
     */
    public static function executeSelectorFunction($mixSelector, $strFunctionName /*, ..., PRIORITY_* */)
    {
        $args = func_get_args();
        call_user_func_array([Application::instance()->jsResponse(), 'executeSelectorFunction'], $args);
    }


    /**
     * Call the given function with the given arguments. If just a function name, then the window object is searched.
     * The function can be inside an object accessible from the global namespace by separating with periods.
     * @param string $strFunctionName Can be namespaced, as in "qcubed.func".
     * @param string $strFunctionName,... Unlimited OPTIONAL parameters to use as a parameter list to the function. List can
     *                                        end with a PRIORITY_* to prioritize the command.
     */
    public static function executeJsFunction($strFunctionName /*, ... */)
    {
        $args = func_get_args();
        call_user_func_array([Application::instance()->jsResponse(), 'executeJsFunction'], $args);
    }

    /**
     * One time add of style sheets, to be used by QForm only for last minute style sheet injection.
     * @param string[] $strStyleSheetArray
     */
    public static function addStyleSheets(array $strStyleSheetArray)
    {
        Application::instance()->jsResponse()->addStyleSheets($strStyleSheetArray);
    }

    /**
     * Add an array of javascript files for one-time inclusion. Called by QForm. Do not call.
     * @param string[] $strJavaScriptFileArray
     */
    public static function addJavaScriptFiles($strJavaScriptFileArray)
    {
        Application::instance()->jsResponse()->addJavaScriptFiles($strJavaScriptFileArray);
    }

    /**
     * Outputs the current page with the buffer data.
     *
     * When directly outputting a QForm (Server or New), it needs to do some special things around cache control and content type.
     *
     * When outputting Ajax, it needs to send out JSON.
     *
     * Otherwise, in situations where we have some kind of PHP file that is doing more unique processing, like outputting a JPEG file,
     * a PDF or a REST service, we need to just send out the page unmodified, and trust that PHP file to do the right thing regarding
     * headers and the like.
     *
     * @param string $strBuffer Buffer data
     *
     * @return string
     */
    public static function outputPage($strBuffer)
    {
        global $_FORM;

        if (!Application::instance()->blnProcessOutput) {
            // We are processing a template, or outputting some other kind of file, like a jpeg or pdf
            return $strBuffer;
        }

        if (Q\Error\Manager::isError() ||
            Application::isAjax() ||
            empty($_FORM)
        ) {
            // Render scripts in the rare situation there is no $_FORM or render_end did not process them
            $strScript = Application::instance()->jsResponse()->renderJavascript();
            if ($strScript) {
                $strBuffer =  $strBuffer . '<script type="text/javascript">' . $strScript . '</script>';
            }
            return $strBuffer;
        } else {
            $file = "";
            $line = 0;
            if (!headers_sent()) {
                // We are outputting a Form
                header('Cache-Control: ' . static::$strCacheControl);
                // make sure the server does not override the character encoding value by explicitly sending it out as a header.
                // some servers will use an internal default if not specified in the header, and that will override the "encoding" value sent in the text.
                header(sprintf('Content-Type: %s; charset=%s', strtolower(static::$strContentType),
                    strtolower(QCUBED_ENCODING)));
            }

            return $strBuffer;
        }
    }

    public static function startOutputBuffering()
    {
        if (php_sapi_name() !== 'cli' &&    // Do not buffer the command line interface
            !defined('__NO_OUTPUT_BUFFER__')
        ) {
            ob_start('\QCubed\ApplicationBase::endOutputBuffering');
        }
    }

    public static function endOutputBuffering($strBuffer)
    {
        return static::outputPage($strBuffer);
    }


    /**
     * Render scripts for injecting files into the html output. This is for server only, not ajax.
     * This list will appear ahead of the javascript commands rendered below.
     *
     * @static
     * @return string
     */
    public static function renderFiles()
    {
        return Application::instance()->jsResponse()->renderFiles();
    }

    /**
     * Function renders all the Javascript commands as output to the client browser. This is a mirror of what
     * occurs in the success function in the qcubed.js ajax code.
     *
     * @param bool $blnBeforeControls True to only render the javascripts that need to come before the controls are defined.
     *                                This is used to break the commands issued into two groups.
     * @static
     * @return string
     */
    public static function renderJavascript($blnBeforeControls = false)
    {
        return Application::instance()->jsResponse()->renderJavascript($blnBeforeControls);
    }

    /**
     * Return the javascript command array, for use by form ajax response. Will erase the command array, so
     * the form better use it.
     * @static
     * @return array
     */
    public static function getJavascriptCommandArray()
    {
        return Application::instance()->jsResponse()->getJavascriptCommandArray();
    }


    /**
     * Print an ajax response to the browser.
     *
     * @param array $strResponseArray An array keyed with AjaxResponse items. These items will be read by the qcubed.js
     * ajax success function and operated on. The goals is to eventually have all possible response types represented
     * in the AjaxResponse so that we can remove the "eval" in qcubed.js.
     */
    public static function sendAjaxResponse(array $strResponseArray)
    {
        header('Content-Type: text/json'); // not application/json, as IE reportedly blows up on that, but jQuery knows what to do.
        $strJSON = Js\Helper::toJSON($strResponseArray);
        if (Application::encodingType() && Application::encodingType() != 'UTF-8') {
            $strJSON = iconv(Application::encodingType(), 'UTF-8', $strJSON); // json must be UTF-8 encoded
        }
        print($strJSON);
    }


    /**
     * Utility function to get the JS file URI, given a string input
     * @param string $strFile File name to be tested
     *
     * @return string the final JS file URI
     */
    public static function getJsFileUri($strFile)
    {
        if ((strpos($strFile, "http") === 0) || (strpos($strFile, "https") === 0)) {
            return $strFile;
        }
        if (strpos($strFile, "/") === 0) {
            return $strFile;
        } else {
            throw new Caller('Relative urls are no longer supported. Trying to load: ' . $strFile);
        }
    }

    /**
     * Utility function to get the CSS file URI, given a string input
     * @param string $strFile File name to be tested
     *
     * @return string the final CSS URI
     */
    public static function getCssFileUri($strFile)
    {
        if ((strpos($strFile, "http") === 0) || (strpos($strFile, "https") === 0)) {
            return $strFile;
        }
        if (strpos($strFile, "/") === 0) {
            return $strFile;
        }
        else {
            throw new Caller("Relative urls are no longer supported. Trying to load: " . $strFile);
        }
    }

    /**
     * For development purposes, this static method outputs all the Application static variables
     *
     * @return void
     */
    public static function varDump()
    {
        _p('<div class="var-dump"><strong>QCubed Settings</strong><ul>', false);
        /*
        $arrValidationErrors = QInstallationValidator::validate();
        foreach ($arrValidationErrors as $objResult) {
            printf('<li><strong class="warning">WARNING:</strong> %s</li>', $objResult->strMessage);
        }*/

        printf('<li>QCUBED_VERSION = "%s"</li>', QCUBED_VERSION);
        //printf('<li>jQuery version = "%s"</li>', __JQUERY_CORE_VERSION__);
        //printf('<li>jQuery UI version = "%s"</li>', __JQUERY_UI_VERSION__);
        printf('<li>QCUBED_PROJECT_INCLUDES_DIR = "%s"</li>', QCUBED_PROJECT_INCLUDES_DIR);
        printf('<li>QCUBED_ERROR_PAGE_PHP = "%s"</li>', QCUBED_ERROR_PAGE_PHP);
        printf('<li>PHP Include Path = "%s"</li>', get_include_path());
        printf('<li>EncodingType = "%s"</li>', Application::encodingType());
        printf('<li>PathInfo = "%s"</li>', Application::instance()->context()->pathInfo());
        printf('<li>QueryString = "%s"</li>', Application::instance()->context()->queryString());
        printf('<li>RequestUri = "%s"</li>', Application::instance()->context()->requestUri());
        printf('<li>ScriptFilename = "%s"</li>', Application::instance()->context()->scriptFileName());
        printf('<li>ScriptName = "%s"</li>', Application::instance()->context()->scriptName());
        printf('<li>ServerAddress = "%s"</li>', Application::instance()->context()->serverAddress());

        if (DatabaseService::isInitialized()) {
            for ($intKey = 1; $intKey <= DatabaseService::count(); $intKey++) {
                printf('<li>Database[%s] settings:</li>', $intKey);
                _p("<ul>", false);
                foreach (unserialize(constant('DB_CONNECTION_' . $intKey)) as $key => $value) {
                    if ($key == "password") {
                        $value = "hidden for security purposes";
                    }

                    _p("<li>" . $key . " = " . var_export($value, true) . "</li>", false);
                }
                _p("</ul>", false);
            }
        }
        _p('</ul></div>', false);
    }

    public static function isAuthorized($options = null)
    {
        return false; // must be overridden!
    }

    public static function checkAuthorized($options = null)
    {
        if (static::isAuthorized($options)) {
            return;
        }

        // If we're here -- then we're not allowed to access.  Present the Error/Issue.
        header($_SERVER['SERVER_PROTOCOL'] . ' 401 Access Denied');
        header('Status: 401 Access Denied', true);
        self::setProcessOutput(false);
        // throw new QRemoteAdminDeniedException(); ?? Really, throw an exception??
        exit();
    }
}
