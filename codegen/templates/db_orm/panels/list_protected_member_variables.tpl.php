<?php if (!isset($objTable->Options['CreateFilter']) || $objTable->Options['CreateFilter'] !== false) { ?>
	/** @var Panel **/
	protected $pnlFilter;

	/** @var TextBox **/
	protected $txtFilter;
<?php } ?>

	/** @var Panel **/
	protected $pnlButtons;

	/** @var Button **/
	protected $btnNew;

	/** @var <?= $strPropertyName ?>List **/
	protected $<?= $strListVarName ?>;

<?php if ($blnUseDialog) { ?>
	/** @var <?= $objTable->ClassName ?>EditDlg **/
	protected $dlgEdit;
<?php }