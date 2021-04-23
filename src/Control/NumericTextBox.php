<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

require_once(dirname(dirname(__DIR__)) . '/i18n/i18n-lib.inc.php');
use QCubed\Application\t;

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Control\TextBox;
use QCubed\Type;
use QCubed as Q;

/**
 * Class NumericTextBox
 *
 * A subclass of TextBox with its validate method overridden -- Validate will also ensure
 * that the Text is a valid integer/float and (if applicable) is in the range of Minimum <= x <= Maximum.
 * This class is abstract. QIntegerTextBox and QFloatTextBox are derived from it.
 *
 * @property mixed $Maximum         (optional) is the maximum value the integer/float can be
 * @property mixed $Minimum         (optional) is the minimum value the integer/float can be
 * @property mixed $Step            (optional) is the step interval for allowed values ( beginning from $Minimum if set)
 * @property string $LabelForGreater Text to show when the input is greater than the allowed value
 * @property string $LabelForLess    Text to show when the input is lesser than the minimum allowed value
 * @property string $LabelForNotStepAligned
 *                          set this property to show an error message if the entered value is not step-aligned
 *                          if not set the value is changed to the next step-aligned value (no error)
 * @was QNumericTextBox
 * @package QCubed\Control
 */
abstract class NumericTextBox extends TextBox
{
    /** @var string Data type of the input (float|integer) */
    protected $strDataType = null;
    /** @var mixed Maximum allowed Value */
    protected $mixMaximum = null;
    /** @var mixed Minimum allowed value */
    protected $mixMinimum = null;
    /** @var mixed Float or Integer value, the multiple of which the input must be */
    protected $mixStep = null;

    /** @var string Text to show when the input value is less than minimum allowed value */
    protected $strLabelForLess;
    /** @var string Text to show when the input value is greater than the maximum allowed value */
    protected $strLabelForGreater;
    /** @var string Text to show when the input value is not step aligned */
    protected $strLabelForNotStepAligned = null;

    //////////
    // Methods
    //////////
    /**
     * Constructor for the control
     *
     * @param ControlBase|FormBase $objParentObject
     * @param null|string $strControlId
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->strLabelForInvalid = t('Invalid Number');
        $this->strLabelForLess = t('Value must be less than %s');
        $this->strLabelForGreater = t('Value must be greater than %s');
    }

    /**
     * @return bool whether or not the input passed all the values
     */
    public function validate()
    {
        if (parent::validate()) {
            if ($this->strText != "") {
                try {
                    $this->strText = Type::cast($this->strText, $this->strDataType);
                } catch (InvalidCast $objExc) {
                    $this->ValidationError = $this->strLabelForInvalid;
                    $this->markAsModified();
                    return false;
                }

                if (!is_numeric($this->strText)) {
                    $this->ValidationError = $this->strLabelForInvalid;
                    $this->markAsModified();
                    return false;
                }

                if (!is_null($this->mixStep)) {
                    $newVal = Type::cast(round(($this->strText - $this->mixMinimum) / $this->mixStep) * $this->mixStep + $this->mixMinimum,
                        $this->strDataType);

                    if ($newVal != $this->strText) {
                        if ($this->strLabelForNotStepAligned) {
                            $this->ValidationError = sprintf($this->strLabelForNotStepAligned, $this->mixStep);
                            $this->markAsModified();
                            return false;
                        }
                        $this->strText = $newVal;
                        $this->markAsModified();
                    }
                }

                if ((!is_null($this->mixMinimum)) && ($this->strText < $this->mixMinimum)) {
                    $this->ValidationError = sprintf($this->strLabelForGreater, $this->mixMinimum);
                    $this->markAsModified();
                    return false;
                }

                if ((!is_null($this->mixMaximum)) && ($this->strText > $this->mixMaximum)) {
                    $this->ValidationError = sprintf($this->strLabelForLess, $this->mixMaximum);
                    $this->markAsModified();
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * PHP __get magic method implementation
     * @param string $strName Name of the property
     *
     * @return mixed
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            // MISC
            case "Maximum":
                return $this->mixMaximum;
            case "Minimum":
                return $this->mixMinimum;
            case 'Step':
                return $this->mixStep;
            case 'LabelForGreater':
                return $this->strLabelForGreater;
            case 'LabelForLess':
                return $this->strLabelForLess;
            case 'LabelForNotStepAligned':
                return $this->strLabelForNotStepAligned;

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
     * @param string $strName
     * @param mixed $mixValue
     * @throws InvalidCast
     * @throws Caller
     */
    public function __set($strName, $mixValue)
    {
        $this->blnModified = true;

        switch ($strName) {
            // MISC
            case "Maximum":
                try {
                    $this->mixMaximum = Type::cast($mixValue, $this->strDataType);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Minimum":
                try {
                    $this->mixMinimum = Type::cast($mixValue, $this->strDataType);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Step":
                try {
                    $this->mixStep = Type::cast($mixValue, $this->strDataType);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'LabelForGreater':
                try {
                    $this->strLabelForGreater = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'LabelForLess':
                try {
                    $this->strLabelForLess = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'LabelForNotStepAligned':
                try {
                    $this->strLabelForNotStepAligned = Type::cast($mixValue, Type::STRING);
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
     * Returns an description of the options available to modify by the designer for the code generator.
     *
     * @return array
     */
    public static function getModelConnectorParams()
    {
        return array_merge(parent::getModelConnectorParams(), array(
            new Q\ModelConnector\Param(get_called_class(), 'Maximum', 'Meximum value allowed', Type::STRING),
            // float or integer
            new Q\ModelConnector\Param(get_called_class(), 'Minimum', 'Meximum value allowed', Type::STRING),
            new Q\ModelConnector\Param(get_called_class(), 'Step', 'If value must be aligned on a step, the step amount',
                Type::STRING),
            new Q\ModelConnector\Param(get_called_class(), 'LabelForLess',
                'If value is too small, override the default error message', Type::STRING),
            new Q\ModelConnector\Param(get_called_class(), 'LabelForGreater',
                'If value is too big, override the default error message', Type::STRING),
            new Q\ModelConnector\Param(get_called_class(), 'LabelForNotStepAligned',
                'If value is not step aligned, override the default error message', Type::STRING)
        ));
    }
}
