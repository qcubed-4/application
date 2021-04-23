<?php
if (!empty($_SESSION['HtmlReporterOutput'])) {
    echo '<h1>QCubed Unit Tests - PHPUnit ' . \PHPUnit_Runner_Version::id() . '</h1>';
    echo $_SESSION['HtmlReporterOutput'];
}
