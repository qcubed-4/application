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
use QCubed\QDateTime;
use QCubed\Type;
use QCubed as Q;


/**
 * This class is meant to be a date-picker.  It will essentially render an uneditable HTML textbox
 * as well as a calendar icon.  The idea is that if you click on the icon or the textbox,
 * it will pop up a calendar in a new small window.
 *
 * @package Controls
 *
 * @property null|QDateTime $DateTime
 * @property string $DateTimePickerType
 * @property string $DateTimePickerFormat
 * @property integer $MinimumYear Minimum Year to show
 * @property integer $MaximumYear Maximum Year to show
 * @property bool $AllowBlankTime Allow the '--' value for the Time section of control's UI
 * @property bool $AllowBlankDate Allow the '--' value for the Date section of control's UI
 * @property string $TimeSeparator Character to separate the select boxes for hour, minute and seconds
 * @property int $SecondInterval Seconds are shown in these intervals
 * @property int $MinuteInterval Minutes are shown in these intervals
 * @property int $HourInterval Hours are shown in these intervals
 * @was QDateTimePicker
 */
class DateTimePicker extends Q\Project\Control\ControlBase
{
    const SHOW_DATE = 'Date';
    const SHOW_DATE_TIME = 'DateTime';
    const SHOW_DATE_TIME_SECONDS = 'DateTimeSeconds';
    const SHOW_TIME = 'Time';
    const SHOW_TIME_SECONDS = 'TimeSeconds';

    const MONTH_DAY_YEAR = 'MonthDayYear';
    const DAY_MONTH_YEAR = 'DayMonthYear';
    const YEAR_MONTH_DAY = 'YearMonthDay';

    /** @var QDateTime|null */
    protected $dttDateTime = null;
    protected $strDateTimePickerType = self::SHOW_DATE;
    protected $strDateTimePickerFormat = self::MONTH_DAY_YEAR;

    protected $intMinimumYear = 1970;
    protected $intMaximumYear = 2030;

    protected $intSelectedMonth = null;
    protected $intSelectedDay = null;
    protected $intSelectedYear = null;

    /** @var bool Allow or Disallow Choosing '--' in the control UI for time */
    protected $blnAllowBlankTime = true;
    /** @var bool Allow or Disallow Choosing '--' in the control UI for date */
    protected $blnAllowBlankDate = true;
    /** @var bool The character which appears between the hour, minutes and seconds */
    protected $strTimeSeparator = ':';
    /** @var int Steps of intervals to show for second field */
    protected $intSecondInterval = 1;
    /** @var int Steps of intervals to show for minute field */
    protected $intMinuteInterval = 1;
    /** @var int Steps of intervals to show for hour field */
    protected $intHourInterval = 1;


    protected $strCssClass = 'datetimepicker';

    /**
     * DateTimePicker constructor.
     * @param ControlBase|FormBase $objParent
     * @param null $strControlId
     */
    public function __construct($objParent, $strControlId = null)
    {
        parent::__construct($objParent, $strControlId);
        $this->addJavascriptFile(QCUBED_JS_URL . '/date_time_picker.js');
    }

