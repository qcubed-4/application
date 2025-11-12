<?php
    // ADD TO THE BEGINNING OF THE FILE NOW! Before outputting any HTML!

    // Cache and freshness check
    header('Cache-Control: no-cache, no-store, must-revalidate'); // No caching allowed
    header('Pragma: no-cache'); // Backward compatibility with older proxies/browsers
    header('Expires: 0'); // Page expires immediately

    // Security headers
    header('X-Content-Type-Options: nosniff'); // Disable content-type sniffing
    header('X-Frame-Options: SAMEORIGIN'); // The page can only be loaded in an iframe from the same domain
    header('Referrer-Policy: strict-origin-when-cross-origin'); // Share reference information more limitedly
    header('X-XSS-Protection: 1; mode=block'); // Activate XSS protection (older browsers)

    // (Optional) Want even higher security?
    // header('Content-Security-Policy: default-src \'self\'');

    use QCubed\Project\Application;

    require(QCUBED_EXAMPLES_DIR . '/includes/examples.inc.php'); ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="<?php _p(Application::encodingType()); ?>" />
    <title><?php _p(Examples::PageName(), false); ?> - QCubed PHP 8.3+ Development Framework - Examples</title>
    <link rel="stylesheet" type="text/css" href="<?php _p(QCUBED_CSS_URL . '/qcubed.css', false); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php _p(QCUBED_CSS_URL . '/waiticon-spinner.css', false); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php _p(QCUBED_CSS_URL . '/jquery-ui.min.css', false); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php _p(QCUBED_EXAMPLES_URL . '/includes/examples.css', false); ?>" />
</head>
<body>
<header>
    <div class="breadcrumb">
        <?php if(!isset($mainPage) && is_numeric(Examples::GetCategoryId())) { ?>
            <span class="category-name"><?php _p((Examples::GetCategoryId() + 1) . '. ' . Examples::$Categories[Examples::GetCategoryId()]['name'], false); ?></span> /
        <?php } ?>
        <strong class="page-name"><?php _p(Examples::PageName(), false); ?></strong>
    </div>
    <?php if(!isset($mainPage)) { ?>
        <nav class="page-links"><?php _p(Examples::PageLinks(), false); ?></nav>
    <?php } ?>

</header>
<section id="content">