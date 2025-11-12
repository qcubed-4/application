<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

    <div id="instructions">
        <h1>Spinners (WaitIcon Update)</h1>
        <p>
            The <strong>WaitIcon</strong> class (also known as a "Spinner" or "Throbber") has been updated and now supports
            multiple modern types and configuration options.<br>
            <br>
            <strong>Available spinner types:</strong>
        </p>
        <ul>
            <li>
                <strong>default</strong> &ndash; A subtle, pastel-colored bar spinner with 12 lines, optimized for all backgrounds.<br>
                <em>This is the default.</em>
            </li>
            <li>
                <strong>classic</strong> &ndash; The well-known spinning ring (arc/loop) loader.
            </li>
            <li>
                <strong>ripple</strong> &ndash; Material Design-style double-ripple spinner (expanding circles).
            </li>
        </ul>
        <p>
            <strong>How to select spinner type and size:</strong>
        </p>
        <pre><code>
$waitIcon = new WaitIcon($this);
$waitIcon->SpinnerType = 'ripple';   // or 'classic', 'default'
$waitIcon->Width = '2em';            // or '24px', '3rem', etc.
$waitIcon->Height = '2em';           // defaults to '1.5em'
    </code></pre>
        <p>
            You can set a <strong>DefaultWaitIcon</strong> for your form; all Ajax actions will use it by default.<br>
            You can also set <code>null</code> if you want to hide the spinner for a specific Ajax action,
            or pass a different WaitIcon instance as needed.
        </p>
        <p>
            Make sure you also render your WaitIcon on the page.<br>
            <em>Note: Some examples use an artificial delay (e.g. <code>sleep()</code>) to make the spinner visible during the Ajax request.</em>
        </p>
        <p>
            <strong>Important:</strong> Don’t forget to include <code>waiticon-spinner.css</code> in your HTML header for proper display.
        </p>
        <p>
            <strong>Summary:</strong> The WaitIcon is now modern and flexible, with multiple display types and property-based sizing.
        </p>
    </div>

    <div id="demoZone">
        <p><?= _r($this->lblMessage); ?></p>
        <p><?= _r($this->btnButton); ?> <?= _r($this->btnButton2); ?></p>
        <p><?= _r($this->DefaultWaitIcon); ?></p>
    </div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>