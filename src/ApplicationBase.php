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
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Application;
    use QCubed\Database\Service as DatabaseService;
    use QCubed as Q;
    use Throwable;

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
     */
    abstract class ApplicationBase extends ObjectBase
    {
        // These constants help us to organize and build a list of responses to the client.
        public const string PRIORITY_STANDARD = '*jsMed*';
        public const string PRIORITY_HIGH = '*jsHigh*';
        public const string PRIORITY_LOW = '*jsLow*';
        /** Execute ONLY this command and exclude all others */
        public const string PRIORITY_EXCLUSIVE = '*jsExclusive*';
        /** Execute this command after all ajax commands have been completely flushed */
        public const string PRIORITY_LAST = '*jsFinal*';

        /**
         * @var bool Set to true to turn on short-term caching. This is an in-memory cache that caches a database
         * Objects only for as long as a single http request lasts. Depending on your application, this may speed
         * up your database access. It DOES increase the amount of memory used in a request.
         * */
        public static bool $blnLocalCache = false;
        public static mixed $forms;
        public static mixed $Database;

        /** @var string */
        protected static string $strCacheControl = 'private';
        /** @var string */
        protected static string $strContentType = "text/html";


        private static object|null $instance = null;


        /** @var Context|null */
        protected ?Context $objContext = null;
        /** @var  JsResponse|null */
        protected ?JsResponse $objJsResponse = null;
        /** @var  bool Current state of output, whether it should be minimized or not. */
        protected ?bool $blnMinimize = false;
        /** @var  Purifier|null The purifier service. */
        protected ?Purifier $objPurifier = null;
        /** @var bool */
        protected bool $blnProcessOutput = true;
        /** @var string  */
        protected string $strEncodingType = QCUBED_ENCODING;


        /**
         * Return and possibly create the application instance, which is a subclass of this class. It will be treated as
         * a singleton.
         *
         * @return Application|null
         */
        public static function instance(): ?Application
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
         * Sets the minimized state of the object.
         *
         * @param bool $blnMinimize Indicates whether to minimize the object.
         * @return bool The previous minimized state.
         */
        public function setMinimize(bool $blnMinimize): bool
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
        public function minimize(): bool
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
        public function context(): Context
        {
            if (!$this->objContext) {
                $this->objContext = new Context();
            }

            return $this->objContext;
        }

        /**
         * Returns a singleton jsResponse object. This is for internal use of the application class only. It manages
         * the JavaScript and JSON responses to requests.
         *
         * @return JsResponse
         */
        public function jsResponse(): JsResponse
        {
            if (!$this->objJsResponse) {
                $this->objJsResponse = new JsResponse();
            }

            return $this->objJsResponse;
        }

        /**
         * @return string   The application encoding type.
         */
        public static function encodingType(): string
        {
            return Application::instance()->strEncodingType;
        }

        /**
         * Sets the application encoding type and returns the previous encoding type.
         *
         * @param string $strEncodingType The new encoding type to be set.
         * @return string                   The previous encoding type.
         */
        public static function setEncodingType(string $strEncodingType): string
        {
            $strOldValue = Application::instance()->strEncodingType;
            Application::instance()->strEncodingType = $strEncodingType;
            return $strOldValue;
        }

        /**
         * Returns true if this is a QCubed Ajax call. Note that if you are calling an entry point with ajax, but not through
         * qcubed.js, then it will return false. If you want to know whether a particular entry point is being called with
         * ajax that might be serving up a REST api, for example, check requestMode() for Context::REQUEST_MODE_AJAX
         * @return bool
         */
        public static function isAjax(): bool
        {
            return Application::instance()->context()->requestMode() == Context::REQUEST_MODE_QCUBED_AJAX;
        }

        /**
         * Attempts to load a class file based on the class name provided.
         *
         * @param string $strClassName The name of the class to load.
         * @return bool                 True if the class file was successfully loaded, false otherwise.
         */
        public static function autoload(string $strClassName): bool
        {
            if (file_exists($strFilePath = sprintf('%s/plugins/%s.php', QCUBED_PROJECT_INCLUDES_DIR, $strClassName))) {
                require_once($strFilePath);
                return true;
            }
            return false;
        }

        /**
         * Sets a custom error handler.
         *
         * @param string $strName The name of the error handler.
         * @param int|null $intLevel The error level for which the handler will be invoked, or null for all levels.
         *
         * @throws Caller Thrown to indicate that this method is deprecated.
         * @noinspection PhpUnusedParameterInspection
         */
        public static function setErrorHandler(string $strName, ?int $intLevel = null): void
        {
            throw new Caller("SetErrorHandler is deprecated. Create an Error\\Handler instead.");
        }

        /**
         * Restores the error handler by throwing an exception indicating the deprecation of SetErrorHandler.
         *
         * @throws Caller   Thrown to indicate that SetErrorHandler is deprecated and an Error\Handler should be created instead.
         */
        public static function restoreErrorHandler(): void
        {
            throw new Caller("SetErrorHandler is deprecated. Create an Error\\Handler instead.");
        }

        /**
         * @param string $strText The text to be purified.
         * @param mixed|null $objCustomConfig Optional custom configuration for the purifier.
         * @return string The purified text.
         */
        public static function purify(string $strText, mixed $objCustomConfig = null): string
        {
            if (!Application::instance()->objPurifier) {
                Application::instance()->initPurifier();
            }
            return Application::instance()->objPurifier->purify($strText, $objCustomConfig);
        }

        /**
         * Initializes the purifier instance.
         *
         * This method is intended to set up and configure the required
         * purifier implementation. It should be implemented by subclasses
         * to ensure specific configuration or initialization tasks are handled.
         */
        abstract protected function initPurifier(): void;

        /**
         * Whether or not we are currently trying to Process the Output of the page.
         * Used by the OutputPage PHP output buffering handler.  As of PHP 5.2,
         * this gets called whenever ob_get_contents() is called.  Because some
         * classes like QFormBase utilize ob_get_contents() to perform template
         * evaluation without wanting to actually perform OutputPage, this flag
         * can be set/modified by QFormBase::EvaluateTemplate accordingly to
         * prevent OutputPage from executing.
         *
         * Also, set this to false if you are outputting custom headers, especially
         * if you send your own "Content-Type" header.
         *
         * @param bool $blnProcess The new value for the process output flag.
         * @return bool The previous value of the process output flag.
         */
        public static function setProcessOutput(bool $blnProcess): bool
        {
            $blnOldValue = Application::instance()->blnProcessOutput;
            Application::instance()->blnProcessOutput = $blnProcess;
            return $blnOldValue;
        }

        /**
         * Definition of CacheControl for the HTTP header. In general, it is recommended to keep this as "private".
         * But this can/should be overridden for a file/scripts that have special caching requirements.
         *
         * @param string $strControl The new cache control value to set.
         * @return string   The old cache control value.
         */
        public static function setCacheControl(string $strControl): string
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
        public static function setContentType(string $strContentType): string
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
         * @param bool $blnAbortCurrentScript Whether to abort the current script or finish it out so data gets saved.
         * @return void
         * @throws Throwable Exception
         */
        public static function redirect(string $strLocation, bool $blnAbortCurrentScript = true): void
        {
            if (!$blnAbortCurrentScript) {
                // Use the JavaScript command mechanism
                Application::instance()->jsResponse()->setLocation($strLocation);
            } else {
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
         * @param bool $blnAbortCurrentScript Whether to abort the current script or finish it out so data gets saved.
         * @return void
         * @throws Throwable Exception
         */
        public static function closeWindow(bool $blnAbortCurrentScript = false): void
        {
            if (!$blnAbortCurrentScript) {
                // Use the JavaScript command mechanism
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
         * Set a cookie. Allows setting of cookies in response to ajax requests.
         *
         * @param string $strName
         * @param string $strValue
         * @param QDateTime $dttTimeout
         * @param string $strPath
         * @param string|null $strDomain
         * @param bool $blnSecure
         */
        public static function setCookie(
            string    $strName,
            string    $strValue,
            QDateTime $dttTimeout,
            string    $strPath = '/',
            ?string    $strDomain = null,
            ?bool $blnSecure = false
        ): void
        {
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
         * Deletes the given cookie IF its set. In other words, you cannot set a cookie and then delete a cookie right away before the
         * cookie gets sent to the browser.
         *
         * @param string $strName The name of the cookie to be deleted.
         * @return void
         */
        public static function deleteCookie(string $strName): void
        {
            if (isset($_COOKIE[$strName])) { // don't post a cookie if it's not set
                $dttTimeout = QDateTime::now();
                $dttTimeout->addYears(-5);

                self::setCookie($strName, "", $dttTimeout);
            }
        }


        /**
         * Causes the browser to display a JavaScript alert() box with a supplied message
         * @param string|null $strMessage Message to be displayed
         */
        public static function displayAlert(?string $strMessage): void
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
         * @param string $strJavaScript the JavaScript to execute
         * @param string $strPriority
         * @throws Caller
         */
        public static function executeJavaScript(string $strJavaScript, string $strPriority = self::PRIORITY_STANDARD): void
        {
            Application::instance()->jsResponse()->executeJavaScript($strJavaScript, $strPriority);
        }

        /**
         * Execute a function on a particular control. Many JavaScript widgets are structured this way, and this gives us
         * a general-purpose way of sending commands to widgets without an 'eval' on the client side.
         *
         * Commands will be executed in the order received, along with ExecuteJavaScript commands and ExecuteObjectCommands.
         * If you want to force a command to execute first, give it high priority, or last, give it low priority.
         *
         * @param string $strControlId I'd of control to direct the command to.
         * @param string $strFunctionName Unlimited OPTIONAL parameters to use as a parameter list to the function. List can
         *                                        end with a PRIORITY_* to prioritize the command.
         */
        public static function executeControlCommand(string $strControlId, string $strFunctionName /*, ..., PRIORITY_* */): void
        {
            $args = func_get_args();
            call_user_func_array([Application::instance()->jsResponse(), 'executeControlCommand'], $args);
        }

        /**
         * Call a function on a jQuery selector. The selector can be a single string or an array where the first
         * item is a selector specifying the items within the context of the second selector.
         *
         * @param array|string $mixSelector
         * @param string $strFunctionName Unlimited OPTIONAL parameters to use as a parameter list to the function. List can
         *                                        end with a PRIORITY_* to prioritize the command.
         */
        public static function executeSelectorFunction(array|string $mixSelector, string $strFunctionName /*, ..., PRIORITY_* */): void
        {
            $args = func_get_args();
            call_user_func_array([Application::instance()->jsResponse(), 'executeSelectorFunction'], $args);
        }


        /**
         * Call the given function with the given arguments. If just a function name, then the window object is searched.
         * The function can be inside an object accessible from the global namespace by separating with periods.
         *
         * @param string $strFunctionName Unlimited OPTIONAL parameters to use as a parameter list to the function. List can
         *                                        end with a PRIORITY_* to prioritize the command.
         */
        public static function executeJsFunction(string $strFunctionName /*, ... */): void
        {
            $args = func_get_args();
            call_user_func_array([Application::instance()->jsResponse(), 'executeJsFunction'], $args);
        }

        /**
         * One time add of style sheets, to be used by QForm only for last minute style sheet injection.
         * @param string[] $strStyleSheetArray
         */
        public static function addStyleSheets(array $strStyleSheetArray): void
        {
            Application::instance()->jsResponse()->addStyleSheets($strStyleSheetArray);
        }

        /**
         * Add an array of JavaScript files for one-time inclusion. Called by QForm. Do not call.
         * @param string[] $strJavaScriptFileArray
         */
        public static function addJavaScriptFiles(array $strJavaScriptFileArray): void
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
        public static function outputPage(string $strBuffer): string
        {
            global $_FORM;

            if (!Application::instance()->blnProcessOutput) {
                // We are processing a template, or outputting some other kind of file, like a JPEG or PDF
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
            } else {
                $file = "";
                $line = 0;
                if (!headers_sent()) {
                    // We are outputting a Form
                    header('Cache-Control: ' . static::$strCacheControl);
                    // Make sure the server does not override the character encoding value by explicitly sending it out as a header.
                    // Some servers will use an internal default if not specified in the header, and that will override the "encoding" value sent in the text.
                    header(sprintf('Content-Type: %s; charset=%s', strtolower(static::$strContentType),
                        strtolower(QCUBED_ENCODING)));
                }

            }
            return $strBuffer;
        }

        /**
         * Starts output buffering, unless running in the CLI environment or explicitly disabled.
         *
         * @return void
         */
        public static function startOutputBuffering(): void
        {
            if (php_sapi_name() !== 'cli' &&    // Do not buffer the command line interface
                !defined('__NO_OUTPUT_BUFFER__')
            ) {
                ob_start('\QCubed\ApplicationBase::endOutputBuffering');
            }
        }

        /**
         * Ends the output buffering process and processes the given buffer content.
         *
         * @param string $strBuffer The buffer content to be processed.
         *
         * @return string The processed output after ending the buffering.
         */
        public static function endOutputBuffering(string $strBuffer): string
        {
            return static::outputPage($strBuffer);
        }


        /**
         * Render scripts for injecting files into the HTML output. This is for a server only, not ajax.
         * This list will appear ahead of the JavaScript commands rendered below.
         *
         * @static
         * @return string
         * @throws Caller
         */
        public static function renderFiles(): string
        {
            return Application::instance()->jsResponse()->renderFiles();
        }

        /**
         * Function renders all the JavaScript commands as output to the client browser. This is a mirror of what
         * occurs in the success function in the qcubed.js ajax code.
         *
         * @param bool $blnBeforeControls True to only render the JavaScripts that need to come before the controls are defined.
         *                                This is used to break the commands issued into two groups.
         * @static
         * @return string
         */
        public static function renderJavascript(bool $blnBeforeControls = false): string
        {
            return Application::instance()->jsResponse()->renderJavascript($blnBeforeControls);
        }

        /**
         * Return the JavaScript command array, for use by form ajax response. Will erase the command array, so
         * the form better use it.
         * @static
         * @return array
         */
        public static function getJavascriptCommandArray(): array
        {
            return Application::instance()->jsResponse()->getJavascriptCommandArray();
        }


        /**
         * Print an ajax response to the browser.
         *
         * Ajax's success function and operated on. The goal is to eventually have all possible response types represented
         * in the AjaxResponse so that we can remove the "eval" in qcubed.js.
         *
         * @param array $strResponseArray The response array to encode and send as JSON.
         * @return void This method does not return a value.
         * @throws Throwable Exception Throws an exception in case of a JSON encoding error.
         */
        public static function sendAjaxResponse(array $strResponseArray): void
        {
            header('Content-Type: text/json'); // not application/json, as IE reportedly blows up on that, but jQuery knows what to do.
            array_walk_recursive($strResponseArray, function (&$item) {
                if (is_string($item) && !mb_check_encoding($item, 'UTF-8')) {
                    $item = mb_convert_encoding($item, 'UTF-8', 'auto');
                }
            });

            $strJSON = Js\Helper::toJSON($strResponseArray);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new  InvalidCast("JSON encoding error: " . json_last_error_msg()); //Using the global Exception class
            }

            if (Application::encodingType() && Application::encodingType() != 'UTF-8') {
                $strJSON = iconv(Application::encodingType(), 'UTF-8', $strJSON);// JSON must be UTF-8 encoded
            }

            print($strJSON);
        }

        /**
         * Utility function to get the JS file URI, given a string input
         * @param string $strFile File name to be tested
         *
         * @return string the final JS file URI
         * @throws Caller
         */
        public static function getJsFileUri(string $strFile): string
        {
            if ((str_starts_with($strFile, "http")) || (str_starts_with($strFile, "https"))) {
                return $strFile;
            }
            if (str_starts_with($strFile, "/")) {
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
         * @throws Caller
         */
        public static function getCssFileUri(string $strFile): string
        {
            if ((str_starts_with($strFile, "http")) || (str_starts_with($strFile, "https"))) {
                return $strFile;
            }
            if (str_starts_with($strFile, "/")) {
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
        public static function varDump(): void
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

        /**
         * Checks if the current operation is authorized.
         *
         * @param mixed $options Optional parameters that may influence the authorization check.
         * @return bool Returns false by default. This method should be overridden to implement actual authorization logic.
         * @noinspection PhpUnusedParameterInspection
         */
        public static function isAuthorized(mixed $options = null): bool
        {
            return false; // must be overridden!
        }

        /**
         * Checks if the current user or process is authorized to proceed.
         *
         * @param mixed|null $options An optional parameter to pass authorization options.
         * @return void No value is returned by this method.
         */
        public static function checkAuthorized(mixed $options = null): void
        {
            if (static::isAuthorized($options)) {
                return;
            }

            // If we're here -- then we're not allowed to access.  Present the Error/Issue.
            header($_SERVER['SERVER_PROTOCOL'] . ' Access 401 Denied');
            header('Status: 401 Access Denied');
            self::setProcessOutput(false);
            // throw new QRemoteAdminDeniedException(); ?? Really, throw an exception??
            exit();
        }
    }