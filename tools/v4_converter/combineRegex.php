#!/usr/bin/env php
<?php
/**
 * Tool to combine all the generated search and replace files into one.
 *
 */

$dirQCubed= dirname(dirname(__DIR__));

$files = [
    __DIR__ . '/manual.regex.php',
    __DIR__ . '/app.regex.php',
    __DIR__ . '/project.regex.php',
    __DIR__ . '/generator.regex.php',
    __DIR__ . '/config.regex.php',
    $dirQCubed . '/common/tools/common.regex.php',
    $dirQCubed . '/orm/tools/orm.regex.php',
    $dirQCubed . '/orm/tools/manual.regex.php',

];

// declare possible top-level items here
$aNew = [
    'const'=>[],
    'class'=>[],
    'func'=>[],
    'regex'=>[],
    'warn'=>[]
];
foreach ($files as $file) {
    $a = include($file);
    foreach ($a as $key=>$items) {
        $aNew[$key] = array_merge($aNew[$key], $items);
    }
}

echo '<?php' . "\n" .
    "return ";

var_export($aNew);

echo  ";";


