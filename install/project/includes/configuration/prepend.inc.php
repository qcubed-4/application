<?php

use QCubed\AutoloaderService;
use QCubed\Database\Service;
use QCubed\Error\Manager;
use QCubed\Project\Application;
use Random\RandomException;

if (!defined('__PREPEND_INCLUDED__')) {
    // Ensure prepend.inc is only executed once
    define('__PREPEND_INCLUDED__', 1);


    ///////////////////////////////////
    // Define Server-specific constants
    ///////////////////////////////////
    /*
    * This assumes that the file containing the configuration is in the same directory
    * as this prefix contains the file. For security reasons, you can feel free
    * to move the configuration file wherever you want. But be sure to provide
    * the relative or absolute path to the file.
    */

    if (file_exists(__DIR__ . '/configuration.inc.php')) {
        require(__DIR__ . '/configuration.inc.php');
    } else {
        // The minimal constants set to work
        define ('QCUBED_PROJECT_DIR', dirname(__FILE__) . '/../..');
        define ('QCUBED_PROJECT_INCLUDES_DIR', dirname(__FILE__) . '/..');
        define ('QCUBED_APP_INCLUDES_DIR', QCUBED_PROJECT_INCLUDES_DIR . '/app_includes');
        define ('QCUBED_PROJECT_MODEL_DIR', QCUBED_PROJECT_INCLUDES_DIR . '/model' );
        define ('QCUBED_PROJECT_MODELCONNECTOR_DIR', QCUBED_PROJECT_INCLUDES_DIR . '/meta_controls');
        define ('QCUBED_PROJECT_MODEL_GEN_DIR', QCUBED_PROJECT_DIR . '/generated/model_base' );
    }

    require_once(QCUBED_BASE_DIR . '/application/src/version.inc.php');     // Include the hard-coded QCubed version number
    require_once(QCUBED_BASE_DIR . '/common/src/Error/Manager.php');   // Include the error manager so we can process errors immediately

    Manager::initialize();

    //////////////////////////////
    // Register the autoloader so we can find our files
    //////////////////////////////

    require_once(QCUBED_BASE_DIR . '/common/src/AutoloaderService.php');   // Find the autoloader

    try {
        AutoloaderService::instance()
            ->initialize(dirname(QCUBED_BASE_DIR))   // register with the vendor directory
            ->addPsr4('QCubed\\Project\\', QCUBED_PROJECT_DIR . '/qcubed')
            ->addPsr4('QCubed\\Plugin\\', QCUBED_PROJECT_DIR . '/includes/plugins')
            ->addClassmapFile(QCUBED_APP_INCLUDES_DIR . '/app_includes.inc.php');
    } catch (Exception $e) {

    }

    if (!defined('QCUBED_CODE_GENERATING')) {
        if (file_exists(QCUBED_PROJECT_MODEL_GEN_DIR . '/_class_paths.inc.php')) {
            AutoloaderService::instance()->addClassmapFile(QCUBED_PROJECT_MODEL_GEN_DIR . '/_class_paths.inc.php');
        }
        if (file_exists(QCUBED_PROJECT_MODEL_GEN_DIR . '/_type_class_paths.inc.php')) {
            AutoloaderService::instance()->addClassmapFile(QCUBED_PROJECT_MODEL_GEN_DIR . '/_type_class_paths.inc.php');
        }
        if (file_exists(QCUBED_PROJECT_MODEL_GEN_DIR . '/QQN.php')) {
            require_once(QCUBED_PROJECT_MODEL_GEN_DIR . '/QQN.php');
        }
    }

    // Register the custom autoloader, making sure we go after the previous autoloader
    spl_autoload_register(array('\\QCubed\\Project\\Application', 'autoload'));

    /*
    If (defined('QCUBED_APP_INCLUDES_DIR')) {
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
    require_once(QCUBED_BASE_DIR . '/application/src/utilities.inc.php');     // Shortcuts used throughout the framework

    ////////////////////////////////////////////////
    // Initialize the Application and DB Connections
    ////////////////////////////////////////////////

    try {
        Service::initializeDatabaseConnections();
    } catch (Exception $e) {

    }

    if (!defined('QCUBED_CODE_GENERATING')) {
        try {
            Application::instance()->initializeServices();
        } catch (RandomException $e) {

        }
    }

    Application::startOutputBuffering();
}