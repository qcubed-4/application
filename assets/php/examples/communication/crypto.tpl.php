<?php require('../includes/header.inc.php'); ?>
<?php $this->renderBegin(); ?>

<div id="instructions">
<h1>Implementing Cryptography</h1>

<p>The <strong>Cryptography</strong> class is used to implement cryptography for your site and
    back-end. Cryptography uses methods from the <strong>openssl</strong> library integrated into PHP </p>

<p>By default, <strong>Cryptography</strong> will use the <strong>AES 256-bit</strong> cipher in <strong>CBC (Cipher
        Block Chaining)</strong> mode.
    We choose AES-256-CBC because it is a strong cipher and is recommended by the US government for its own secret
    documents and hence is widely accepted too.
    Cryptography can also conveniently do base64 conversion (similar to MIME-based
    Base64 encoding) so that the resulting encrypted data can be used in text-based streams,
    GET/POST data, URLs, etc. By default, Base64 encoding is enabled, since we mostly deal with HTTP on the web, and
    it's just easier to transport text than binary.</p>

<p>However, note that any of these options can be changed at any time. Through the <strong>openssl</strong>
    library, <strong>Cryptography</strong> supports most of the industry-accepted ciphers. You can use the
    <strong><a href="http://php.net/manual/en/function.openssl-get-cipher-methods.php" target="_blank">openssl-get-cipher-methods</a></strong>
    method to see the list of supported encryption methods.</p>

<p>You can specify a "default" cipher, base64 flag, key and initialization vector by modifying
    the arguments when constructing a new instance of <strong>Cryptography</strong>.</p>

<p><em>Asymmetric Cryptography (using public-private key pairs) is not currently supported.</em></p>

<p><strong>Cryptography</strong> also supports the encryption and decryption of entire files.</p>

<p>By default, the QCubed-4 framework is not set up with a default cryptography key. You can set one up for your
    application by
    defining the <strong>QCUBED_CRYPTOGRAPHY_DEFAULT_KEY</strong> define. By sure to keep this key private.

<h2>The Initialization Vector</h2>
Some ciphers require an initialization vector. An initialization vector is an important part of preventing someone
from being able to guess your key from a series of encrypted data. Initialization vectors must be random and should
be remembered. Generally speaking, you should let the Cryptography class handle the creation and management of
the initialization vector for you by specifying null. It will embed the initialization vector into the encrypted
data
(which is fine and does not compromise the data, it just makes it longer). Only in special situations where you are
trying to limit the size of encrypted data would you need to manage the IV yourself.
</div>

<div id="demoZone">
    <h2>Default Settings - AES 256-bit CBC with default IV and Key</h2>
    <ul>
        <?php
        printf('<li>Original Data: <strong>%s</strong></li>', $this->strOriginal);
        printf('<li>Encrypted Data: <pre><code>%s</code></pre></li>', wordwrap($this->strEncrypted, 60, "<br />\n", true));
        printf('<li>Decrypted Data: <strong>%s</strong></li>', $this->strDecrypted);
        ?>
    </ul>

    <h2>Blowfish, Cipher Block Chaining, with Base64 encoding and a custom IV)</h2>
    <ul>
    <?php
    printf('<li>Original Data: <strong>%s</strong></li>', $this->strOriginal);
    printf('<li>Encrypted Data: <pre><code>%s</code></pre></li>', wordwrap($this->strEncrypted1, 60, "<br />\n", true));
    printf('<li>Decrypted Data: <strong>%s</strong></li>', $this->strDecrypted1);
    ?>
    </ul>

    <h2>Blowfish, Cipher Block Chaining, without Base64 encoding and the same custom IV as above)</h2>
    <ul>
        <?php
        printf('<li>Original Data: <strong>%s</strong></li>', $this->strOriginal);
        printf('<li>Encrypted Data: <pre><code>%s</code></pre></li>', wordwrap($this->strEncrypted2, 60, "<br />\n", true));
        printf('<li>Decrypted Data: <strong>%s</strong></li>', $this->strDecrypted2);
        ?>
    </ul>
</div>

<?php $this->renderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>

