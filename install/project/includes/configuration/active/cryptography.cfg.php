<?php

    /**
     * This is the default algorithm to be used
     *
     * Cryptography module can help in encrypting or decrypting
     * It utilizes the openssl library functions built into PHP
     *
     * NOTE: Cryptography uses only symmetric algorithms
     *
     * AES 256-bit is strong enough for most use cases and is recommended by the US government for classified documents.
     *
     * CBC or Cipher Block Chaining is considered considerably safer than ECB (Electronic CodeBook) methods.
     * For more background: https://en.wikipedia.org/wiki/Block_cipher_mode_of_operation
     */
    const QCUBED_CRYPTOGRAPHY_DEFAULT_CIPHER = 'AES-256-CBC';

    /**
     * Default KEY (used as a password) for AES-256-CBC
     */
    const QCUBED_CRYPTOGRAPHY_DEFAULT_KEY = 'qc0Do!d3F@lT.k3Y';

