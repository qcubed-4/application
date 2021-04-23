<?php require(QCUBED_EXAMPLES_DIR . '/includes/examples.inc.php'); ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="<?php _p(\QCubed\Project\Application::encodingType()); ?>" />
		<title><?php _p(Examples::PageName(), false); ?> - QCubed PHP 5 Development Framework - Examples</title>
		<link rel="stylesheet" type="text/css" href="<?php _p(QCUBED_CSS_URL . '/qcubed.css', false); ?>"></link>
		<link rel="stylesheet" type="text/css" href="<?php _p(QCUBED_EXAMPLES_URL . '/includes/examples.css', false); ?>"></link>
	</head>
	<body>
		<header>
			<div class="breadcrumb">
<?php		if(!isset($mainPage) && is_numeric(Examples::GetCategoryId())) { ?>
				<span class="category-name"><?php _p((Examples::GetCategoryId() + 1) . '. ' . Examples::$Categories[Examples::GetCategoryId()]['name'], false); ?></span> / 
<?php		} ?>
				<strong class="page-name"><?php _p(Examples::PageName(), false); ?></strong>
			</div>
			
<?php	if(!isset($mainPage)) { ?>
			<nav class="page-links"><?php _p(Examples::PageLinks(), false); ?></nav>
<?php	} ?>
			
		</header>
		<section id="content">