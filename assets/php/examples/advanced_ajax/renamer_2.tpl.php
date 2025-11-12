<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
	<h1>More "J" and Less "A" in AJAX</h1>

	<p>Because our Rename able Labels make full use of <strong>Ajax</strong> actions, any clicking (including
		just selecting a label) involves an asynchronous server hit.</p>

	<p>Of course, by having all your functionality and display logic in one place, we show
		how you can quickly and rapidly develop Ajax interactions with very little PHP code,
		and in fact, with <em>no</em> custom JavaScript whatsoever. This allows developers
		the ability to rapidly prototype not just web-based
		 applications but also web-based applications with full Ajax functionality.</p>

	<p>But as your application matures, you may want to have some fully server-side Ajax functionality to
		be converted into more performance-efficient client-side-only JavaScript functionality.
		This example shows how you can easily change an existing <strong>Form</strong> that uses all QCubed-based Ajax
		interactions into a more blended server- and client-side JavaScript/Ajax form. Because the API for
        <strong>Server</strong> actions, <strong>JavaScript</strong> actions, and <strong>Ajax</strong> actions are all the same,
        the process of rewriting specific functionality nuggets is simple,
        and the types of actions (from Ajax to JavaScript to server) should be very interchangeable.
    </p>

</div>

<div id="demoZone">
	<?php for ($intIndex = 0; $intIndex < 10; $intIndex++) { ?>
		<p style="height: 16px;">
			<?php $this->lblArray[$intIndex]->render(); ?>
			<?php $this->txtArray[$intIndex]->render(); ?>
		</p>
	<?php } ?>
</div>

    <script type="text/javascript">
        let intSelectedIndex = -1;
        let objSelectedLabel;

        function lblArray_Click(objControl) {
            let strControlId = objControl.id,
                //intIndex = strControlId.substr(5),
                intIndex = strControlId.substring(5),

            objTextbox;

            // Is the Label being clicked already selected?
            if (intSelectedIndex === intIndex) {
                // It's already selected -- go ahead and replace it with the textbox
                qc.getW(strControlId).toggleDisplay('hide');
                qc.getW('textbox' + intIndex).toggleDisplay('show');

                objTextbox = qcubed.getControl('textbox' + intIndex);
                objTextbox.value = objControl.innerHTML;
                objTextbox.focus();
                objTextbox.select();
            } else {
                // Nope -- not yet selected

                // First, unselect everything else
                if (objSelectedLabel){
                    objSelectedLabel.className = 'renamer_item';
                }
                // Now, make this item selected
                objControl.className = 'renamer_item renamer_item_selected';
                objSelectedLabel = objControl;
                intSelectedIndex = intIndex;
            }
        }
    </script>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>