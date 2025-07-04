<?php

/**
 * Absolute File Paths for Internal Directories
 *
 * Please specify the absolute file path for all the following directories in your QCubed-based web
 * application.
 *
 * Note that all paths must start with a slash or 'x:\' (for Windows users) and must have
 * no ending slashes.  (We take advantage of the QCUBED_PROJECT_INCLUDES_DIR to help simplify this section.
 * But note that this is NOT required. These directories can also reside outside of the
 * Document Root altogether. So feel free to use or not use the DOC ROOT and QCUBED_PROJECT_INCLUDES_DIR
 * constants as you wish/need in defining your other directory constants.)
 */

const QCUBED_APP_INCLUDES_DIR = QCUBED_PROJECT_INCLUDES_DIR . '/app_includes';

// Browser-writable temporary directory for various frameworks generated files. Handle with care. Does not need to be in doc root.
const QCUBED_TMP_DIR = QCUBED_PROJECT_DIR . '/tmp';
const QCUBED_CACHE_DIR = QCUBED_TMP_DIR . '/cache';
const QCUBED_FILE_CACHE_DIR = QCUBED_TMP_DIR . '/cache';
const QCUBED_PLUGIN_TMP_DIR = QCUBED_TMP_DIR . '/plugin';
const QCUBED_PURIFIER_CACHE_DIR = QCUBED_CACHE_DIR . '/purifier';

/**
 * The absolute paths of the "upload" and "_files" folders as well as their URL paths are also added here.
 */

const APP_UPLOADS_URL = QCUBED_URL_PREFIX . '/project/assets/upload';
define ('APP_UPLOADS_DIR', $_SERVER['DOCUMENT_ROOT'] . APP_UPLOADS_URL);
const APP_UPLOADS_TEMP_URL = QCUBED_URL_PREFIX . '/project/tmp';
define ('APP_UPLOADS_TEMP_DIR', $_SERVER['DOCUMENT_ROOT'] . APP_UPLOADS_TEMP_URL);
