<?php
if (!defined('__PREPEND_INCLUDED__')) {
    // Ensure prepend.inc is only executed once
    define('__PREPEND_INCLUDED__', 1);


    ///////////////////////////////////
    // Define Server-specific constants
    ///////////////////////////////////
    /*
     * This assumes that the configuration include file is in the same directory
     * as this prepend include file.  For security reasons, you can feel free
     * to move the configuration file anywhere you want.  But be sure to provide
     * a relative or absolute path to the file.
     */
    require(__DIR__ . '/configuration.inc.php');

    require_once(APP_DIR . '/src/version.inc.php');     // Include the hard-coded QCubed version number
    require_once(QCUBED_BASE_DIR . '/common/src/Error/Manager.php');   // Include the error manager so we can process errors immediately
    \QCubed\Error\Manager::initialize();

    //////////////////////////////
    // Register the autoloader so we can find our files
    //////////////////////////////
    require_once(QCUBED_BASE_DIR . '/common/src/AutoloaderService.php');   // Find the autoloader
    \QCubed\AutoloaderService::instance()
        ->initialize(dirname(QCUBED_BASE_DIR) )   // register with the vendor directory
        ->addPsr4('QCubed\\Project\\', QCUBED_PROJECT_DIR . '/qcubed')
        ->addClassmapFile(QCUBED_APP_INCLUDES_DIR . '/app_includes.inc.php')
        // temp includes
        ->addPsr4('QCubed\\', APP_DIR . '/src')
    ;

    if (!defined('QCUBED_CODE_GENERATING')) {
        if (file_exists(QCUBED_PROJECT_MODEL_GEN_DIR . '/_class_paths.inc.php')) {
            \QCubed\AutoloaderService::instance()->addClassmapFile(QCUBED_PROJECT_MODEL_GEN_DIR . '/_class_paths.inc.php');
        }
        if (file_exists(QCUBED_PROJECT_MODEL_GEN_DIR . '/_type_class_paths.inc.php')) {
            \QCubed\AutoloaderService::instance()->addClassmapFile(QCUBED_PROJECT_MODEL_GEN_DIR . '/_type_class_paths.inc.php');
        }
        if (file_exists(QCUBED_PROJECT_MODEL_GEN_DIR . '/QQN.php')) {
            require_once(QCUBED_PROJECT_MODEL_GEN_DIR . '/QQN.php');
        }
    }

    // Register the custom autoloader, making sure we go after the previous autoloader
    spl_autoload_register(array('\\QCubed\\Project\\Application', 'autoload'), true, false);

    /*
    if (defined('QCUBED_APP_INCLUDES_DIR')) {
        require_once(QCUBED_APP_INCLUDES_DIR . '/app_includes.inc.php');    // autoload local files
    }
*/
    //////////////////////////
    // Custom Global Functions
    //////////////////////////
    // Define any custom global functions (if any) here...


    ////////////////
    // Include Files
    ////////////////
    // Include any other include files (if any) here...

    require_once(QCUBED_BASE_DIR . '/i18n/tools/i18n-app.inc.php'); // Include the translation shortcuts. See the Application for translation setup.
    require_once(APP_DIR . '/src/utilities.inc.php');     // Shortcuts used throughout the framework

    ////////////////////////////////////////////////
    // Initialize the Application and DB Connections
    ////////////////////////////////////////////////
    \QCubed\Database\Service::initializeDatabaseConnections();
    \QCubed\Project\Application::instance()->initializeServices();

    \QCubed\Project\Application::startOutputBuffering();
}