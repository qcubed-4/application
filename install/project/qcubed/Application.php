<?php

namespace QCubed\Project;

use QCubed;
use QCubed\Purifier;
use QCubed\I18n\TranslationService;
use QCubed\Project\Watcher\Watcher;


/**
 * Class Application
 *
 * This is the subclass of the main application singleton object. Use this to customize the behavior of the default
 * application, and to add your own globally accessible methods and properties specific to your application.
 *
 * @package QCubed\Project
 * @was QApplication
 */
class Application extends QCubed\ApplicationBase
{

    // define any services you will need for your application here
    //protected $authService;

    /**
     * This is called by the PHP5 Autoloader.  This method overrides the
     * one in ApplicationBase.
     *
     * @param string $strClassName
     * @return bool
     */
    public static function autoload($strClassName)
    {
        if (!parent::autoload($strClassName)) {
            // Run any custom autoloading functionality (if any) here...
            // return true; if you find the class
        }
        return false;
    }

    /**
     * Set up your application specific services here.
     */
    public function initializeServices()
    {
        error_log("Services initialized");

        $this->startSession();  // make sure we start the session first in case other services need it.
        $this->initCsrfProtection();
        $this->initTranslator();
        $this->initWatcher();

        //$this->verifyCsrfToken();

        //$this->authService = new \Project\Service\Auth();
    }
    /**
     * If you want to use a custom session handler, set it up here. The commented example below uses a QCubed handler that
     * puts sessions in a database.
     */
    protected function startSession()
    {
        /*
        QDbBackedSessionHandler::initialize(DB_BACKED_SESSION_HANDLER_DB_INDEX,
            DB_BACKED_SESSION_HANDLER_TABLE_NAME);*/

        // start the session
        session_start();
    }

    /**
     * Initialize the translator singleton. See the I18N library for details on how to configure this.
     * If you do nothing, no translation will happen.
     */
    protected function initTranslator()
    {
        $translator = new \QCubed\I18n\SimpleCacheTranslator();

        $translator->bindDomain('app', QCUBED_PROJECT_DIR . "/i18n")  // set to application's i18n directory
            ->setDefaultDomain('app')
            ->setTempDir(QCUBED_CACHE_DIR);
        TranslationService::instance()->setTranslator($translator);

        // If the user or you want a language other than english, set that here.
        //TranslationService::instance()->setLanguage('et');
    }

    /**
     * Initialize the purify which purifies text that comes from the user, preventing cross-site scripting attacks.
     * This is a default purifier. You can modify individual text boxes if needed.
     */
    public function initPurifier() {
        $this->objPurifier = new Purifier();
    }

    /**
     * Initialize your watcher class here, if needed.
     */
    protected function initWatcher() {
        Watcher::initialize();
    }

    /**
     * Initializes CSRF protection by setting up both persistent and dynamic CSRF tokens.
     *
     * @return void
     */
    protected function initCsrfProtection() {
        // If there is no CSRF token in the session, generate and store a persistent token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Initial persistent token
        }

        // Generate a dynamic CSRF token for each request
        $GLOBALS['_csrf_token'] = bin2hex(random_bytes(32)); // Dynamic token for every request
    }

    /**
     * Verifies the CSRF token by comparing the token from the POST request with the session value.
     *
     * There are several methods to implement CSRF protection. The simplest approach is as follows:
     *
     * In your formCreate():
     * <code>
     * // Add all the necessary inputs for your form.
     * </code>
     *
     * Usage example in a function such as Submit_Click:
     * <code>
     * protected function Submit_Click(ActionParams $params) {
     *     // Validate the CSRF token.
     *     if (!Application::verifyCsrfToken()) {
     *         // Developers can define how they want to handle invalid tokens.
     *         // The following Application::DisplayAlert() is a generalized example.
     *         Application::DisplayAlert('CSRF Token is invalid! The request was aborted.');
     *         return;
     *     }
     *
     *     // If the token is valid, continue processing the data.
     *     // Save necessary inputs or perform other actions as required by the developer.
     * }
     * </code>
     *
     * Note: This method is not limited to click events; it can also be applied to other events like change, etc.
     *
     * @return bool Returns true if the CSRF token is valid, false otherwise.
     */

    /**
     * Verifies the CSRF token by comparing the token from the POST request
     * with the token stored in the session.
     *
     * @return bool Returns true if the tokens match, otherwise false.
     */
    public static function verifyCsrfToken(): bool {
        // Check if the CSRF token from the POST request is missing or does not match the session token
        if (empty($_POST['Qform__FormCsrfToken']) || $_POST['Qform__FormCsrfToken'] !== $_SESSION['csrf_token']) {
            return false; // Token is invalid or missing, return false
        }

        return true; // Token is valid, return true
    }

    /**
     * This is a stub function for you to check permissions for the current user.
     *
     * @param $options  Anything you want. Could be permissions required to view the current page. You would
     * then make sure the current user is logged in, and has permissions that matched the given permissions.
     *
     * @return bool
     */
    public static function isAuthorized($options = null) {
        return true;
    }

}
