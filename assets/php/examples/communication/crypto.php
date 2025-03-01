<?php
use QCubed\Control\Label;
use QCubed\Cryptography;

require_once('../qcubed.inc.php');

// Define the \QCubed\Project\Control\FormBase with all our Qcontrols
class ExamplesForm extends \QCubed\Project\Control\FormBase
{

    // Local declarations of our Qcontrols
    protected $strOriginal;

    protected $objCrypto;
    protected $strEncrypted;
    protected $strDecrypted;

    protected $objCrypto1;
    protected $strEncrypted1;
    protected $strDecrypted1;

    protected $objCrypto2;
    protected $strEncrypted2;
    protected $strDecrypted2;


    // Initialize our Controls during the Form Creation process
    protected function formCreate()
    {
        $this->strOriginal = 'The quick brown fox jumps over the lazy dog.';

        // This is an example of assigning a key at creation time, but you can also
        // set up a default key value that will be used if no key is specified.

        $this->objCrypto = new Cryptography('MyTempKey$#2');
        $this->strEncrypted = $this->objCrypto->encrypt($this->strOriginal);
        $this->strDecrypted  = $this->objCrypto->decrypt($this->strEncrypted);

        // Modify the base64 mode while making the specification on the constructor, itself
        // By default, let's instantiate a \QCubed\Cryptography object with Base64 encoding enabled
        // Note: while the resulting encrypted data is safe for any text-based stream, including
        // use as GET/POST data, inside the URL, etc., the resulting encrypted data stream will
        // be 33% larger.

        $this->objCrypto1 = new Cryptography('MyTempKey$#2', true, 'BF-CBC');
        $this->strEncrypted1 = $this->objCrypto1->encrypt($this->strOriginal);
        $this->strDecrypted1 = $this->objCrypto1->decrypt($this->strEncrypted1);

        // Modify the base64 mode while making the specification on the constructor, itself
        // By default, let's instantiate a \QCubed\Cryptography object with Base64 encoding enabled
        // Note: while the resulting encrypted data is safe for any text-based stream, including
        // use as GET/POST data, inside the URL, etc., the resulting encrypted data stream will
        // be 33% larger.

        $this->objCrypto2 = new Cryptography('MyTempKey$#2', true, 'BF-CBC');
        $this->strEncrypted2 = $this->objCrypto2->encrypt($this->strOriginal);
        $this->strDecrypted2 = $this->objCrypto2->decrypt($this->strEncrypted2);
    }
}

// Run the Form we have defined
ExamplesForm::run('ExamplesForm');