<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Exception\Caller;
use QCubed\Type;
use QCubed as Q;

require_once(dirname(__DIR__, 2) . '/i18n/i18n-lib.inc.php');


/**
 * Class IntegerTextBox
 *
 * A subclass of TextBox with its validate method overridden -- Validate will also ensure
 * that the Text is a valid integer and (if applicable) is in the range of Minimum <= x <= Maximum
 *
 * We do not use the sanitized capability of QTextBox here. Sanitizing the data will change the data, and
 * if the user does not type in an integer, we will not be able to put up a warning telling the user they made
 * a mistake. You can easily change this behavior by setting SanitizeFilter = FILTER_SANITIZE_NUMBER_INT.
 *
 * @property int|null $Value            Returns the integer value of the text, sanitized.
 * @package QCubed\Control
 */
class IntegerTextBox extends NumericTextBox
{
    /**
     * Constructor method for initializing the object.
     *
     * @param mixed $objParentObject The parent object that holds a reference to this control.
     * @param string|null $strControlId Optional control ID for unique identification.
     * @return void
     * @throws Caller
     */
    public function __construct(mixed $objParentObject, ?string $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->strLabelForInvalid = t('Invalid Integer');
        $this->strDataType = Type::INTEGER;
    }

    /**
     * Magic method to retrieve the value of a property.
     *
     * @param string $strName The name of the property to retrieve.
     * @return mixed|null The value of the property, null if the property is empty, or the sanitized integer value for specific attributes.
     * @throws Caller If an invalid property name is accessed or another exception occurs.
     */
    public function __get(string $strName): mixed
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
     * Retrieves the code generator instance for the current class.
     *
     * @return Q\Codegen\Generator\TextBox An instance of the TextBox code generator specific to the class.
     */
    public static function getCodeGenerator(): Q\Codegen\Generator\TextBox
    {
        return new Q\Codegen\Generator\TextBox(__CLASS__); // reuse the TextBox generator
    }
}