    public function parsePostData()
    {
        $blnIsDateTimeSet = false;
        if ($this->dttDateTime == null) {
            $dttNewDateTime = QDateTime::now();
        } else {
            $blnIsDateTimeSet = true;
            $dttNewDateTime = $this->dttDateTime;
        }

        // Update Date Component
        switch ($this->strDateTimePickerType) {
            case self::SHOW_DATE:
            case self::SHOW_DATE_TIME:
            case self::SHOW_DATE_TIME_SECONDS:
                $strKey = $this->strControlId . '_lstMonth';
                if (array_key_exists($strKey, $_POST)) {
                    $intMonth = $_POST[$strKey];
                } else {
                    if ($blnIsDateTimeSet) {
                        $intMonth = $dttNewDateTime->Month;
                    } else {
                        $intMonth = null;
                    }
                }

                $strKey = $this->strControlId . '_lstDay';
                if (array_key_exists($strKey, $_POST)) {
                    $intDay = $_POST[$strKey];
                } else {
                    if ($blnIsDateTimeSet) {
                        $intDay = $dttNewDateTime->Day;
                    } else {
                        $intDay = null;
                    }
                }

                $strKey = $this->strControlId . '_lstYear';
                if (array_key_exists($strKey, $_POST)) {
                    $intYear = $_POST[$strKey];
                } else {
                    if ($blnIsDateTimeSet) {
                        $intYear = $dttNewDateTime->Year;
                    } else {
                        $intYear = null;
                    }
                }

                $this->intSelectedMonth = $intMonth;
                $this->intSelectedDay = $intDay;
                $this->intSelectedYear = $intYear;

                if (!empty($intYear) && !empty($intMonth) && !empty($intDay)) {
                    $dttNewDateTime->setDate($intYear, $intMonth, $intDay);
                } else {
                    $dttNewDateTime->Year = null;
                }
                break;
        }

        // Update Time Component
        if (!$dttNewDateTime->isTimeNull()) {
            // Time is NOT NULL
            $blnIsTimeSet = true;
        } else {
            // TIME IS NULL
            $blnIsTimeSet = false;
        }

        switch ($this->strDateTimePickerType) {
            case self::SHOW_TIME:
            case self::SHOW_TIME_SECONDS:
            case self::SHOW_DATE_TIME:
            case self::SHOW_DATE_TIME_SECONDS:
                $strKey = $this->strControlId . '_lstHour';
                if (array_key_exists($strKey, $_POST)) {
                    $intHour = $_POST[$strKey];
                } else {
                    if ($blnIsTimeSet) {
                        $intHour = $dttNewDateTime->Hour;
                    } else {
                        $intHour = null;
                    }
                }

                $strKey = $this->strControlId . '_lstMinute';
                if (array_key_exists($strKey, $_POST)) {
                    $intMinute = $_POST[$strKey];
                } else {
                    if ($blnIsTimeSet) {
                        $intMinute = $dttNewDateTime->Minute;
                    } else {
                        $intMinute = null;
                    }
                }

                $intSecond = 0;

                if (($this->strDateTimePickerType == self::SHOW_TIME_SECONDS) ||
                    ($this->strDateTimePickerType == self::SHOW_DATE_TIME_SECONDS)
                ) {
                    $strKey = $this->strControlId . '_lstSecond';
                    if (array_key_exists($strKey, $_POST)) {
                        $intSecond = $_POST[$strKey];
                    } else {
                        if ($blnIsTimeSet) {
                            $intSecond = $dttNewDateTime->Second;
                        } else {
                            $intSecond = 0;
                        }
                    }
                }

                if (!empty($intHour) && !empty($intMinute) && !empty($intSecond)) {
                    $dttNewDateTime->setTime($intHour, $intMinute, $intSecond);
                } else {
                    $dttNewDateTime->Hour = null;
                }

                break;
        }

        $this->dttDateTime = $dttNewDateTime;
    }

