#!/usr/bin/env php
<?php
/**
 * MIT License
 *
 * Copyright (c) Shannon Pekary spekary@gmail.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * Process a substitution script from the gen_was command. Run this command to convert QCubed v3 code into v4 code.
 * Files are changed in place, so be careful! Make backups before running this.
 *
 * Use the all.regex.php file as the pattern file.
 *
 * -R to search a directory recursively.
 */

if (count($argv) < 3) {
    echo 'Usage: run_was [-R] patternFile outFile(s)';
}

$options = getopt('R');
$blnRecursive = isset($options['R']);

$files = $argv;
array_shift($files);
for ($i = 0; $i < count($options); $i++) {
    array_shift($files);
}

$patternFile = array_shift($files);
if (!file_exists($patternFile)) {
    echo "patternFile " . $patternFile . " not found";
    return -1;
}
$patterns = include($patternFile);
$regexFind = [];
$regexReplace = [];

if (isset($patterns['func'])) {
    $find = array_map(function ($was) {
        return '/' . addslashes($was) .
            '\s*\(/i';
    }, array_keys($patterns['func']));

    $replace = array_map(function ($newName) {
        $slashedName = addslashes($newName);
        return $slashedName . '(';
    }, array_values($patterns['func']));

    $regexFind = array_merge($regexFind, $find);
    $regexReplace = array_merge($regexReplace, $replace);
}

// straight regex replacement
if (isset($patterns['regex'])) {
    $find = array_map(function ($was) {
        return '/' . $was .
            '/i';
    }, array_keys($patterns['regex']));

    $regexFind = array_merge($regexFind, $find);
    $regexReplace = array_merge($regexReplace,  array_values($patterns['regex']));
}

/**
 * Here we try to detect a class that could be like this:
 *  QControl, or
 *  \QControl, or
 *   \QCubed\QControl
 *
 * Classes must be processed before consts.
 */
if (isset($patterns['class'])) {
    $find = array_map(function ($was) {
        $pattern = <<<'PTRN'
/(\W)(?:(?:\\\w+)*\\)*
PTRN;
        $pattern .= $was;
        $pattern .= <<<'PTRN'
(?:\b)/i
PTRN;
        return $pattern;
    }, array_keys($patterns['class']));

    $replace = array_map(function ($className) {
        $slashedClassName = addslashes($className);
        return '$1' . $slashedClassName;
    }, array_values($patterns['class']));

    $regexFind = array_merge($regexFind, $find);
    $regexReplace = array_merge($regexReplace, $replace);
}


if (isset($patterns['const'])) {
    $find = array_map(function ($was) {
        return '/' . addslashes($was) .
            '([^a-zA-Z0-9\\(])/i';
    }, array_keys($patterns['const']));

    $replace = array_map(function ($newName) {
        $slashedName = addslashes($newName);
        return $slashedName . '$1';
    }, array_values($patterns['const']));

    $regexFind = array_merge($regexFind, $find);
    $regexReplace = array_merge($regexReplace, $replace);
}




function processFile($file, $regexFind, $regexReplace)
{
    global $patterns;

    $strFile = file_get_contents($file);
    //var_export($regexFind); return;
    //var_export($regexReplace); return;
    $newFile = preg_replace($regexFind, $regexReplace, $strFile);
//echo $newFile; return;
    file_put_contents($file, $newFile);

    if (isset($patterns['warn'])) {
        foreach ($patterns['warn'] as $pat=>$msg) {
            if (preg_match('/' . $pat . '/i', $strFile)) {
                echo "File: ";
                echo $file;
                echo " - ";
                echo $msg;
                echo "\n";
            }
        }
    }
}

function processFiles($files, $regexFind, $regexReplace)
{
    global $blnRecursive;

    foreach ($files as $file) {
        if (!file_exists($file)) {
            echo "outFile " . $file . " not found";
            return -1;
        }
        if (is_dir($file)) {
            if ($file != '.' &&
                $file != '..' &&
                $blnRecursive
            ) {
                $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($file));
                $filter = new RegexIterator($objects, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
                foreach ($filter as $name => $object) {
                    processFile($name, $regexFind, $regexReplace);
                }
            }
        } else {
            processFile($file, $regexFind, $regexReplace);
        }
    }
}

processFiles($files, $regexFind, $regexReplace);

