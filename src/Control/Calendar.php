<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use Exception;
use QCubed\Event\Click;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\QDateTime;
use QCubed as Q;
use QCubed\Type;
use QCubed\Action\ActionBase as QAction;
use QCubed\Event\EventBase as QEvent;
use Throwable;

/**
 * Class Calendar
 *
 * This class will render a pop-up, modeless calendar control
 * that can be used to let the user pick a date.
 *
 * @package Controls
 * @property QDateTime MinDate
 * @property QDateTime MaxDate
 * @property QDateTime DefaultDate
 * @property int FirstDay
 * @property int|int[] NumberOfMonths
 * @property boolean AutoSize
 * @property boolean GotoCurrent
 * @property boolean IsRTL
 * @property string DateFormat
 * @property-write string DateTimeFormat
 * @property string JqDateFormat
 * @property boolean ShowButtonPanel
 * @package QCubed\Control
 */
class Calendar extends DateTimeTextBox
{
    protected string $strJavaScripts = QCUBED_JQUI_JS;
    protected string $strStyleSheets = QCUBED_JQUI_CSS;
    protected ?QDateTime $datMinDate = null;
    protected ?QDateTime $datMaxDate = null;
    protected ?QDateTime  $datDefaultDate = null;
    protected ?int $intFirstDay = null;
    protected mixed $mixNumberOfMonths = null;
    protected ?bool $blnAutoSize = false;
    protected ?bool $blnGotoCurrent = false;
    protected ?bool $blnIsRTL = false;
    protected bool $blnModified = false;
    protected string $strJqDateFormat = 'M d yy';
    protected bool $blnShowButtonPanel = true;

    // Map the JQuery datepicker format specs to QCubed \QCubed\QDateTime format specs.
    //QCubed	JQuery		PHP	Description
    //-------------------------------------------------
    //MMMM	    MM			F	Month as full name (e.g., March)
    //MMM	    M			M	Month as three-letters (e.g., Mar)
    //MM	    mm			m	Month as an integer with leading zero (e.g., 03)
    //M	        m			n	Month as an integer (e.g., 3)
    //DDDD	    DD			l	Day of week as full name (e.g., Wednesday)
    //DDD	    D			D	Day of week as three-letters (e.g., Wed)
    //DD	    dd			d	Day as an integer with leading zero (e.g., 02)
    //D	        d			j	Day as an integer (e.g., 2)
    //YYYY	    yy			Y	Year as a four-digit integer (e.g., 1977)
    //YY	    y			y	Year as a two-digit integer (e.g., 77)
    /** @var array QCubed to JQuery Map of date formates */
    private static array $mapQC2JQ = array(
        'MMMM' => 'MM',
        'MMM' => 'M',
        'MM' => 'mm',
        'M' => 'm',
        'DDDD' => 'DD',
        'DDD' => 'D',
        'DD' => 'dd',
        'D' => 'd',
        'YYYY' => 'yy',
        'YY' => 'y',
    );

    private static ?array $mapJQ2QC = null;

    /**
     * Converts a jQuery format string to a QCubed format string
     *
     * @param string $jqFrmt The jQuery format string to be converted
     *
     * @return string The converted QCubed format string
     */
    public static function qcFrmt(string $jqFrmt): string
    {
        if (!static::$mapJQ2QC) {
            static::$mapJQ2QC = array_flip(static::$mapQC2JQ);
        }

        return strtr($jqFrmt, (array)static::$mapJQ2QC);
    }

    /**
     * Converts a QC format string to a jQuery-compatible format string
     * using a predefined mapping.
     *
     * @param string $qcFrmt The QC format strings to be converted.
     *
     * @return string The jQuery-compatible format string.
     */
    public static function jqFrmt(string $qcFrmt): string
    {
        return strtr($qcFrmt, static::$mapQC2JQ);
    }

    /**
     * Converts a QDateTime object to a JavaScript date object
     *
     * @param QDateTime $dt The date and time object to be converted
     *
     * @return string The JavaScript date object representation
     */
    public static function jsDate(QDateTime $dt): string
    {
        return Q\Js\Helper::toJsObject($dt);
    }

    /**
     * Validates the current state or input
     *
     * @return bool True if the validation passes, otherwise false
     */
    public function validate(): bool
    {
        return true;
    }

    /**
     * Converts a PHP property to a JavaScript property string
     * This method generates a JavaScript property assignment string based on the given PHP property and key
     *
     * @param string $strProp The name of the PHP property to be converted
     * @param string $strKey The key to associate with the JavaScript property
     *
     * @return string A JavaScript property assignment string or an empty string if the property value is null
     */
    protected function makeJsProperty(string $strProp, string $strKey): string
    {
        $objValue = $this->$strProp;
        if (null === $objValue) {
            return '';
        }

        return $strKey . ': ' . Q\Js\Helper::toJsObject($objValue) . ', ';
    }

