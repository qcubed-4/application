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

//use QCubed\Application\t;

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\ModelConnector\Param;
use QCubed\Type;
use QCubed as Q;
use QCubed\Project\Control\TextBox;
use Throwable;

/**
 * Class CsvTextBox
 *
 * A subclass of TextBox that allows the user to type in a list of values to be converted into
 * an array. Use str_getcsv to process.
 *
 * @property string $Delimiter is the csv separator. Default: (comma)
 * @property string $Enclosure
 * @property string $Escape
 * @property integer $MinItemCount
 * @property integer $MaxItemCount
 * @package QCubed\Control
 */
class CsvTextBox extends TextBox
{
    /** @var string */
    protected string $strDelimiter = ',';
    /** @var string */
    protected string $strEnclosure = '"';
    /** @var string */
    protected string $strEscape = '\\';
    /** @var int|null */
    protected ?int $intMinItemCount = null;
    /** @var int|null */
    protected ?int $intMaxItemCount = null;

    /**
     * Constructor
     *
     * @param ControlBase|FormBase $objParentObject Parent of this textbox
     * @param null|string $strControlId Desired control ID for the textbox
     * @throws Caller
     */
    public function __construct(ControlBase|FormBase $objParentObject, ?string $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        // borrows too short and too long labels from super class
        $this->strLabelForTooShort = t('Enter at least %s items.');
        $this->strLabelForTooLong = t('Enter no more than %s items.');
    }

    /**
     * Validate the control, setting validation error if there is a problem.
     * @return bool
     */
    public function validate(): bool
    {
        $blnRet = parent::validate();
        if ($blnRet) {
            $a = str_getcsv($this->strText ?? '', $this->strDelimiter, $this->strEnclosure, $this->strEscape);

            if ($this->intMinItemCount !== null &&
                count($a) < $this->intMinItemCount
            ) {
                $this->ValidationError = sprintf($this->strLabelForTooShort, $this->intMinItemCount);
                return false;
            }

            if ($this->intMaxItemCount !== null &&
                count($a) > $this->intMaxItemCount
            ) {
                $this->ValidationError = sprintf($this->strLabelForTooLong, $this->intMaxItemCount);
                return false;
            }
        }

        // If we're here, then everything is a-ok.  Return true.
        return true;
    }

    /**
     * PHP magic method
     * @param string $strName Property name
     *
     * @return mixed
     * @throws Caller
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            // APPEARANCE
            case "Delimiter":
                return $this->strDelimiter;
            case "Enclosure":
                return $this->strEnclosure;
            case "Escape":
                return $this->strEscape;
            case "MinItemCount":
                return $this->intMinItemCount;
            case "MaxItemCount":
                return $this->intMaxItemCount;
            case 'Value':
                if (empty($this->strText)) {
                    return array();
                }
                return str_getcsv($this->strText, $this->strDelimiter, $this->strEnclosure, $this->strEscape);

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /////////////////////////
    // Public Properties: SET
    /////////////////////////
    /**
     * PHP __set magic method implementation
     *
     * @param string $strName Name of the property
     * @param mixed $mixValue Value of the property
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     * @throws Throwable
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        $this->blnModified = true;

        switch ($strName) {
            // APPEARANCE
            case "Delimiter":
                try {
                    $this->strDelimiter = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Enclosure":
                try {
                    $this->strEnclosure = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Escape":
                try {
                    $this->strEscape = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "MinItemCount":
                try {
                    $this->intMinItemCount = Type::cast($mixValue, Type::INTEGER);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "MaxItemCount":
                try {
                    $this->intMaxItemCount = Type::cast($mixValue, Type::INTEGER);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Value":
                try {
                    $a = Type::cast($mixValue, Type::ARRAY_TYPE);
                    $temp_memory = fopen('php://memory', 'w');
                    fputcsv($temp_memory, $a, $this->strDelimiter, $this->strEnclosure);
                    rewind($temp_memory);
                    $this->strText = fgets($temp_memory);
                    fclose($temp_memory);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }

    /**
     * Returns a description of the options available to modify by the designer for the code generator.
     *
     * @return Param[]
     * @throws Caller
     */
    public static function getModelConnectorParams(): array
    {
        return array_merge(parent::getModelConnectorParams(), array(
            new Q\ModelConnector\Param(get_called_class(), 'Delimiter', 'Default: , (comma)', Type::STRING),
            new Q\ModelConnector\Param(get_called_class(), 'Enclosure', 'Default: " (double-quote)',
                Type::STRING),
            new Q\ModelConnector\Param(get_called_class(), 'Escape', 'Default: \\ (backslash)', Type::STRING),
            new Q\ModelConnector\Param(get_called_class(), 'MinItemCount', 'Minimum number of items required.',
                Type::INTEGER),
            new Q\ModelConnector\Param(get_called_class(), 'MaxItemCount', 'Maximum number of items allowed.',
                Type::INTEGER)
        ));
    }
}
