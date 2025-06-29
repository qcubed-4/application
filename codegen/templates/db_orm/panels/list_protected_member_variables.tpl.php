<?php if (!isset($objTable->Options['CreateFilter']) || $objTable->Options['CreateFilter'] !== false) { ?>
    /** @var Panel **/
    protected Panel $pnlFilter;

    /** @var TextBox **/
    protected TextBox $txtFilter;
<?php } ?>

    /** @var Panel **/
    protected Panel $pnlButtons;

    /** @var Button **/
    protected Button $btnNew;

    /** @var <?= $strPropertyName ?>List **/
    protected <?= $strPropertyName ?>List $<?= $strListVarName ?>;

<?php if ($blnUseDialog) { ?>
    /** @var <?= $objTable->ClassName ?>EditDlg **/
    protected <?= $objTable->ClassName ?>EditDlg $dlgEdit;
<?php }