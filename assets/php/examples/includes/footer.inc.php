<?php
	// The url to send to view_source.php
	// first encode the basic info
	$strCatId = Examples::GetCategoryId();
	$strUrl = QCUBED_EXAMPLES_URL .
		'/view_source.php/' .
		$strCatId . '/' .
		Examples::GetExampleId();

	if ($strCatId == "plugin") {
		$strFile = Examples::GetPluginFile();
		$strUrl .= '/' . $strFile . '/' . $strFile;
	} else {
		$strUrl .= '/' . basename(\QCubed\Project\Application::instance()->context()->scriptName());
	}
?>
<?php	if(!isset($mainPage)) { ?>
			<button id="viewSource">View Source</button>
<?php	} ?>
		</section>
		<footer>
			<div id="tagline"><a href="http://qcubed.github.com/" title="QCubed Homepage"><img id="logo" src="<?php _p(QCUBED_IMAGE_URL . '/qcubed_logo_footer.png', false); ?>" alt="QCubed Framework" /> <span class="version"><?php _p(QCUBED_VERSION); ?></span></a></div>
		</footer>
		
		<script type="text/javascript">
			var viewSource = document.getElementById('viewSource');
			if (viewSource) {
				viewSource.onclick = function (){
					objWindow = window.open("<?= $strUrl ?>", "ViewSource", "menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes,width=1000,height=750,left=50,top=50");
					objWindow.focus();
					return false;
				};
			}			
			window.gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
			document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));

			try {
				window.pageTracker = _gat._getTracker("UA-7231795-1");
				pageTracker._trackPageview();
			} catch(err) {}
		</script>
	</body>
</html>