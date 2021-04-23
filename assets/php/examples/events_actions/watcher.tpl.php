<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

    <div id="instructions">
        <h1>Automatic Refreshing and the Watcher Classes</h1>

        <p>The <strong>Watcher</strong> class is used to connect a control to a database table or tables so that
            whenever that database changes, the control automatically refreshes. This can save you from having to
            setup callbacks between edit forms and dialogs in order to refresh a control that is viewing data. In
            addition,
            in a multi-user environment, when one user changes the data, the other user will automatically see the
            change. This is similar to what you might see in a system like NodeJS, with some caveats</p>

        <p>The current implementation requires the browser to generate either an Ajax event or Server event in order to
            detect the change. In a multi-user environment, it the user is actively using your application, this should
            happen pretty often. However, if your application is such that the user might have long-periods of
            inactivity, but still should see the results of activity from other users, you can do a couple of things:
        <ul>
            <li>
                Set up a <strong>JsTimer</strong> to generate periodic events. See the <a href="../other_controls/timer_js.php">JsTimer
                    example page</a> for help. In that
                example page, it discusses adding actions to the timer. For purposes of generating opportunities for
                the Watcher to look at the database, you will add a null ajax action to the timer.
            </li>
            <li>
                The other option, which is currently not implemented in QCubed, is create a direct connection between
                the
                server and the user's browser that will trigger these events. There are a few different technologies to
                do this,
                and many require a customized html server. Apache will not do this out of the box. A messaging server
                like PubNub may be
                the best candidate for this.
            </li>
        </ul>
        </p>

        <p>To make a watcher work, you must edit
            the <strong>/project/qcubed/Watcher/Watcher.php</strong> file so that the Watcher class inherits from
            the watcher type you want. Available types currently let you use a database to track changes, or
            use a CacheProvider subclass.</p>

        <p>This is another <strong>Datagrid</strong> example with a couple of fields to add a new person.
            Whenever you add a person, the person will appear in the datagrid immediately.
            It also has a timer to generate periodic events that will check whether another user has changed the
            database.
            Try opening the page in another browser on your computer to simulate a multi-user environment.
            Whenever you add data to one browser, it will appear in the other browser.</p>


    </div>

    <div id="demoZone">
        <?php $this->dtgPersons->render(); ?>
        <p>First:<?php $this->txtFirstName->render(); ?></p>
        <p>Last:<?php $this->txtLastName->render(); ?></p>
        <?php $this->btnNew->render(); ?>
    </div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>