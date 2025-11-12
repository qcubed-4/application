<?php
	require_once('qcubed.inc.php');
	require('includes/examples.inc.php');

	$strCategoryId = \QCubed\Project\Application::instance()->context()->pathItem(0);
	$strExampleId = \QCubed\Project\Application::instance()->context()->pathItem(1);
	if ($strCategoryId == 'plugin') {
		$strSubId = \QCubed\Project\Application::instance()->context()->pathItem(2);
		$strScript = \QCubed\Project\Application::instance()->context()->pathItem(3);
	} else {
		$strSubId = null;
		$strScript = \QCubed\Project\Application::instance()->context()->pathItem(2);
	}

	$strReference = Examples::GetExampleScriptPath($strCategoryId, $strExampleId);
	$strName = Examples::GetExampleName($strCategoryId, $strExampleId);


if (!$strScript) {
		$strUrl = \QCubed\Project\Application::instance()->context()->requestUri() . substr($strReference, strrpos($strReference, '/'));
		\QCubed\Project\Application::Redirect($strUrl, true);
	}
?>
<html>
	<head>
		<meta charset="<?php _p(\QCubed\Project\Application::encodingType()); ?>" />
		<title>QCubed PHP 5 Development Framework - View Source</title>
		<link rel="stylesheet" type="text/css" href="<?php _p(QCUBED_CSS_URL . '/qcubed.css'); ?>" />
		<link rel="stylesheet" type="text/css" href="<?php _p(QCUBED_EXAMPLES_URL . '/includes/examples.css'); ?>" />
	</head>
	<body>
		<div id="closeWindow"><a href="javascript:window.close()" class="close-window">Close this Window</a></div>
		<header><nav class="page-links"><span class="headerSmall"><?php _p(Examples::CodeLinks($strCategoryId, $strExampleId, $strSubId, $strScript), false); ?></nav></header>
		<section id="content">
<?php
	// Filename Cleanup
	if (($strScript == 'header.inc.php') || ($strScript == 'footer.inc.php') || ($strScript == 'examples.css'))
		$strFilename = 'includes/' . $strScript;
	else if (($strScript == 'mysql_innodb.sql') || ($strScript == 'sql_server.sql') || ($strScript == 'pgsql.sql')) {
		$strFilename = $strScript;
	}
	elseif ($strScript[0] == '\\') {    // a fully qualified class name
        $strFilename = \QCubed\AutoloaderService::instance()->findFile($strScript);
    } else {
	    // convert a url to a dir
		$strFilename = QCUBED_EXAMPLES_DIR . substr($strReference, strlen(QCUBED_EXAMPLES_URL));
		// substitute actual script name, since it may be a related script
        $strFilename = substr($strFilename, 0, strrpos ($strFilename, '/') + 1);
        $strFilename .= $strScript;
	}

	if (!file_exists($strFilename)) {
		throw new Exception("Example file does not exist: " . $strFilename);
	}
?>
			<h1>Source of: <?php _p(preg_replace('/__.*__/', '', $strScript)); ?></h1>
			<pre><?php highlight_file($strFilename); ?></pre>
		</section>
	</body>
</html>