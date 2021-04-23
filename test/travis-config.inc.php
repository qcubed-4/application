<?php

/**
 * The base configuration file for the travis test.
 */
$workingDir = getcwd();
define('__WORKING_DIR__', $workingDir);
define('APP_DIR', __WORKING_DIR__);

// Configure
require( __WORKING_DIR__ . '/project/includes/configuration/prepend.inc.php');

\QCubed\AutoloaderService::instance()
    ->addPsr4('QCubed\\', __WORKING_DIR__ . '/src')    // Add in our own code
    ->addPsr4("QCubed\\Codegen\\Generator\\", __WORKING_DIR__ . '/codegen/generator/')
;

//codegen
$db = getenv("DB");
if (!$db) {
    $db = 'mysql';  // default to mysql, just for local testing of travis build
}
require_once (__DIR__ . '/travis/' . $db . '.inc.php');
require_once(QCUBED_PROJECT_DIR . '/qcubed/Codegen/CodegenBase.php');
require( QCUBED_ORM_TOOLS_DIR . '/codegen.cli.php');

// Add in the built files that were missed from prepend.inc.php because they didn't exist before codegen

if (file_exists(QCUBED_PROJECT_MODEL_GEN_DIR . '/_class_paths.inc.php')) {
    \QCubed\AutoloaderService::instance()->addClassmapFile(QCUBED_PROJECT_MODEL_GEN_DIR . '/_class_paths.inc.php');
}
if (file_exists(QCUBED_PROJECT_MODEL_GEN_DIR . '/_type_class_paths.inc.php')) {
    \QCubed\AutoloaderService::instance()->addClassmapFile(QCUBED_PROJECT_MODEL_GEN_DIR . '/_type_class_paths.inc.php');
}
if (file_exists(QCUBED_PROJECT_MODEL_GEN_DIR . '/QQN.php')) {
    require_once(QCUBED_PROJECT_MODEL_GEN_DIR . '/QQN.php');
}
