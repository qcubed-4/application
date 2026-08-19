<?php

    namespace QCubed\Project;

    use QCubed;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\I18n\SimpleCacheTranslator;
    use QCubed\Purifier;
    use QCubed\I18n\TranslationService;
    use QCubed\Project\Watcher\Watcher;
    use QCubed\QDateTime;
    use Random\RandomException;
    use Psr\SimpleCache\InvalidArgumentException;
    // use QCubed\Session\DatabaseHandler;
    use User;


    /**
     * Class Application
     *
     * This is the subclass of the main application singleton object. Use this to customize the behavior of the default
     *  application and to add your own globally accessible methods and properties specific to your application.
     *
     * @package QCubed\Project
     * @was QApplication
     */
    class Application extends QCubed\ApplicationBase
    {

        // define any services you will need for your application here
        //protected $authService;

        /**
         * Delegates class autoloading to the base application.
         *
         * Override this method when project-specific autoloading behavior is required.
         *
         * @param string $strClassName Fully qualified class name to load.
         *
         * @return bool True when the class was successfully loaded.
         */
        public static function autoload(string $strClassName): bool
        {
            return parent::autoload($strClassName);
        }

        /**
         * Initializes application-wide services.
         *
         * The session is started first because CSRF protection, authentication,
         * translation, and other services may depend on session data.
         *
         * @return void
         * @throws RandomException
         * @throws InvalidArgumentException
         */
        public function initializeServices(): void
        {
            $this->startSession();
            $this->initCsrfProtection();
            $this->initTranslator();
            $this->initWatcher();
        }

        /**
         * Starts the PHP session for the application.
         *
         * A custom session handler may be initialized here before session_start()
         * when required by the project. For example, QCubed provides the
         * DatabaseHandler implementation for storing sessions in a database.
         *
         * @return void
         */
        protected function startSession(): void
        {
            /*
             * Optional database-backed session storage.
             *
             * Applications that need centralized session storage may initialize
             * DatabaseHandler before starting the PHP session.
             *
             * DatabaseHandler::initialize(
             *     DB_BACKED_SESSION_HANDLER_DB_INDEX,
             *     DB_BACKED_SESSION_HANDLER_TABLE_NAME
             * );
             */

            session_start();
        }

        /**
         * Initializes the translation system for the application.
         * Configures a translator instance with the application's internationalization directory,
         * sets the default domain, and specifies a temporary directory for caching.
         * Register the translator within the translation service.
         *
         * @return void
         * @throws InvalidArgumentException
         */
        protected function initTranslator(): void
        {
            $translator = new SimpleCacheTranslator();

            $translator->bindDomain('app', QCUBED_PROJECT_DIR . "/i18n")
                ->setDefaultDomain('app')
                ->setTempDir(QCUBED_CACHE_DIR);

            TranslationService::instance()->setTranslator($translator);

            // TranslationService::instance()->setLanguage('en');
        }

        /**
         * Initializes the purifier instance.
         * @return void
         */
        public function initPurifier(): void
        {
            $this->objPurifier = new Purifier();
        }

        /**
         * Initializes the application watcher.
         *
         * @return void
         */
        protected function initWatcher(): void
        {
            Watcher::initialize();
        }

        /**
         * Initializes CSRF protection for the current session.
         *
         * A CSRF token is generated once when it does not yet exist in the session.
         * The same token is reused for form rendering and subsequent POST/Ajax
         * validation throughout the session.
         *
         * Keeping token generation centralized prevents the session token from being
         * replaced while a previously rendered form still contains the original token.
         *
         * @return void
         * @throws RandomException
         */
        protected function initCsrfProtection(): void
        {
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            $GLOBALS['_csrf_token'] = $_SESSION['csrf_token'];
        }

        /**
         * Verifies the CSRF token submitted with a QCubed form request.
         *
         * The submitted form token is compared with the token stored in the current
         * session. Validation works for both regular form submissions and Ajax events.
         *
         * This method only validates the token. It does not regenerate or modify the
         * session token.
         *
         * Example:
         *
         * <code>
         * if (!Application::verifyCsrfToken()) {
         *     // Handle an invalid or expired request.
         *     return;
         * }
         * </code>
         *
         * @return bool True when both tokens exist and match; otherwise false.
         */
        public static function verifyCsrfToken(): bool
        {
            $postedToken = $_POST['Qform__FormCsrfToken'] ?? '';
            $sessionToken = $_SESSION['csrf_token'] ?? '';

            if ($postedToken === '' || $sessionToken === '') {
                return false;
            }

            return hash_equals($sessionToken, $postedToken);
        }

        /**
         * Determines if the user or process is authorized based on the given options.
         *
         * @param mixed|null $options An optional parameter to evaluate authorization context.
         * @return bool Returns true if authorized, otherwise false.
         */
        public static function isAuthorized(mixed $options = null): bool
        {
            return true;
        }
    }