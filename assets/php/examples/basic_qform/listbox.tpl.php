<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

    <div id="instructions">
        <h1>The ListControl Family of Controls</h1>

        <p><strong>\QCubed\Control\ListControl</strong> controls handle simple lists of objects which can be selected.  In its most
            basic form, we are talking about HTML listboxes (e.g. &lt;select&gt;) with name/value
            pairs (e.g. &lt;option&gt;).</p>

        <p>Of course, list boxes can be single- and multi-select. But keep in mind that sometimes you may want to display that
            list as a list of labeled checkboxes (which essentially works like a multi-select list box) or as a list of labeled
            radio buttons (which works like a single-select list box). QCubed-4 includes the <strong>ListBox</strong>, <strong>CheckboxList</strong>,
            and <strong>RadioButtonList</strong> controls, all of which inherited from ListControl to allow you to present
            the data and functionality you need in the most user-friendly way.</p>

        <p>In this example, we create a <strong>ListBox</strong> and <strong>Checkbox</strong> control.  They pull their data
            from the <strong>Person</strong> table in the database.  Also, if you select a person, we will update the
            <strong>lblMessage</strong> label to show what you have selected.</p>

        <p>If you do a <strong>View Source...</strong> in your browser to view the HTML,
            you'll note that the <strong>value</strong> attributes in the &lt;option&gt; tags are indexes (starting with 0)
            and not the values assigned in the PHP code.  This is done intentionally as a security measure to prevent database
            indexes from being sent to the browser, and to allow for non-string-based values, or even duplicate values.
            You can look up specific values in the <strong>ListControl</strong> by using the <strong>SelectedValue</strong>
            attribute. You can also look up selected Names, Ids, and get the whole <strong>ListItem</strong>.</p>

        <h2>Projects and Their Members</h2>

        <p>The second list demonstrates how you can use <strong>optgroup</strong> labels to visually group related list items
            together in a <strong>ListBox</strong>. Here, we pull a list of <strong>projects</strong> from the database and
            show each project's <strong>team members</strong> as selectable items, grouped under their respective project
            names. This allows for better organization and a more intuitive interface when dealing with categorized data.</p>

        <p>Each list item still uses the person ID as the value, and when a selection is made, the corresponding
            name is displayed. You could easily extend this to show additional project or person details.</p>

    </div>

    <div id="demoZone">
        <p><label>List 1</label><?php $this->lstPersons->render(); ?></p>
        <p><label>List 2</label><?php $this->lstProjectPeople->render(); ?></p>
        <p><label>List 3</label><?php $this->chkPersons->render(); ?></p>
        <p>Recently Selected: <?php $this->lblMessage->render(); ?></p>
    </div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>