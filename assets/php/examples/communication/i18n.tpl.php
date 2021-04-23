<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>Internationalization and Translation</h1>
    <p><QCubed offers internationalization support via its I18n library.</p>
	<p>
        See the I18N library ReadMe for details on how to set it up, but it basically involves creating a
        translator, pointing the translator to the translation files, and setting the language settings.
	</p>

	<p>
        The translation library has a few different modes of operation, but one popular translation file
        format it can read is the GNU .PO file format found at
		<a href="http://www.gnu.org/software/gettext/manual/html_node/gettext_9.html" class="bodyLink">http://www.gnu.org/software/gettext/manual/html_node/gettext_9.html</a>
	</p>

	<p>To translate any piece of text, simply use <b>t(xxx)</b>. Or as a shortcut,
		if you want to do a PHP <b>print()</b> of any translated text in your template, you can use
		the QCubed printing shortcut <b>_t(xxx)</b> -- this does the equivalent of
		<b>print(t(xxx))</b>.
	</p>

	<p>Note that generated Forms and the Controls are all I18n aware -- they will translate themselves
		based on the selected language (as long as the appropriate language file exists).  QCubed-specific
		langauge files are part of QCubed core, and exist in the i18n directories of their respective libraries.
        If you are able to contribute, please take
		the current en.po file and translate it to any currently unsupported language and feel free to
		submit it.
	</p>
</div>

<div id="demoZone">
	<h2><?php _t('Internationalization Example'); ?></h2>
	<p>
		<?php _t('Current Language'); ?>: 
		<strong><?php _p(\QCubed\I18n\TranslationService::instance()->translator()->getLocale() ? \QCubed\I18n\TranslationService::instance()->translator()->getLocale() : 'none'); ?></strong>
	</p>

	<?php $this->btnEn->render('Text="' . t('Switch to') . ' en"'); ?>
	<?php $this->btnEs->render('Text="' . t('Switch to') . ' es"'); ?>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>
