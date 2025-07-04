<?php

use QCubed\Database\Service;
use QCubed\QString;
use QCubed\Type;

require_once('./qcubed.inc.php');

    //Exit gracefully if called directly or profiling data is missing.
    if (!isset($_POST['intDatabaseIndex']) && !isset($_POST['strProfileData']) && !isset($_POST['strReferrer'])) {
        exit('Nothing to profile. No Database Profiling data received.');
    }

    if (!isset($_POST['intDatabaseIndex']) || !isset($_POST['strProfileData']) || !isset($_POST['strReferrer'])) {
        throw new Exception('Database Profiling data appears to have been corrupted.');
    }

    $intDatabaseIndex = intval($_POST['intDatabaseIndex']);
    $strReferrer = QString::htmlEntities($_POST['strReferrer']);

    $objProfileArray = unserialize(base64_decode($_POST['strProfileData']));
    $objProfileArray = Type::cast($objProfileArray, Type::ARRAY_TYPE);
    $intCount = count($objProfileArray);

/**
 * Outputs the result of an SQL EXPLAIN statement in an HTML table format for the given query.
 *
 * @param string $strOriginalQuery The original SQL query string to be explained.
 * @return string|null The explanation of the query in HTML table format, or null if the query contains "AUTOCOMMIT=1".
 */
function printExplainStatement(string $strOriginalQuery): ?string
    {
        global $intDatabaseIndex;
        if (substr_count($strOriginalQuery, "AUTOCOMMIT=1") > 0) {
            return null;
        }
        $result = "";
        
        $objDb = Service::getDatabase($intDatabaseIndex);
        $objDbResult = $objDb->explainStatement($strOriginalQuery);
        if (!$objDbResult) {
            return "";
        }
        
        $result .= "<table class='explainTable' border=1>";
        $headersShown = false;
        while ($mixRow = $objDbResult->fetchArray()) {
            if (!$headersShown) {
                $result .= "<thead class='header'>";
                foreach ($mixRow as $key=>$value) {
                    if (!is_numeric($key)) {
                        $result .= "<td>" . $key . "</td>";
                        $headersShown = true;
                    }
                }
                $result .= '</thead>';
            }
            $result .= "<tr>";
            foreach ($mixRow as $key=>$value) {
                if (!is_numeric($key)) {
                    $result .= "<td>" . $value . "</td>";
                }
            }
            $result .= "</tr>";
        }
        $result .= "</table>";
        return $result;
    }
    
    $strJsFileArray = explode(",", QCUBED_JQUERY_JS);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>QCubed Development Framework - Database Profiling Tool</title>
    <link rel="stylesheet" type="text/css" href="<?php _p(QCUBED_BASE_URL . "/application/assets/css/corepage.css", false); ?>" />
<?php
    foreach ($strJsFileArray as $strJsFile) {
        if (str_contains($strJsFile, "http")) {
            ?>
	<script type="text/javascript" src="<?= $strJsFile ?>"></script>
<?php

        } else {
            $strSlash = '';
            if (!str_starts_with($strJsFile, "/")) {
                $strSlash = '/';
            } ?>
	<script type="text/javascript" src="<?php _p(QCUBED_JS_URL . $strSlash . $strJsFile); ?>"></script>
<?php

        }
    }
?>
	<script type="text/javascript">
		function toggle(strWhatId) {
            const obj = document.getElementById(strWhatId);
            const objButton = document.getElementById("button" + strWhatId);

            if (obj && objButton) {
				if (obj["style"].display === "block") {
					obj["style"].display = "none";
					objButton.innerHTML = objButton.innerHTML.replace("Hide", "Show");
				}
				
				else {
					obj["style"].display = "block";
					objButton.innerHTML = objButton.innerHTML.replace("Show", "Hide");
				}
			}
			return false;
		}

		function showAll() {
			jQuery(".querySection, .explainSection").each(function() {
				if ($(this).css('display') === "none") {
					toggle(this.id);
				}
			});

			return false;
		}

		function hideAll() {
			jQuery(".querySection, .explainSection").each(function() {
				if ($(this).css('display') === "block") {
					toggle(this.id);
				}
			});
			return false;
		}
	</script>
	<style>
		.explainTable {
			border: 1px solid black; 
			border-collapse:collapse;
			margin-top: 5px;
		}
		.explainTable td {
			padding: 4px;
		}
		.explainTable .header td {
			background: #CCC;
			font-weight: bold;
		}
	</style>
