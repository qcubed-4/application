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
use QCubed\QDateTime;
use QCubed\Type;

/**
 * Class DateTimeTextBox
 *
 * @property QDateTime $Maximum
 * @property QDateTime $Minimum
 * @property string $DateTimeFormat
 * @property QDateTime $DateTime
 * @property string $LabelForInvalid
 * @was QDateTimeTextBox
 * @package QCubed\Control
 */
class DateTimeTextBox extends TextBox
{
    ///////////////////////////
    // Private Member Variables
    ///////////////////////////

    // MISC
    protected $dttMinimum = null;
    protected $dttMaximum = null;
    protected $strDateTimeFormat = "MMM D, YYYY";
    protected $dttDateTime = null;

    protected $strLabelForInvalid = 'For example, "Mar 20, 4:30pm" or "Mar 20"';
    protected $calLinkedControl;

    //////////
    // Methods
    //////////

    public function parsePostData()
    {
        // Check to see if this Control's Value was passed in via the POST data
        if (array_key_exists($this->strControlId, $_POST)) {
            parent::parsePostData();
            $this->dttDateTime = self::parseForDateTimeValue($this->strText);
        }
    }

    public static function parseForDateTimeValue($strText)
    {
        // Trim and Clean
        $strText = strtolower(trim($strText));
        while (strpos($strText, '  ') !== false) {
            $strText = str_replace('  ', ' ', $strText);
        }
        $strText = str_replace('.', '', $strText);
        $strText = str_replace('@', ' ', $strText);

        // Are we ATTEMPTING to parse a Time value?
        if ((strpos($strText, ':') === false) &&
            (strpos($strText, 'am') === false) &&
            (strpos($strText, 'pm') === false)
        ) {
            // There is NO TIME VALUE
            $dttToReturn = new QDateTime($strText);
            if ($dttToReturn->isDateNull()) {
                return null;
            } else {
                return $dttToReturn;
            }
        }

        // Add ':00' if it doesn't exist AND if 'am' or 'pm' exists
        if ((strpos($strText, 'pm') !== false) &&
            (strpos($strText, ':') === false)
        ) {
            $strText = str_replace(' pm', ':00 pm', $strText, $intCount);
            if (!$intCount) {
                $strText = str_replace('pm', ':00 pm', $strText, $intCount);
            }
        } else {
            if ((strpos($strText, 'am') !== false) &&
                (strpos($strText, ':') === false)
            ) {
                $strText = str_replace(' am', ':00 am', $strText, $intCount);
                if (!$intCount) {
                    $strText = str_replace('am', ':00 am', $strText, $intCount);
                }
            }
        }

        $dttToReturn = new QDateTime($strText);
        if ($dttToReturn->isDateNull()) {
            return null;
        } else {
            return $dttToReturn;
        }
    }

    public function validate()
    {
        if (parent::validate()) {
            if ($this->strText != "") {
                $dttTest = self::parseForDateTimeValue($this->strText);

                if (!$dttTest) {
                    $this->ValidationError = $this->strLabelForInvalid;
                    return false;
                }

                if (!is_null($this->dttMinimum)) {
                    if ($this->dttMinimum == QDateTime::NOW) {
                        $dttToCompare = new QDateTime(QDateTime::NOW);
                        $strError = t('in the past');
                    } else {
                        $dttToCompare = $this->dttMinimum;
                        $strError = t('before ') . (string)$dttToCompare;
                    }

                    if ($dttTest->isEarlierThan($dttToCompare)) {
                        $this->ValidationError = t('Date cannot be ') . $strError;
                        return false;
                    }
                }

                if (!is_null($this->dttMaximum)) {
                    if ($this->dttMaximum == QDateTime::NOW) {
                        $dttToCompare = new QDateTime(QDateTime::NOW);
                        $strError = t('in the future');
                    } else {
                        $dttToCompare = $this->dttMaximum;
                        $strError = t('after ') . (string)$dttToCompare;
                    }

                    if ($dttTest->isLaterThan($dttToCompare)) {
                        $this->ValidationError = t('Date cannot be ') . $strError;
                        return false;
                    }
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
    public function __get($strName)
    {
        switch ($strName) {
            // MISC
            case "Maximum":
                return $this->dttMaximum;
            case "Minimum":
                return $this->dttMinimum;
            case 'DateTimeFormat':
                return $this->strDateTimeFormat;
            case 'DateTime':
                return $this->dttDateTime;
            case 'LabelForInvalid':
                return $this->strLabelForInvalid;

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
     * @param string $strName
     * @param string $mixValue
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        $this->blnModified = true;

        switch ($strName) {
            // MISC
            case 'Maximum':
                try {
                    if ($mixValue == QDateTime::NOW) {
                        $this->dttMaximum = QDateTime::NOW;
                    } else {
                        $this->dttMaximum = Type::cast($mixValue, Type::DATE_TIME);
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Minimum':
                try {
                    if ($mixValue == QDateTime::NOW) {
                        $this->dttMinimum = QDateTime::NOW;
                    } else {
                        $this->dttMinimum = Type::cast($mixValue, Type::DATE_TIME);
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'DateTimeFormat':
                try {
                    $this->strDateTimeFormat = Type::cast($mixValue, Type::STRING);
                    // trigger an update to reformat the text with the new format
                    $this->DateTime = $this->dttDateTime;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case 'DateTime':
                try {
                    $this->dttDateTime = Type::cast($mixValue, Type::DATE_TIME);
                    if (!$this->dttDateTime || !$this->strDateTimeFormat) {
                        parent::__set('Text', '');
                    } else {
                        parent::__set('Text', $this->dttDateTime->qFormat($this->strDateTimeFormat));
                    }
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case 'Text':
                $this->dttDateTime = self::parseForDateTimeValue($this->strText);
                parent::__set('Text', $mixValue);
                break;


            case 'LabelForInvalid':
                try {
                    $this->strLabelForInvalid = Type::cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

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
     * Codegen Helper, used during the Codegen process only.
     *
     * @param string $strPropName
     * @return string
     */
    public static function codegen_VarName($strPropName)
    {
        return 'cal' . $strPropName;
    }
}
