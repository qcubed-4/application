<?php require_once('../qcubed.inc.php'); ?>
<?php require('../includes/header.inc.php'); ?>

<div id="instructions">
    <h1>Integrating Forms and the Code Generator</h1>

    <p>When you code generate your objects, QCubed-4 will actually provide a starting
        point for this integration in the generated <strong>Drafts</strong>.  These generated
        scripts are definitely <i>drafts</i> or starting points from which you can create
        more elaborate, useful and functional <strong>Forms</strong> or <strong>Panels</strong> for your application.</p>

    <p>At a high level, this concept is very similar to the <strong>scaffolding</strong> which
        is provided by many other frameworks.  But note that because of the object-oriented
        approach of the <strong>Model Connectors</strong> and <strong>DataGrid Connectors</strong>, these <strong>Drafts</strong> can offer much more
        power and functionality over <strong>scaffolding</strong>.</p>
</div>

<div id="demoZone">
    To view one of the generated <strong>Forms</strong>, please a click here to
    view the <strong><a href="<?php _p(QCUBED_FORMS_URL); ?>/person_list.php" class="bodyLink">Person List</a></strong> page (available only if you are running examples locally).
</div>

<?php require('../includes/footer.inc.php'); ?>
