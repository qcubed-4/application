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
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\QDateTime;
use QCubed as Q;
use QCubed\Type;
use QCubed\Action\ActionBase as QAction;
use QCubed\Event\EventBase as QEvent;

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
 * @was QCalendar
 * @package QCubed\Control
 */
class Calendar extends DateTimeTextBox
{
    protected $strJavaScripts = QCUBED_JQUI_JS;
    protected $strStyleSheets = QCUBED_JQUI_CSS;
    protected $datMinDate = null;
    protected $datMaxDate = null;
    protected $datDefaultDate = null;
    protected $intFirstDay = null;
    protected $mixNumberOfMonths = null;
    protected $blnAutoSize = false;
    protected $blnGotoCurrent = false;
    protected $blnIsRTL = false;
    protected $blnModified = false;
    protected $strJqDateFormat = 'M d yy';
    protected $blnShowButtonPanel = true;

    // map the JQuery datepicker format specs to QCubed \QCubed\QDateTime format specs.
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
    private static $mapQC2JQ = array(
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
    private static $mapJQ2QC = null;

    public static function qcFrmt($jqFrmt)
    {
        if (!static::$mapJQ2QC) {
            static::$mapJQ2QC = array_flip(static::$mapQC2JQ);
        }

        return strtr($jqFrmt, static::$mapJQ2QC);
    }

    public static function jqFrmt($qcFrmt)
    {
        return strtr($qcFrmt, static::$mapQC2JQ);
    }

    /**
     * @deprecated Use \QCubed\Js\Helper::toJsObject
     * @param QDateTime $dt
     * @return string
     */
    public static function jsDate(QDateTime $dt)
    {
        return Q\Js\Helper::toJsObject($dt);
    }

    /**
     * Validates the control (default: returns true)
     *
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    protected function makeJsProperty($strProp, $strKey)
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
     * @return string The HTML which can be sent to browser
     */
    public function getControlHtml()
    {
        $strToReturn = parent::getControlHtml();

        $strJqOptions = '';
        $strJqOptions .= $this->makeJsProperty('ShowButtonPanel', 'showButtonPanel');
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
    public function __get($strName)
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
     * PHP magic method
     *
     * @param string $strName
     * @param string $mixValue
     *
     * @return void
     * @throws Exception|Caller|InvalidCast|Exception
     */
    public function __set($strName, $mixValue)
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
    public function addAction(QEvent $objEvent, QAction $objAction)
    {
        if ($objEvent instanceof QClickEvent) {
            throw new Caller('QCalendar does not support click events');
        }
        parent::addAction($objEvent, $objAction);
    }
}
