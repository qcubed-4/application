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
use QCubed\Exception\InvalidCast;
use QCubed\QDateTime;
use QCubed\Type;
use QCubed as Q;


/**
 * This class is meant to be a date-picker.  It will essentially render an editable HTML textbox
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
 * @property string $TimeSeparator Character to separate the select boxes for an hour, minute and seconds
 * @property int $SecondInterval Seconds are shown in these intervals
 * @property int $MinuteInterval Minutes are shown in these intervals
 * @property int $HourInterval Hours are shown in these intervals
 */
class DateTimePicker extends Q\Project\Control\ControlBase
{
    /**
     *
     */
    public const SHOW_DATE = 'Date';
    public const SHOW_DATE_TIME = 'DateTime';

    public const SHOW_DATE_TIME_SECONDS = 'DateTimeSeconds';
    public const SHOW_TIME = 'Time';
    public const SHOW_TIME_SECONDS = 'TimeSeconds';

    public const MONTH_DAY_YEAR = 'MonthDayYear';
    public const DAY_MONTH_YEAR = 'DayMonthYear';
    public const YEAR_MONTH_DAY = 'YearMonthDay';

    /**
     * @var QDateTime|null
     */
    protected ?QDateTime $dttDateTime = null;
    protected string $strDateTimePickerType = self::SHOW_DATE;
    protected string $strDateTimePickerFormat = self::MONTH_DAY_YEAR;

    protected int $intMinimumYear = 1970;
    protected int $intMaximumYear = 2030;

    protected ?int $intSelectedMonth = null;
    protected ?int $intSelectedDay = null;
    protected ?int $intSelectedYear = null;

    /** @var bool Allow or Disallow Choosing '--' in the control UI for time */
    protected bool $blnAllowBlankTime = true;
    /** @var bool Allow or Disallow Choosing '--' in the control UI for date */
    protected bool $blnAllowBlankDate = true;
    /** @var string The character which appears between the hour, minutes and seconds */
    protected string $strTimeSeparator = ':';
    /** @var int Steps of intervals to show for the second field */
    protected int $intSecondInterval = 1;
    /** @var int Steps of intervals to show for minute field */
    protected int $intMinuteInterval = 1;
    /** @var int Steps of intervals to show for hour field */
    protected int $intHourInterval = 1;
    protected string $strCssClass = 'datetimepicker';

    /**
     * Constructor for the class.
     *
     * @param FormBase|ControlBase $objParent The parent object of the control.
     * @param string|null $strControlId Optional control ID to uniquely identify the control.
     * @return void
     * @throws Caller
     */
    public function __construct(FormBase|ControlBase $objParent, ?string $strControlId = null)
    {
        parent::__construct($objParent, $strControlId);
        $this->addJavascriptFile(QCUBED_JS_URL . '/date_time_picker.js');
    }

    /**
     * Parses the posted data related to a date-time picker and updates the internal date-time object accordingly.
     *
     * This method processes the posted values for the date and/or time fields, validates them against the picker type,
     * and updates the internal date-time object (`dttDateTime`) with the new values. If the picker type restricts to
     * either date or time only, the excluded part is cleared.
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function parsePostData(): void
    {
        $blnIsDateTimeSet = false;
        if ($this->dttDateTime == null) {
            $dttNewDateTime = QDateTime::now();
        } else {
            $blnIsDateTimeSet = true;
            $dttNewDateTime = clone $this->dttDateTime;
        }

        // --- Date part ---
        if (in_array($this->strDateTimePickerType, [self::SHOW_DATE, self::SHOW_DATE_TIME, self::SHOW_DATE_TIME_SECONDS])) {
            $strKey = $this->strControlId . '_lstMonth';
            $intMonth = array_key_exists($strKey, $_POST) ? $_POST[$strKey] : ($blnIsDateTimeSet ? $dttNewDateTime->Month : null);

            $strKey = $this->strControlId . '_lstDay';
            $intDay = array_key_exists($strKey, $_POST) ? $_POST[$strKey] : ($blnIsDateTimeSet ? $dttNewDateTime->Day : null);

            $strKey = $this->strControlId . '_lstYear';
            $intYear = array_key_exists($strKey, $_POST) ? $_POST[$strKey] : ($blnIsDateTimeSet ? $dttNewDateTime->Year : null);

            $this->intSelectedMonth = $intMonth;
            $this->intSelectedDay   = $intDay;
            $this->intSelectedYear  = $intYear;

            if (!empty($intYear) && !empty($intMonth) && !empty($intDay)) {
                $dttNewDateTime->setDate($intYear, $intMonth, $intDay);
            } else {
                $dttNewDateTime->Year = null;
            }
        }

        // In the date selector (SHOW_DATE) CLEAR the time part!
        if ($this->strDateTimePickerType === self::SHOW_DATE) {
            // Leave the time blank!
            $dttNewDateTime->Hour = null;
            $dttNewDateTime->Minute = null;
            $dttNewDateTime->Second = null;
        }

        // --- Time part ---
        if (in_array($this->strDateTimePickerType, [self::SHOW_TIME, self::SHOW_TIME_SECONDS, self::SHOW_DATE_TIME, self::SHOW_DATE_TIME_SECONDS])) {
            $strKey = $this->strControlId . '_lstHour';
            $intHour = array_key_exists($strKey, $_POST) ? $_POST[$strKey] : ($blnIsDateTimeSet ? $dttNewDateTime->Hour : null);

            $strKey = $this->strControlId . '_lstMinute';
            $intMinute = array_key_exists($strKey, $_POST) ? $_POST[$strKey] : ($blnIsDateTimeSet ? $dttNewDateTime->Minute : null);

            $intSecond = 0;
            if (in_array($this->strDateTimePickerType, [self::SHOW_TIME_SECONDS, self::SHOW_DATE_TIME_SECONDS])) {
                $strKey = $this->strControlId . '_lstSecond';
                $intSecond = array_key_exists($strKey, $_POST) ? $_POST[$strKey] : ($blnIsDateTimeSet ? $dttNewDateTime->Second : null);
            }

            if (!empty($intHour) && !empty($intMinute) && $intHour !== null && $intMinute !== null) {
                $dttNewDateTime->setTime($intHour, $intMinute, $intSecond);
            } else {
                $dttNewDateTime->Hour = null;
                $dttNewDateTime->Minute = null;
                $dttNewDateTime->Second = null;
            }
        } else if ($this->strDateTimePickerType === self::SHOW_DATE) {
            // If only a date selection, the time is always empty (as a fallback if the logic ignored the previous place)
            $dttNewDateTime->Hour = null;
            $dttNewDateTime->Minute = null;
            $dttNewDateTime->Second = null;
        }

        $this->dttDateTime = $dttNewDateTime;
    }

    /**
     * Generates and returns the HTML for the control, including the appropriate attributes,
     * CSS classes, and style rules. This method renders the necessary HTML for date and time
     * pickers based on configurations such as date type, required fields, allowed blank dates,
     * and intervals for time selections.
     *
     * The method handles the generation of combinations of dropdowns for months, days, years,
     * hours, and minutes as required by the control's configuration. It adheres to specific
     * formats and ordering of date and time components based on the picker type (e.g., date only,
     * date and time, etc.).
     *
     * Various control options, including whether the fields are required, the ranges for
     * selectable years, and intervals for time components, are taken into account while
     * preparing these outputs.
     *
     * @return string The fully constructed HTML representation of the control.
     */
    protected function getControlHtml(): string
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

