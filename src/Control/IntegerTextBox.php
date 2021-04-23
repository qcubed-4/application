<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Application\t;

use QCubed\Exception\Caller;
use QCubed\Type;
use QCubed as Q;

require_once(dirname(dirname(__DIR__)) . '/i18n/i18n-lib.inc.php');


/**
 * Class IntegerTextBox
 *
 * A subclass of TextBox with its validate method overridden -- Validate will also ensure
 * that the Text is a valid integer and (if applicable) is in the range of Minimum <= x <= Maximum
 *
 * We do not use the sanitize capability of QTextBox here. Sanitizing the data will change the data, and
 * if the user does not type in an integer, we will not be able to put up a warning telling the user they made
 * a mistake. You can easily change this behavior by setting SanitizeFilter = FILTER_SANITIZE_NUMBER_INT.
 *
 * @property int|null $Value            Returns the integer value of the text, sanitized.
 * @was QIntegerTextBox
 * @package QCubed\Control
 */
class IntegerTextBox extends NumericTextBox
{
    /**
     * Constructor
     *
     * @param ControlBase|FormBase $objParentObject
     * @param null|string $strControlId
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->strLabelForInvalid = t('Invalid Integer');
        $this->strDataType = Type::INTEGER;
    }

    public function __get($strName)
    {
        switch ($strName) {
            case "Value":
                if ($this->strText === null || $this->strText === "") {
                    return null;
                } else {
                    return (int)filter_var($this->strText, FILTER_SANITIZE_NUMBER_INT);
                }

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * Returns the generator corresponding to this control.
     *
     * @return Q\Codegen\Generator\GeneratorBase
     */
    public static function getCodeGenerator() {
        return new Q\Codegen\Generator\TextBox(__CLASS__); // reuse the TextBox generator
    }
}
