<?php
if (!empty($_SESSION['HtmlReporterOutput'])) {
    echo '<h1>QCubed-4 Unit Tests - PHPUnit ' . \PHPUnit\Runner\Version::id() . '</h1>';
    echo $_SESSION['HtmlReporterOutput'];
}
