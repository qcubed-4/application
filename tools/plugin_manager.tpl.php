<?php
	$strPageTitle = "QCubed Libraries";
    require(QCUBED_CONFIG_DIR . '/header.inc.php');
?>
	<h1><?php _t('Libraries'); ?></h1>
		<?php $this->renderBegin() ?>
	<p> QCubed uses Composer to install libraries. To install library plugin, simply execute the 'composer require library_name' command on your command line.
	</p>
	<p>Below is a list of your currently installed libraries.</p>
	<?php $this->dtgPlugins->render(); ?>
	
	<hr />
	

	<?php $this->renderEnd() ?>
	
<?php require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>