    /**
     * Returns the HTML used to render this control
     *
     * @return string
     */
    protected function getControlHtml()
    {
        // Ignore Class
        $strCssClass = $this->strCssClass;
        $this->strCssClass = '';
        $strAttributes = $this->getAttributes();
        $this->strCssClass = $strCssClass;

        $strStyle = $this->getStyleAttributes();
        if ($strStyle) {
            $strAttributes .= sprintf(' style="%s"', $strStyle);
        }

        $strCommand = sprintf(' onchange="Qcubed__DateTimePicker_Change(\'%s\', this);"', $this->strControlId);

        if ($this->dttDateTime) {
            $dttDateTime = $this->dttDateTime;
        } else {
            $dttDateTime = new QDateTime();
        }

        $strToReturn = '';

        // Generate Date-portion
        switch ($this->strDateTimePickerType) {
            case self::SHOW_DATE:
            case self::SHOW_DATE_TIME:
            case self::SHOW_DATE_TIME_SECONDS:
                // Month
                $strMonthListbox = sprintf(
                    '<select name="%s_lstMonth" id="%s_lstMonth" class="month" %s%s>',
                    $this->strControlId,
                    $this->strControlId,
                    $strAttributes,
                    $strCommand
                );

                if (!$this->blnRequired || $dttDateTime->isDateNull()) {
                    if ($this->blnAllowBlankDate) {
                        $strMonthListbox .= '<option value="">--</option>';
                    }
                }

                for ($intMonth = 1; $intMonth <= 12; $intMonth++) {
                    if ((!$dttDateTime->isDateNull() && ($dttDateTime->Month == $intMonth)) || ($this->intSelectedMonth == $intMonth)) {
                        $strSelected = ' selected="selected"';
                    } else {
                        $strSelected = '';
                    }

                    $dateObj = new QDateTime("2000-$intMonth-01");
                    $strMonthListbox .= sprintf(
                        '<option value="%s"%s>%s</option>',
                        $intMonth,
                        $strSelected,
                        $dateObj->format('M')  // Kasuta DateTime objekti, et saada lühike kuu nimi
                    );
                }

                $strMonthListbox .= '</select>';

                // Day
                $strDayListbox = sprintf(
                    '<select name="%s_lstDay" id="%s_lstDay" class="day" %s%s>',
                    $this->strControlId,
                    $this->strControlId,
                    $strAttributes,
                    $strCommand
                );

                if (!$this->blnRequired || $dttDateTime->isDateNull()) {
                    if ($this->blnAllowBlankDate) {
                        $strDayListbox .= '<option value="">--</option>';
                    }
                }

                if ($dttDateTime->isDateNull()) {
                    if ($this->blnRequired) {
                        // New DateTime, but we are required -- therefore, let's assume January is preselected
                        for ($intDay = 1; $intDay <= 31; $intDay++) {
                            $strDayListbox .= sprintf('<option value="%s">%s</option>', $intDay, $intDay);
                        }
                    } else {
                        // New DateTime -- but we are NOT required
                        // See if a month has been selected yet.
                        if ($this->intSelectedMonth) {
                            $intSelectedYear = ($this->intSelectedYear) ? $this->intSelectedYear : 2000;
                            $intDaysInMonth = date('t', mktime(0, 0, 0, $this->intSelectedMonth, 1, $intSelectedYear));
                            for ($intDay = 1; $intDay <= $intDaysInMonth; $intDay++) {
                                if (($dttDateTime->Day == $intDay) || ($this->intSelectedDay == $intDay)) {
                                    $strSelected = ' selected="selected"';
                                } else {
                                    $strSelected = '';
                                }
                                $strDayListbox .= sprintf('<option value="%s"%s>%s</option>',
                                    $intDay,
                                    $strSelected,
                                    $intDay);
                            }
                        } else {
                            // It's ok just to have the "--" marks and nothing else
                            $strDayListbox .= '<option value="">--</option>';
                        }
                    }
                } else {
                    $intDaysInMonth = $dttDateTime->pHPDate('t');
                    for ($intDay = 1; $intDay <= $intDaysInMonth; $intDay++) {
                        if (($dttDateTime->Day == $intDay) || ($this->intSelectedDay == $intDay)) {
                            $strSelected = ' selected="selected"';
                        } else {
                            $strSelected = '';
                        }
                        $strDayListbox .= sprintf('<option value="%s"%s>%s</option>',
                            $intDay,
                            $strSelected,
                            $intDay);
                    }
                }

                $strDayListbox .= '</select>';

                // Year
                $strYearListbox = sprintf(
                    '<select name="%s_lstYear" id="%s_lstYear" class="year" %s%s>',
                    $this->strControlId,
                    $this->strControlId,
                    $strAttributes,
                    $strCommand
                );

                if (!$this->blnRequired || $dttDateTime->isDateNull()) {
                    if ($this->blnAllowBlankDate) {
                        $strYearListbox .= '<option value="">--</option>';
                    }
                }

                for ($intYear = $this->intMinimumYear; $intYear <= $this->intMaximumYear; $intYear++) {
                    if (/*!$dttDateTime->isDateNull() && */
                    (($dttDateTime->Year == $intYear) || ($this->intSelectedYear == $intYear))) {
                        $strSelected = ' selected="selected"';
                    } else {
                        $strSelected = '';
                    }

                    $strYearListbox .= sprintf('<option value="%s"%s>%s</option>', $intYear, $strSelected, $intYear);
                }

                $strYearListbox .= '</select>';

                // Put it all together
                switch ($this->strDateTimePickerFormat) {
                    case self::MONTH_DAY_YEAR:
                        $strToReturn .= $strMonthListbox . $strDayListbox . $strYearListbox;
                        break;
                    case QDateTimePickerFormat::DayMonthYear:
                        $strToReturn .= $strDayListbox . $strMonthListbox . $strYearListbox;
                        break;
                    case QDateTimePickerFormat::YearMonthDay:
                        $strToReturn .= $strYearListbox . $strMonthListbox . $strDayListbox;
                        break;
                }
                break;
        }

        switch ($this->strDateTimePickerType) {
            case self::SHOW_DATE_TIME:
            case self::SHOW_DATE_TIME_SECONDS:
                $strToReturn .= '<span class="divider"></span>';
        }

        switch ($this->strDateTimePickerType) {
            case self::SHOW_TIME:
            case self::SHOW_TIME_SECONDS:
            case self::SHOW_DATE_TIME:
            case self::SHOW_DATE_TIME_SECONDS:
                // Hour
                $strHourListBox = sprintf('<select name="%s_lstHour" id="%s_lstHour" class="hour" %s>',
                    $this->strControlId, $this->strControlId, $strAttributes);
                if (!$this->blnRequired || $dttDateTime->isTimeNull()) {
                    if ($this->blnAllowBlankTime) {
                        $strHourListBox .= '<option value="">--</option>';
                    }
                }
                for ($intHour = 0; $intHour <= 23; $intHour += $this->intHourInterval) {
                    if (!$dttDateTime->isTimeNull() && ($dttDateTime->Hour == $intHour)) {
                        $strSelected = ' selected="selected"';
                    } else {
                        $strSelected = '';
                    }
                    $strHourListBox .= sprintf('<option value="%s"%s>%s</option>',
                        $intHour,
                        $strSelected,
                        date('g A', mktime($intHour, 0, 0, 1, 1, 2000)));
                }
                $strHourListBox .= '</select>';

                // Minute
                $strMinuteListBox = sprintf('<select name="%s_lstMinute" id="%s_lstMinute" class="minute" %s>',
                    $this->strControlId, $this->strControlId, $strAttributes);
                if (!$this->blnRequired || $dttDateTime->isTimeNull()) {
                    if ($this->blnAllowBlankTime) {
                        $strMinuteListBox .= '<option value="">--</option>';
                    }
                }
                for ($intMinute = 0; $intMinute <= 59; $intMinute += $this->intMinuteInterval) {
                    if (!$dttDateTime->isTimeNull() && ($dttDateTime->Minute == $intMinute)) {
                        $strSelected = ' selected="selected"';
                    } else {
                        $strSelected = '';
                    }
                    $strMinuteListBox .= sprintf('<option value="%s"%s>%02d</option>',
                        $intMinute,
                        $strSelected,
                        $intMinute);
                }
                $strMinuteListBox .= '</select>';


                // Seconds
                $strSecondListBox = sprintf('<select name="%s_lstSecond" id="%s_lstSecond" class="second" %s>',
                    $this->strControlId, $this->strControlId, $strAttributes);
                if (!$this->blnRequired || $dttDateTime->isTimeNull()) {
                    if ($this->blnAllowBlankTime) {
                        $strSecondListBox .= '<option value="">--</option>';
                    }
                }
                for ($intSecond = 0; $intSecond <= 59; $intSecond += $this->intSecondInterval) {
                    if (!$dttDateTime->isTimeNull() && ($dttDateTime->Second == $intSecond)) {
                        $strSelected = ' selected="selected"';
                    } else {
                        $strSelected = '';
                    }
                    $strSecondListBox .= sprintf('<option value="%s"%s>%02d</option>',
                        $intSecond,
                        $strSelected,
                        $intSecond);
                }
                $strSecondListBox .= '</select>';


                // PUtting it all together
                if (($this->strDateTimePickerType == self::SHOW_DATE_TIME_SECONDS) ||
                    ($this->strDateTimePickerType == self::SHOW_TIME_SECONDS)
                ) {
                    $strToReturn .= $strHourListBox . $this->strTimeSeparator . $strMinuteListBox . $this->strTimeSeparator . $strSecondListBox;
                } else {
                    $strToReturn .= $strHourListBox . $this->strTimeSeparator . $strMinuteListBox;
                }
        }

        if ($this->strCssClass) {
            $strCssClass = ' class="' . $this->strCssClass . '"';
        } else {
            $strCssClass = '';
        }
        return sprintf('<span id="%s"%s>%s</span>', $this->strControlId, $strCssClass, $strToReturn);
    }