    /**
     * Returns the HTML for the control
     *
     * @return string The HTML, which can be sent to the browser
     * @throws Caller
     */
    public function getControlHtml(): string
    {
        $strToReturn = parent::getControlHtml();

        $strJqOptions = $this->makeJsProperty('ShowButtonPanel', 'showButtonPanel');
        $strJqOptions .= $this->makeJsProperty('JqDateFormat', 'dateFormat');
        $strJqOptions .= $this->makeJsProperty('AutoSize', 'autoSize');
        $strJqOptions .= $this->makeJsProperty('MaxDate', 'maxDate');
        $strJqOptions .= $this->makeJsProperty('MinDate', 'minDate');
        $strJqOptions .= $this->makeJsProperty('DefaultDate', 'defaultDate');
        $strJqOptions .= $this->makeJsProperty('FirstDay', 'firstDay');
        $strJqOptions .= $this->makeJsProperty('GotoCurrent', 'gotoCurrent');
        $strJqOptions .= $this->makeJsProperty('IsRTL', 'isRTL');
        $strJqOptions .= $this->makeJsProperty('NumberOfMonths', 'numberOfMonths');
        if ($strJqOptions) {
            $strJqOptions = substr($strJqOptions, 0, -2);
        }

        Application::executeJavaScript(
            sprintf('jQuery("#%s").datepicker({%s})', $this->strControlId, $strJqOptions));

        return $strToReturn;
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * PHP magic method
     *
     * @param string $strName
     *
     * @return mixed
     * @throws Exception|Caller
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case "MinDate":
                return $this->datMinDate;
            case "MaxDate":
                return $this->datMaxDate;
            case "DefaultDate":
                return $this->datDefaultDate;
            case "FirstDay":
                return $this->intFirstDay;
            case "GotoCurrent":
                return $this->blnGotoCurrent;
            case "IsRTL":
                return $this->blnIsRTL;
            case "NumberOfMonths":
                return $this->mixNumberOfMonths;
            case "AutoSize":
                return $this->blnAutoSize;
            case "DateFormat":
                return $this->strDateTimeFormat;
            case "JqDateFormat":
                return $this->strJqDateFormat;
            case "ShowButtonPanel":
                return $this->blnShowButtonPanel;
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
     * Sets the value of a property dynamically based on its name.
     * This method performs type casting and validates the value for specific properties.
     * It throws exceptions when the casting or the property assignment fails.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property.
     *
     * @return void
     *
     * @throws Caller Thrown for invalid or unrecognized property names.
     * @throws InvalidCast Thrown when the value cannot be cast to the required type.
     * @throws Throwable
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case "MinDate":
                try {
                    $this->datMinDate = Type::cast($mixValue, Type::DATE_TIME);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "MaxDate":
                try {
                    $this->datMaxDate = Type::cast($mixValue, Type::DATE_TIME);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "DefaultDate":
                try {
                    $this->datDefaultDate = Type::cast($mixValue, Type::DATE_TIME);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "FirstDay":
                try {
                    $this->intFirstDay = Type::cast($mixValue, Type::INTEGER);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "GotoCurrent":
                try {
                    $this->blnGotoCurrent = Type::cast($mixValue, Type::BOOLEAN);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "IsRTL":
                try {
                    $this->blnIsRTL = Type::cast($mixValue, Type::BOOLEAN);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "NumberOfMonths":
                if (!is_array($mixValue) && !is_numeric($mixValue)) {
                    throw new exception('NumberOfMonths must be an integer or an array');
                }
                $this->mixNumberOfMonths = $mixValue;
                $this->blnModified = true;
                break;
            case "AutoSize":
                try {
                    $this->blnAutoSize = Type::cast($mixValue, Type::BOOLEAN);
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "JqDateFormat":
                try {
                    $this->strJqDateFormat = Type::cast($mixValue, Type::STRING);
                    parent::__set('DateTimeFormat', static::qcFrmt($this->strJqDateFormat));
                    $this->blnModified = true;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "DateTimeFormat":
            case "DateFormat":
                parent::__set('DateTimeFormat', $mixValue);
                $this->strJqDateFormat = static::jqFrmt($this->strDateTimeFormat);
                $this->blnModified = true;
                break;
            case "ShowButtonPanel":
                try {
                    $this->blnShowButtonPanel = Type::cast($mixValue, Type::BOOLEAN);
                    $this->blnModified = true;
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
     * Adds an event to the calendar
     * It overrides the base method to make sure click events are not accepted
     *
     * @param QEvent $objEvent
     * @param QAction $objAction
     *
     * @throws Caller
     */
    public function addAction(QEvent $objEvent, QAction $objAction): void
    {
        if ($objEvent instanceof Click) {
            throw new Caller('QCalendar does not support click events');
        }
        parent::addAction($objEvent, $objAction);
    }
}
