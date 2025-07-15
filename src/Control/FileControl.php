<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

require_once(dirname(__DIR__, 2) . '/i18n/i18n-lib.inc.php');

use QCubed as Q;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;


/**
 * Class FileControl
 *
 * This class will render an HTML File input.
 *
 * @package Controls
 *
 * @property-read string $FileName is the name of the file that the user uploads?
 * @property-read string $Type is the MIME type of the file?
 * @property-read integer $Size is the size in bytes of the file?
 * @property-read string $File is the temporary full file path on the server where the file physically resides
 * @package QCubed\Control
 */
class FileControl extends Q\Project\Control\ControlBase
{
    ///////////////////////////
    // Private Member Variables
    ///////////////////////////

    // MISC
    protected ?string $strFileName = null;
    protected ?string $strType = null;
    protected ?int $intSize = null;
    protected ?string $strFile = null;

    // SETTINGS
    protected array $strFormAttributes = array('enctype' => 'multipart/form-data');

    //////////
    // Methods
    //////////
    /**
     * Parses data sent via a POST request and updates control properties with the uploaded file details.
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function parsePostData(): void
    {
        // Check to see if this Control's Value was passed in via the POST data
        if ((array_key_exists($this->strControlId, $_FILES)) && ($_FILES[$this->strControlId]['tmp_name'])) {
            // It was -- update this Control's value with the new value passed in via the POST arguments
            $this->strFileName = $_FILES[$this->strControlId]['name'];
            $this->strType = $_FILES[$this->strControlId]['type'];
            $this->intSize = Type::cast($_FILES[$this->strControlId]['size'], Type::INTEGER);
            $this->strFile = $_FILES[$this->strControlId]['tmp_name'];
        }
    }

    /**
     * Returns the HTML of the control which can be sent to the user's browser
     *
     * @return string HTML of the control
     */
    protected function getControlHtml(): string
    {
        // Reset Internal Values
        //$this->strFileName = null;
        //$this->strType = null;
        //$this->intSize = null;
        //$this->strFile = null;

        $strStyle = $this->getStyleAttributes();
        if ($strStyle) {
            $strStyle = sprintf('style="%s"', $strStyle);
        }

        return sprintf('<input type="file" name="%s" id="%s" %s%s />',
            $this->strControlId,
            $this->strControlId,
            $this->renderHtmlAttributes(),
            $strStyle);
    }

    /**
     * Tells if the file control is valid
     *
     * @return bool
     */
    public function validate(): bool
    {
        if ($this->blnRequired) {
            if ($this->strFileName) {
                return true;
            } else {
                $this->ValidationError = t('File selection is required');
                return false;
            }
        } else {
            return true;
        }
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * Magic method to retrieve the value of a property dynamically.
     *
     * @param string $strName The name of the property to retrieve.
     *
     * @return mixed Returns the value of the requested property if it exists.
     * @throws Caller Throws an exception if the property is not defined or inaccessible.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            // MISC
            case "FileName":
                return $this->strFileName;
            case "Type":
                return $this->strType;
            case "Size":
                return $this->intSize;
            case "File":
                return $this->strFile;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }
}
