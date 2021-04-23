<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Jqui;

use QCubed\Control\Calendar;
use QCubed\Control\ControlBase;
use QCubed\Control\FormBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\QDateTime;
use QCubed\Type;

/**
 * Class DatepickerBase
 *
 * Impelements a JQuery UI Datepicker
 *
 * The QDatepickerBase class defined here provides an interface between the generated
 * QDatepickerGen class, and QCubed. This file is part of the core and will be overwritten
 * when you update QCubed. To override, make your changes to the QDatepicker.class.php file instead.
 *
 * A Datepicker is a field that is designed to just allow dates, and to popup a calendar for picking dates.
 *
 * @property string $DateFormat            The format to use for displaying the date in the field
 * @property string $DateTimeFormat        Alias for DateFormat
 * @property QDateTime $DateTime        The date to set the field to
 * @property mixed $Minimum                Alias for MinDate
 * @property mixed $Maximum                Alias for MaxDate
 * @property string $Text                Textual date to set it to
 *
 * @link http://jqueryui.com/datepicker/
 * @was QDatepickerBase
 * @package QCubed\Jqui
 */
class DatepickerBase extends DatepickerGen
{
    /** @var string Default datetime format for the picker */
    protected $strDateTimeFormat = "MM/DD/YYYY";    // same as default for JQuery UI control
    /** @var QDateTime variable to hold the date time to be selected (or already selected) */
    protected $dttDateTime;    // default to no selection

    /**
     * @param ControlBase|FormBase $objParentObject
     * @param null|string $strControlId
     *
     * @throws Caller|InvalidCast
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        parent::__set('OnSelect', new \QCubed\Js\Closure($this->OnSelectJs(), array('dateText','inst')));    // setup a way to detect a selection
    }

    /**
     * Output JS that will record changes to the datepicker and fire our own select event.
     */
    protected function OnSelectJs()
    {
        $strId = $this->getJqControlId();
        $strJS = sprintf('qcubed.recordControlModification("%s", "_Text", dateText); $j("#%s").trigger("QDatepicker_Select2")',
            $strId, $strId);
        return $strJS;
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * @param string $strName
     *
     * @return mixed|null|string
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            // MISC
            case "Maximum":
                return $this->MaxDate;
            case "Minimum":
                return $this->MinDate;
            case 'DateTimeFormat':
            case 'DateFormat':
                return $this->strDateTimeFormat;
            case 'DateTime':
                return $this->dttDateTime ? clone($this->dttDateTime) : null;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->IncrementOffset();
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
     * @param string $strName Property name
     * @param string $mixValue Property value
     *
     * @return mixed|void
     * @throws Caller|InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case 'MaxDate':
            case 'Maximum':
                if (is_string($mixValue)) {
                    if (preg_match('/[+-][0-9]+[dDwWmMyY]/', $mixValue)) {
                        parent::__set($strName, $mixValue);
                        break;
                    }
                    $mixValue = new QDateTime($mixValue);
                }
                parent::__set('MaxDate', Type::Cast($mixValue, Type::DATE_TIME));
                break;

            case 'MinDate':
            case 'Minimum':
                if (is_string($mixValue)) {
                    if (preg_match('/[+-][0-9]+[dDwWmMyY]/', $mixValue)) {
                        parent::__set($strName, $mixValue);
                        break;
                    }
                    $mixValue = new QDateTime($mixValue);
                }
                parent::__set('MinDate', Type::Cast($mixValue, Type::DATE_TIME));
                break;

            case 'DateTime':
                try {
                    $this->dttDateTime = new QDateTime($mixValue, null, QDateTime::DATE_ONLY_TYPE);
                    parent::SetDate($this->dttDateTime);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case 'JqDateFormat':
                try {
                    parent::__set($strName, $mixValue);
                    $this->strDateTimeFormat = Calendar::qcFrmt($this->JqDateFormat);
                    // trigger an update to reformat the text with the new format
                    $this->DateTime = $this->dttDateTime;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case 'DateTimeFormat':
            case 'DateFormat':
                try {
                    $this->strDateTimeFormat = Type::Cast($mixValue, Type::STRING);
                    parent::__set('JqDateFormat', Calendar::jqFrmt($this->strDateTimeFormat));
                    // trigger an update to reformat the text with the new format
                    $this->DateTime = $this->dttDateTime;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case 'Text':    // Set the selected date with a text value
                $this->DateTime = $mixValue;
                break;

            case '_Text':    // Internal only. Do not use. Called by JS above to keep track of user selection.
                $this->dttDateTime = new QDateTime($mixValue);
                break;

            case 'OnSelect':
                // Since we are using the OnSelect event already, and Datepicker doesn't allow binding, so there can be
                // only one event, we will make sure our js is part of any new OnSelect js.
                $strJS = $this->OnSelectJs() . ';' . $mixValue;
                $objClosure = new \QCubed\Js\Closure($strJS, array('dateText','inst'));
                parent::__set('OnSelect', $objClosure);
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
        }
    }

    /* === Codegen Helpers, used during the Codegen process only. === */

    /**
     * Returns the variable name for a control of this type during code generation process
     *
     * @param string $strPropName Property name for which the control to be generated is being generated
     *
     * @return string
     */
    public static function Codegen_VarName($strPropName)
    {
        return 'cal' . $strPropName;
    }


}