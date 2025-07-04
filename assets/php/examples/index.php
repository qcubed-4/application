<?php
use QCubed\Project\Application;

require_once('qcubed.inc.php');
// show the first section by default
$intSectionToShow = (!Application::instance()->context()->pathItem(0)) ? 1 : Application::instance()->context()->pathItem(0);
// Used to distinguish the home page in the header
$mainPage = true;
require('includes/header.inc.php');
?>
<div id="instructions" class="full">
	<h1>QCubed-4 Examples Site</h1>

	<p>This is a collection of many small examples that demonstrate the functionality
		in QCubed-4.  Later examples tend to build upon functionality or concepts that are
		discussed in prior ones, which allows the Examples site to be viewed as a quasi-tutorial.
		However, you should still feel free to check out any of the examples as you wish.</p>

	<p>The Examples are broken into three main parts: the <strong>Code Generator</strong>, the <strong>Forms and Control Library</strong>, and
		<strong>Other QCubed-4 Functionality</strong>.</p>

	<p class="bodySmall">* Some of the examples (marked with a "*") use the <strong>Example Site Database</strong>.
		This database (which consists of six tables and some preloaded sample data) is included in the Example Site directories.  See
		<a href="<?php _p(QCUBED_EXAMPLES_URL); ?>/code_generator/intro.php" class="bodyLink" style="font-weight: bold;">Basic CodeGen &gt; About the Database</a>
		for more information.</p>
</div>

<div class="main-navigator ui-widget ui-corner-all">
	<a id="link1" href="<?php _p(Application::instance()->context()->scriptName()) ?>" onclick="return DisplayPart('1')" class="<?php _p(($intSectionToShow == 1) ? "selected" : "nav-link"); ?>">The Code Generator</a>
	&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
	<a id="link2" href="<?php _p(Application::instance()->context()->scriptName()) ?>/2" onclick="return DisplayPart('2')" class="<?php _p(($intSectionToShow == 2) ? "selected" : "nav-link"); ?>">The Forms and Control Library</a>
	&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
	<a id="link3" href="<?php _p(Application::instance()->context()->scriptName()) ?>/3" onclick="return DisplayPart('3')" class="<?php _p(($intSectionToShow == 3) ? "selected" : "nav-link"); ?>">Other QCubed-4 Functionality</a>
</div>

<?php
for ($intIndex = 0; $intIndex < count(Examples::$Categories); $intIndex++) {
	$objExampleCategory = Examples::$Categories[$intIndex];

	if ($intIndex == 0) {
?>
	<div id="part1" <?php if ($intSectionToShow != 1) { _p('style="display: none;"', false); } ?>>
		<div class="main-info">
			<p><strong>The Code Generator</strong> is at the heart of the Model in the MVC (Model, View, Controller) architecture.
				It uses the data model you have defined to create all your data objects, relationships and <abbr title="Create, Restore, Update, Delete">CRUD</abbr> functionality.</p>

			<p>Sections 1–3 look specifically at the <strong>Code Generator</strong>, the <strong>Object Relational Model</strong> it creates, and the
				<strong>QCubed-4 Query</strong> library which powers it.</p>
		</div>
		<blockquote>
<?php
	}

	if ($intIndex == 3) {
?>
		</blockquote>
	</div>
	<div id="part2" <?php if ($intSectionToShow != 2) { _p('style="display: none;"', false); } ?>>
		<div class="main-info">
			<p><em>QForms</em> is a <strong>stateful, event-driven architecture for web-based forms</strong>, providing the display and
				presentation functionality for QCubed-4.  Basically, it is your "V" and "C" of the MVC architecture.</p>

			<p>Sections 4–10 are examples on how to use <strong>Forms</strong> and the <strong>Control</strong> libraries
				within the QCubed-4 Development Framework.</p>
		</div>
		<blockquote>
<?php
	}

	if ($intIndex == 10) {
?>
		</blockquote>
	</div>
	<div id="part3" <?php if ($intSectionToShow != 3) { _p('style="display: none;"', false); } ?>>
		<div class="main-info">
			<p>Beyond the <strong>Code Generator</strong> and the <strong>Forms</strong> library, QCubed-4 also has many other modules and features
				that are useful for web application developers.</p>
		</div>
		<blockquote>
<?php
	}

	printf('<p>%s. <b>%s</b> - %s</p><ul class="link-list">', ($intIndex + 1), $objExampleCategory['name'], $objExampleCategory['description']);

	foreach ($objExampleCategory as $strKey => $strValue) {
		if (is_numeric($strKey)) {
			$intPosition = strpos($strValue, ' ');
			printf('<li><a href="%s">%s</a></li>', substr($strValue, 0, $intPosition), substr($strValue, $intPosition + 1));
		}
	}

	_p('</ul>', false);
}
?>
	</blockquote>
</div>

<script type="text/javascript">
	/**
	 * Toggle the sections in the navigator.
	 * @TODO this should be UI tabs or accordion.
	 */
	function DisplayPart(strPartId) {
        const selectedClass = "selected",
            regularClass = "nav-link";

        switch (strPartId) {
			case "1":
				document.getElementById("part1").style.display = "block";
				document.getElementById("part2").style.display = "none";
				document.getElementById("part3").style.display = "none";

				document.getElementById("link1").className = selectedClass;
				document.getElementById("link2").className = regularClass;
				document.getElementById("link3").className = regularClass;
				break;
			case "2":
				document.getElementById("part1").style.display = "none";
				document.getElementById("part2").style.display = "block";
				document.getElementById("part3").style.display = "none";

				document.getElementById("link1").className = regularClass;
				document.getElementById("link2").className = selectedClass;
				document.getElementById("link3").className = regularClass;
				break;
			case "3":
				document.getElementById("part1").style.display = "none";
				document.getElementById("part2").style.display = "none";
				document.getElementById("part3").style.display = "block";

				document.getElementById("link1").className = regularClass;
				document.getElementById("link2").className = regularClass;
				document.getElementById("link3").className = selectedClass;
				break;
		}
		return false;
	}
</script>

<?php require('includes/footer.inc.php'); ?>