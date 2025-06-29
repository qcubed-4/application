<?php

/* Relative File Paths for Web Accessible Directories
 *
 * Specify the path that someone entered into a browser to refer to the files and directories listed.
 * Most commonly, this would be the path from the browser's document root directory, but various web server
 * configurations might make it so that server paths do not correspond to a browser URL path.
 *
 * For some directories (e.g., the Examples site), if you are no longer using it, you STILL need to
 * have the constant defined.  But feel free to define the directory constant as blank (e.g. '') or null.
 *
 * Note that paths must have a leading slash and no ending slash. All definitions start with QCUBED_ as a way of
 * "namespacing" the definitions. See the config.regex.php file for the transformations that are done to convert to these
 * new definitions.
 *
 * For development purposes, you will not likely need to change any of these. Production needs may vary, though.
 */

const QCUBED_JS_URL = QCUBED_BASE_URL . '/application/assets/js';
const QCUBED_CSS_URL = QCUBED_BASE_URL . '/application/assets/css';
const QCUBED_PHP_URL = QCUBED_BASE_URL . '/application/assets/php';
const QCUBED_IMAGE_URL = QCUBED_BASE_URL . '/application/assets/images';

// Location of the Examples site
const QCUBED_EXAMPLES_URL = QCUBED_PHP_URL . '/examples';
const QCUBED_EXAMPLES_DIR = QCUBED_BASE_DIR . '/application/assets/php/examples';   // corresponding physical dir

define ('QCUBED_VENDOR_URL', dirname(QCUBED_BASE_URL));

const QCUBED_ITEMS_PER_PAGE = 20;

// Location of asset files for your application
const QCUBED_PROJECT_JS_URL = QCUBED_PROJECT_ASSETS_URL . '/js';
const QCUBED_PROJECT_CSS_URL = QCUBED_PROJECT_ASSETS_URL . '/css';
const QCUBED_PROJECT_PHP_URL = QCUBED_PROJECT_ASSETS_URL . '/php';
const QCUBED_PROJECT_IMAGE_URL = QCUBED_PROJECT_ASSETS_URL . '/images';

// Location of a base qcubed CSS file. Swap comments to use your own version.
const QCUBED_CSS = QCUBED_CSS_URL . '/qcubed.css';
//define ('QCUBED_CSS', QCUBED_PROJECT_CSS_URL . '/qcubed.css');

// There are multiple ways to add jQuery JS files to QCubed, all demonstrated below

// Minified versions
//define ('QCUBED_JQUERY_JS', 'https://code.jquery.com/jquery-1.12.4.min.js');
//define ('QCUBED_JQUERY_JS', 'https://code.jquery.com/jquery-3.2.1.min.js');
//define ('QCUBED_JQUI_JS', ' http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js');

// The original, non-minified jQuery for debugging purposes.
//define ('QCUBED_JQUERY_JS', 'https://code.jquery.com/jquery-1.12.4.js');
//define ('QCUBED_JQUERY_JS', 'https://code.jquery.com/jquery-3.2.1.js');
//define ('QCUBED_JQUI_JS', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js');

const QCUBED_JQUERY_JS = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer';

// The local versions. Useful for doing development when you don't have internet connectivity
//define ('QCUBED_JQUERY_JS', QCUBED_JS_URL . '/jquery.js');
//define ('QCUBED_JQUERY_JS', QCUBED_JS_URL . '/jquery3.js');
const QCUBED_JQUI_JS = QCUBED_JS_URL . '/jquery-ui.js';
//const QCUBED_JQUI_JS = 'https://code.jquery.com/ui/1.14.1/jquery-ui.js';

/** Specific files */

// The core qcubed JavaScript file to be used.
// In production or as a performance tweak, you may want to use the compressed "_qc_packed.js" library
const QCUBED_JS = QCUBED_JS_URL . '/qcubed.js';
//define ('QCUBED_JS',  '_qc_packed.js');

// Point to your own version of the JQuery UI theme here
const QCUBED_JQUI_CSS = QCUBED_CSS_URL . '/jquery-ui.css';

// A wonderful, free, library of scalable icons as fonts. We use it in DataGrid. Point to a local copy for offline development if needed.
//Define('QCUBED_FONT_AWESOME_CSS', QCUBED_PROJECT_CSS_URL . '/font-awesome.css');
//define('QCUBED_FONT_AWESOME_CSS', 'https://opensource.keycdn.com/fontawesome/4.7.0/font-awesome.min.css');
const QCUBED_FONT_AWESOME_CSS = QCUBED_PROJECT_CSS_URL . '/font-awesome.min.css';

// Location of the QCubed-specific web-based development tools, like start_page.php
const QCUBED_APP_TOOLS_URL = QCUBED_BASE_URL . '/application/tools';