</head>
<body>
	<div id="container">
		<div id="headerContainer">
			<div id="headerBorder">
				<div id="header">
					<div id="hleft">
						<span class="hsmall">QCubed Development Framework <?= QCUBED_VERSION_NUMBER_ONLY ?></span><br/>
						<span class="hbig">Database Profiling Tool</span>
					</div>
					<div id="hright">
						<b>Database Index:</b> <?php _p($intDatabaseIndex); ?>&nbsp;&nbsp;
						<b>Database Type:</b> <?php _p(Service::getDatabase($intDatabaseIndex)->Adapter); ?><br/>
						<b>Database Server:</b> <?php _p(Service::getDatabase($intDatabaseIndex)->Server); ?>&nbsp;&nbsp;
						<b>Database Name:</b> <?php _p(Service::getDatabase($intDatabaseIndex)->Database); ?><br/>
						<b>Profile Generated From:</b> <?php _p($strReferrer, false); ?>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	</div>

	<div id="content">
		<span class="title">
<?php
        switch ($intCount) {
            case 0: _p('<b>There were no queries that were performed.</b>', false); break;
            case 1: _p('<b>There was 1 query that was performed.</b>', false); break;
            default: printf('<b>There were %s queries that were performed.</b>', $intCount); break;
        }
?>
		</span>
		<br/>
		<br/>
		<a href="#" onClick="return showAll();" class="smallbutton">Show All</a>
		<a href="#" onClick="return hideAll();" class="smallbutton">Hide All</a>
		<br/>
		<br/>
<?php
        $intIndex = 1;

        foreach ($objProfileArray as $objProfile) {
            $objBacktrace = $objProfile['objBacktrace'];
            $strQuery = $objProfile['strQuery'];
            $dblTimeInfo = $objProfile['dblTimeInfo'];

            $objArgs = (array_key_exists('args', $objBacktrace)) ? $objBacktrace['args'] : array();
            $strClass = (array_key_exists('class', $objBacktrace)) ? $objBacktrace['class'] : null;
            $strType = (array_key_exists('type', $objBacktrace)) ? $objBacktrace['type'] : null;
            $strFunction = (array_key_exists('function', $objBacktrace)) ? $objBacktrace['function'] : null;
            $strFile = (array_key_exists('file', $objBacktrace)) ? $objBacktrace['file'] : null;
            $strLine = (array_key_exists('line', $objBacktrace)) ? $objBacktrace['line'] : null; ?>
			<span class="function">
				Called by <?php _p($strClass . $strType . $strFunction . '(' . implode(', ', $objArgs) . ')'); ?>
			</span>&nbsp;&nbsp;<br/>
			<span class="function_details">
				<b>File: </b><?php _p($strFile); ?>; &nbsp;&nbsp;<b>Line: </b><?php _p($strLine); ?>
			</span>
<?php
            //mark slow query - those that take over 1 second
            if ($dblTimeInfo >= 1) {
                echo('<div class="time slow">');
            } else {
                echo'<div class="time">';
            }
            printf("Query took %.1f ms", $dblTimeInfo * 1000); ?>
			<?php $explainStatement = PrintExplainStatement($strQuery); ?>
			<a href="#" onClick="return toggle('query<?php _p($intIndex); ?>')" id="buttonquery<?php _p($intIndex); ?>" class="queryButton smallbutton">
				Show SQL
			</a>
			<?php if ($explainStatement) {
                ?>
			&nbsp;&nbsp;
			<a href="#" onClick="return toggle('explain<?php _p($intIndex); ?>')" id="buttonexplain<?php _p($intIndex); ?>" class="explainButton smallbutton">
				Show EXPLAIN statement
			</a>
			<?php 
            } ?>
			
			<pre id="query<?php _p($intIndex); ?>" style="display: none" class="querySection"><code><?php _p($strQuery); ?></code></pre>
			<div id="explain<?php _p($intIndex); ?>" style="display: none" class="explainSection"><?= $explainStatement; ?></div>
<?php	
            echo '</div>'; ?>
			<br/>
<?php
            $intIndex++;
        }
?>  </div>

</body>
</html>