    public function validate()
    {
        if ($this->blnRequired) {
            $blnIsNull = false;

            if (!$this->dttDateTime) {
                $blnIsNull = true;
            } else {
                if ((($this->strDateTimePickerType == self::SHOW_DATE) ||
                        ($this->strDateTimePickerType == self::SHOW_DATE_TIME) ||
                        ($this->strDateTimePickerType == self::SHOW_DATE_TIME_SECONDS)) &&
                    ($this->dttDateTime->isDateNull())
                ) {
                    $blnIsNull = true;
                } else {
                    if ((($this->strDateTimePickerType == self::SHOW_TIME) ||
                            ($this->strDateTimePickerType == self::SHOW_TIME_SECONDS)) &&
                        ($this->dttDateTime->isTimeNull())
                    ) {
                        $blnIsNull = true;
                    }
                }
            }

            if ($blnIsNull) {
                if ($this->strName) {
                    $this->ValidationError = sprintf(t('%s is required'), $this->strName);
                } else {
                    $this->ValidationError = t('Required');
                }
                return false;
            }
        } else {
            if ((($this->strDateTimePickerType == self::SHOW_DATE) ||
                    ($this->strDateTimePickerType == self::SHOW_DATE_TIME) ||
                    ($this->strDateTimePickerType == self::SHOW_DATE_TIME_SECONDS)) &&
                ($this->intSelectedDay || $this->intSelectedMonth || $this->intSelectedYear) &&
                ($this->dttDateTime->isDateNull())
            ) {
                $this->ValidationError = t('Invalid Date');
                return false;
            }
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
            case "DateTime":
                if (is_null($this->dttDateTime) || $this->dttDateTime->isNull()) {
                    return null;
                } else {
                    $objToReturn = clone($this->dttDateTime);
                    return $objToReturn;
                }

            case "DateTimePickerType":
                return $this->strDateTimePickerType;
            case "DateTimePickerFormat":
                return $this->strDateTimePickerFormat;
            case "MinimumYear":
                return $this->intMinimumYear;
            case "MaximumYear":
                return $this->intMaximumYear;
            case "AllowBlankTime":
                return $this->blnAllowBlankTime;
            case "AllowBlankDate":
                return $this->blnAllowBlankDate;
            case "TimeSeparator":
                return $this->strTimeSeparator;
            case "SecondInterval":
                return $this->intSecondInterval;
            case "MinuteInterval":
                return $this->intMinuteInterval;
            case "HourInterval":
                return $this->intHourInterval;

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
    public function __set($strName, $mixValue)
    {
        $this->blnModified = true;

        switch ($strName) {
            // MISC
            case "DateTime":
                try {
                    $dttDate = Type::cast($mixValue, Type::DATE_TIME);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

                $this->intSelectedMonth = null;
                $this->intSelectedDay = null;
                $this->intSelectedYear = null;

                if (is_null($dttDate) || $dttDate->isNull()) {
                    $this->dttDateTime = null;
                } else {
                    $this->dttDateTime = $dttDate;
                }

                break;

            case "DateTimePickerType":
                try {
                    $this->strDateTimePickerType = Type::cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case "DateTimePickerFormat":
                try {
                    $this->strDateTimePickerFormat = Type::cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case "MinimumYear":
                try {
                    $this->intMinimumYear = Type::cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case "MaximumYear":
                try {
                    $this->intMaximumYear = Type::cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
            case "AllowBlankTime":
                try {
                    $this->blnAllowBlankTime = Type::cast($mixValue, Type::BOOLEAN);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case "AllowBlankDate":
                try {
                    $this->blnAllowBlankDate = Type::cast($mixValue, Type::BOOLEAN);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case "TimeSeparator":
                try {
                    $this->strTimeSeparator = Type::cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case "SecondInterval":
                try {
                    $this->intSecondInterval = Type::cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case "MinuteInterval":
                try {
                    $this->intMinuteInterval = Type::cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case "HourInterval":
                try {
                    $this->intHourInterval = Type::cast($mixValue, Type::INTEGER);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                    break;
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
        return new Q\Codegen\Generator\DateTimePicker(__CLASS__); // reuse the Table generator
    }
}
