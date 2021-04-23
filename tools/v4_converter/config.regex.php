<?php
// Help to convert the old config defines to new ones
$a['regex']['__VIRTUAL_DIRECTORY__\\s*\\.\\s*__CSS_ASSETS__'] = 'QCUBED_CSS_URL';
$a['regex']['__VIRTUAL_DIRECTORY__\\s*\\.\\s*__JS_ASSETS__'] = 'QCUBED_JS_URL';
$a['regex']['__VIRTUAL_DIRECTORY__\\s*\\.\\s*__IMAGE_ASSETS__'] = 'QCUBED_IMAGE_URL';
$a['regex']['__VIRTUAL_DIRECTORY__\\s*\\.\\s*__PHP_ASSETS__'] = 'QCUBED_PHP_URL';
$a['regex']['__VIRTUAL_DIRECTORY__\\s*\\.\\s*__EXAMPLES__'] = 'QCUBED_EXAMPLES_URL';
$a['regex']['__VIRTUAL_DIRECTORY__\\s*\\.\\s*__FORMS__'] = 'QCUBED_FORMS_URL';

$a['regex']['__VIRTUAL_DIRECTORY__\\s*\\.\\s*__APP_JS_ASSETS__'] = 'QCUBED_PROJECT_JS_URL';
$a['regex']['__VIRTUAL_DIRECTORY__\\s*\\.\\s*__APP_CSS_ASSETS__'] = 'QCUBED_PROJECT_CSS_URL';
$a['regex']['__VIRTUAL_DIRECTORY__\\s*\\.\\s*__APP_IMAGE_ASSETS__'] = 'QCUBED_PROJECT_IMAGE_URL';
$a['regex']['__VIRTUAL_DIRECTORY__\\s*\\.\\s*__APP_PHP_ASSETS__'] = 'QCUBED_PROJECT_PHP_URL';

$a['regex']['__VIRTUAL_DIRECTORY__\\s*\\.\\s*__DEVTOOLS_ASSETS__'] = 'QCUBED_APP_TOOLS_URL';



$a['regex']['__DOCROOT__\\s*\\.\\s*__EXAMPLES__'] = 'QCUBED_EXAMPLES_DIR';
$a['regex']['__DOCROOT__\\s*\\.\\s*__FORMS__'] = 'QCUBED_FORMS_DIR';

$a['regex']['__VENDOR_ASSETS__'] = 'QCUBED_VENDOR_URL';
$a['regex']['__FORM_LIST_ITEMS_PER_PAGE__'] = 'QCUBED_ITEMS_PER_PAGE';
$a['regex']['__JQUERY_BASE__'] = 'QCUBED_JQUERY_JS';
$a['regex']['__JQUERY_EFFECTS__'] = 'QCUBED_JQUI_JS';
$a['regex']['__QCUBED_JS_CORE__'] = 'QCUBED_JS';
$a['regex']['__JQUERY_CSS__'] = 'QCUBED_JQUI_CSS';

$a['regex']['__PROJECT__'] = 'QCUBED_PROJECT_DIR';
$a['regex']['__INCLUDES__'] = 'QCUBED_PROJECT_INCLUDES_DIR';
$a['regex']['__CONFIGURATION__'] = 'QCUBED_CONFIG_DIR';
$a['regex']['__APP_INCLUDES__'] = 'QCUBED_APP_INCLUDES_DIR';
$a['regex']['__TMP__'] = 'QCUBED_TMP_DIR';
$a['regex']['__CACHE__'] = 'QCUBED_CACHE_DIR';
$a['regex']['__FILE_CACHE__'] = 'QCUBED_FILE_CACHE_DIR';

$a['regex']['ERROR_PAGE_PATH'] = 'QCUBED_ERROR_PAGE_PHP';

$a['regex']['__MODEL_CONNECTOR__'] = 'QCUBED_PROJECT_MODELCONNECTOR_DIR';
$a['regex']['__MODEL_CONNECTOR_GEN__'] = 'QCUBED_PROJECT_MODELCONNECTOR_GEN_DIR';
$a['regex']['__DIALOG__'] = 'QCUBED_PROJECT_DIALOG_DIR';
$a['regex']['__DIALOG_GEN__'] = 'QCUBED_PROJECT_DIALOG_GEN_DIR';
$a['regex']['__PANEL__'] = 'QCUBED_PROJECT_PANEL_DIR';
$a['regex']['__PANEL_GEN__'] = 'QCUBED_PROJECT_PANEL_GEN_DIR';

$a['regex']['__MODEL__'] = 'QCUBED_PROJECT_MODEL_DIR';
$a['regex']['__MODEL_GEN__'] = 'QCUBED_PROJECT_MODEL_GEN_DIR';

$a['regex']['__URL_REWRITE__'] = 'QCUBED_URL_REWRITE';
$a['regex']['__APPLICATION_ENCODING_TYPE__'] = 'QCUBED_ENCODING';

$a['regex']['__APP_CSS_ASSETS__'] = 'QCUBED_PROJECT_CSS_URL';
$a['regex']['__APP_IMAGE_ASSETS__'] = 'QCUBED_PROJECT_IMAGE_URL';
$a['regex']['__APP_JS_ASSETS__'] = 'QCUBED_PROJECT_JS_URL';

$a['regex']['__MINIMIZE__'] = 'QCUBED_MINIMIZE';
$a['regex']['__VIRTUAL_DIRECTORY__\\s*\\.\\s*__SUBDIRECTORY__'] = 'QCUBED_URL_PREFIX';

$a['regex']['__FONT_AWESOME__'] = 'QCUBED_FONT_AWESOME_CSS';

return $a;