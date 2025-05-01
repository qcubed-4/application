<?php
require_once('../qcubed.inc.php');

/**
 * Make sure user has at least set up the url pointer.
 */

if (!defined('QCUBED_URL_PREFIX')) {
    echo "<span class='error' style='font-weight: 500;'>Cannot find the configuration file. Make sure your qcubed.inc.php file is installed correctly.</span>"; 
    exit;
}
if (QCUBED_URL_PREFIX == '{ url_prefix }') {
    // config file has not been set up correctly
    // Suggest a recommended value based on vendor-dir in composer.json
    $composerFile = dirname(__DIR__) . '/composer.json';
    $composer   = is_file($composerFile) ? json_decode(file_get_contents($composerFile), true) : [];
    $vendorDir = sprintf('/%s/', $composer['config']['vendor-dir'] ?? 'vendor');
    $prefixPos = strpos($_SERVER['PHP_SELF'], $vendorDir);
    $suggestedPrefix = ($prefixPos !== false) ? substr($_SERVER['PHP_SELF'], 0, $prefixPos) : '';
    echo "<span class='error' style='font-weight: 500;'>Your config file is not set up correctly. 
        Please edit <code>install/project/includes/configuration/active/0config.cfg.php</code> 
        and replace <code>{ url_prefix }</code> with '<strong>" . htmlspecialchars($suggestedPrefix) . "</strong>'.</span>";
    exit;
}

$strPageTitle = 'QCubed-4 Development Framework - Start Page';
require(QCUBED_CONFIG_DIR . '/header.inc.php');
?>
    <h1 class="page-title">
        <img id="qcubed-4_logo" src="<?= (QCUBED_IMAGE_URL . '/qcubed-4_logo.png'); ?>" alt="QCubed-4 Framework" />Welcome to QCubed-4!</h1>
    <div class="install-status">
        <p><strong>If you are seeing this, the framework has been successfully installed.</strong></p>
    </div>
    <h2>Next Steps</h2>
    <ul class="link-list">
        <li><a href="<?= QCUBED_CODEGEN_URL ?>">Code Generator</a> - to create ORM model objects that map to tables in your database, and ModelConnectors
            and form drafts to edit and display the data.</li>
        <li><a href="form_drafts.php">View Form Drafts</a> - to view the generated files (after you run the Code Generator).</li>
        <li><a href="<?php _p(QCUBED_EXAMPLES_URL) ?>/index.php">QCubed Examples</a> - learn QCubed by studying and modifying the example files locally.</li>
        <li><a href="../test/localtest.php">QCubed Unit Tests</a> - set of tests that QCubed developers use to verify the integrity of the framework.
            You must install the test SQL database and codegen_options.json file to run the tests. These can be found in the <?php _p(QCUBED_EXAMPLES_DIR)?> directory.</li>
        <li><a href="plugin_manager.php">QCubed Libraries</a> - Installed QCubed Libraries</li>
    </ul>
<?php if (\QCubed\Project\Application::isAuthorized()) { ?>
    <pre><code><?php \QCubed\Project\Application::varDump(); ?></code></pre>
<?php } ?>
<?php require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>
