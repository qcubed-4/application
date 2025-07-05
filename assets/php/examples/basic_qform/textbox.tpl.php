<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

    <div id="instructions">
        <h1>The TextBox Family of Controls</h1>

        <p><strong>TextBox</strong> controls handle basic user input. Different flavors of controls are available for various forms of user input, such as plain text, numbers, or data that requires strict formatting.</p>

        <p>The last controls, <strong>Email</strong>, <strong>URL</strong>, and <strong>Custom</strong>, are based on validation and filtering routines introduced in PHP 5.3. The custom field uses PHP's built-in ability to validate against a Perl regular expression to accept only hexadecimal numbers.</p>

        <h3>Multiple Email Entry and Flexible Separators</h3>

        <p>The <strong>MultipleEmailTextBox</strong> control extends the functionality of the standard EmailTextBox by allowing users to enter several email addresses in a single field. Addresses can be separated by a comma, semicolon, any whitespace (spaces, tabs, line breaks), or any combination of these. This gives users a great deal of flexibility and improves usability, especially when inviting or managing several recipients at once.</p>

        <p>When data is entered, the control automatically processes the text by splitting it into individual addresses using a regular expression. Each address is trimmed to remove extra spaces and then validated to check if it's a properly formatted email address. Addresses are sorted into two groups: those that are valid and those that are invalid.</p>

        <ul>
            <li><strong>Valid:</strong> Contains all recognized and correctly formatted email addresses.</li>
            <li><strong>Invalid:</strong> Contains all entries that fail validation and do not match the expected email format.</li>
        </ul>

        <p>Developers have easy access to these groups using the <code>getGroupedEmails()</code> method, which returns two arrays: one for all valid email addresses, and one for invalid ones. This enables custom handling, such as:</p>
        <ul>
            <li>Displaying tailored warning or error messages to users pointing out any addresses that need correction,</li>
            <li>Preventing form submission if invalid entries are present,</li>
            <li>Cleaning or preprocessing the list before saving it to your database or sending invitations.</li>
        </ul>

        <p><strong>Usage Example:</strong></p>
        <pre><code class="php">
// Suppose $textbox is an instance of MultipleEmailTextBox
$result = $textbox->getGroupedEmails();
$valid = $result['valid'];      // Array of valid email addresses
$invalid = $result['invalid'];  // Array of invalid email addresses
    </code></pre>

        <p>Thanks to MultipleEmailTextBox, accepting multiple e-mails is both user friendly and straightforward for developers to validate and process.</p>

        <h3>Other TextBox Variants</h3>
        <p>Alongside MultipleEmailTextBox, you can also use traditional controls for specific data types. For example, URLTextBox ensures only properly formatted web addresses, and CustomTextBox supports additional validation logic through regular expressions. Each variant builds on the base TextBox, but focuses validation and filtering on its respective format.</p>
    </div>

    <div id="demoZone">
        <p>Basic (limited to 5 chars): <?php $this->txtBasic->renderWithError(); ?></p>
        <p>Integer (max value of 10): <?php $this->txtInt->renderWithError(); ?></p>
        <p>Float: <?php $this->txtFlt->renderWithError(); ?></p>
        <p>List (2–5 comma-separated items): <?php $this->txtList->renderWithError(); ?></p>
        <p>Email: <?php $this->txtEmail->renderWithError(); ?></p>
        <p>Url: <?php $this->txtUrl->renderWithError(); ?></p>
        <p>Custom (Only hex): <?php $this->txtCustom->renderWithError(); ?></p>
        <p><?php $this->btnValidate->render(); ?></p>
        <p>Multiple Emails: <?php $this->txtMultipleEmails->render(); ?></p>
        <p>Validate: <?php $this->btnEmailValidate->render(); ?></p>
    </div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>