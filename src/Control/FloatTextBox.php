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

    use QCubed\Exception\Caller;
    use QCubed\Type;
    use QCubed as Q;

    /**
     * Class FloatTextBox
     *
     * A subclass of QNumericTextBox -- Validate will also ensure
     * that the Text is a valid float and (if applicable) is in the range of Minimum <= x <= Maximum
     *
     * We do not use the sanitized capability of TextBox here. Sanitizing the data will change the data, and
     * if the user does not type in a valid float, we will not be able to put up a warning telling the user they made
     * a mistake. You can easily change this behavior by setting the following:
     *    SanitizeFilter = FILTER_SANITIZE_NUMBER_FLOAT
     *  SanitizeFilterOptions = FILTER_FLAG_ALLOW_FRACTION
     *
     * @property int|null $Value            Returns the integer value of the text, sanitized.
     * @package QCubed\Control
     */
    class FloatTextBox extends NumericTextBox
    {
        //////////
        // Methods
        //////////

        /**
         * Constructor
         *
         * @param ControlBase|FormBase $objParentObject
         * @param null|string $strControlId
         * @throws Caller
         */
        public function __construct(ControlBase|FormBase $objParentObject, ?string $strControlId = null)
        {
            parent::__construct($objParentObject, $strControlId);
            $this->strLabelForInvalid = t('This must be a number');
            $this->strDataType = Type::FLOAT;
        }

        /**
         * Magic method to get the value of a property.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property.
         * @throws Caller If the property does not exist or cannot be retrieved.
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case "Value":
                    if ($this->strText === null || $this->strText === "") {
                        return null;
                    } else {
                        return (float)filter_var($this->strText, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
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
         * @return Q\Codegen\Generator\TextBox
         */
        public static function getCodeGenerator(): Q\Codegen\Generator\TextBox
        {
            return new Q\Codegen\Generator\TextBox(__CLASS__); // reuse the TextBox generator
        }
    }