        $strCommand = sprintf(' onchange="QCubed__DateTimePicker_Change(\'%s\', this);"', $this->strControlId);

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
                        '<option value="%s" %s>%s</option>',
                        $intMonth,
                        $strSelected,
                        $dateObj->format('M')
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
                        // New DateTime -- but we are NOT required to See if a month has been selected yet.
                        if ($this->intSelectedMonth) {
                            $intSelectedYear = $this->intSelectedYear ?? 2000;
                            $intDaysInMonth = date('t', mktime(0, 0, 0, $this->intSelectedMonth, 1, $intSelectedYear));
                            for ($intDay = 1; $intDay <= $intDaysInMonth; $intDay++) {
                                if (($dttDateTime->Day == $intDay) || ($this->intSelectedDay == $intDay)) {
                                    $strSelected = ' selected="selected"';
                                } else {
                                    $strSelected = '';
                                }
                                $strDayListbox .= sprintf('<option value="%s" %s>%s</option>',
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
                        $strDayListbox .= sprintf('<option value="%s" %s>%s</option>',
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
                    if (!$dttDateTime->isDateNull() &&
                    (($dttDateTime->Year == $intYear) || ($this->intSelectedYear == $intYear))) {
                        $strSelected = ' selected="selected"';
                    } else {
                        $strSelected = '';
                    }

                    $strYearListbox .= sprintf('<option value="%s" %s>%s</option>', $intYear, $strSelected, $intYear);
                }

                $strYearListbox .= '</select>';

                // Put it all together
                $strToReturn .= match ($this->strDateTimePickerFormat) {
                    self::MONTH_DAY_YEAR => $strMonthListbox . $strDayListbox . $strYearListbox,
                    self::DAY_MONTH_YEAR => $strDayListbox . $strMonthListbox . $strYearListbox,
                    self::YEAR_MONTH_DAY => $strYearListbox . $strMonthListbox . $strDayListbox
                };
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
                    $strHourListBox .= sprintf('<option value="%s" %s>%s</option>',
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
                    $strMinuteListBox .= sprintf('<option value="%s" %s>%02d</option>',
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
                    $strSecondListBox .= sprintf('<option value="%s" %s>%02d</option>',
                        $intSecond,
                        $strSelected,
                        $intSecond);
                }
                $strSecondListBox .= '</select>';


                // Putting it all together
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
        return sprintf('<span id="%s" %s>%s</span>', $this->strControlId, $strCssClass, $strToReturn);
    }

    /**
     * Validates the control based on the defined requirements and selected values.
     *
     * @return bool True if the validation passes, false otherwise.
     */
    public function validate(): bool
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

    /**
     * Magic getter method to retrieve the value of a property.
     *
     * @param string $strName The name of the property to retrieve.
     * @return mixed The value of the requested property, or the value returned by the parent implementation.
     * @throws Caller If the property does not exist or an exception occurs in the parent implementation.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            // MISC
            case "DateTime":
                return ($this->dttDateTime instanceof QDateTime) ? $this->dttDateTime : null;
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

    /**
     * Magic method to set the value of a property.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property.
     * @return void
     * @throws InvalidCast Thrown if the given value cannot be cast to the expected type.
     * @throws Caller Thrown if the property does not exist or cannot be dynamically set.
     */
    public function __set(string $strName, mixed $mixValue): void
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
    public static function getCodeGenerator(): Q\Codegen\Generator\DateTimePicker
    {
        return new Q\Codegen\Generator\DateTimePicker(__CLASS__); // reuse the Table generator
    }
}
