<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Jqui;

    require_once(dirname(__DIR__, 2) . '/i18n/i18n-lib.inc.php');

    use DateMalformedStringException;
    use QCubed\Control\Calendar;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
    use QCubed\Type;
    use QCubed as Q;
    use Throwable;

    /**
     * Class DatepickerBoxBase
     *
     * The QDatepickerBoxBase class defined here provides an interface between the generated
     * QDatepickerBoxGen class and QCubed. This file is part of the core and will be overwritten
     * when you update QCubed. To override, make your changes to the QDatepickerBox.class.php file instead.
     *
     * A Datepicker Box is similar to a Datepicker, but it's not associated with a field. It
     * just displays a calendar for picking a date.
     *
     * @property string $DateFormat             The format to use for displaying the date
     * @property string $DateTimeFormat         Alias for DateFormat
     * @property QDateTime $DateTime               The date to set the field to
     * @property mixed $Minimum                Alias for MinDate
     * @property mixed $Maximum                Alias for MaxDate
     * @property string $Text                   Textual date to set it to
     * @property-write string $MinDateErrorMsg  Message to display if we are before the minimum date
     * @property-write string $MaxDateErrorMsg  Message to display if we are after the maximum date
     * @link    http://jqueryui.com/datepicker/#inline
     * @package QCubed\Jqui
     */
    class DatepickerBoxBase extends DatepickerBoxGen
    {
        /** @var string Format for the datetime to pick */
        protected string $strDateTimeFormat = "MM/DD/YYYY"; // matches default of JQuery UI control
        /** @var QDateTime|null variable to store the picked value */
        protected ?QDateTime $dttDateTime = null;
        /** @var  string|null */
        protected ?string $strMinDateErrorMsg = null;
        /** @var  string|null */
        protected ?string $strMaxDateErrorMsg = null;

        /**
         * Parse the data posted back via the control.
         * This function basically tests for the Crossscripting rules applied to the TextBox
         * @throws Caller
         * @throws \DateMalformedStringException
         */
        public function parsePostData(): void
        {
            // Check to see if this Control's Value was passed in via the POST data
            if (array_key_exists($this->strControlId, $_POST)) {
                parent::parsePostData();
                $this->dttDateTime = new QDateTime($this->strText, null, QDateTime::DATE_ONLY_TYPE);
                if ($this->dttDateTime->isNull()) {
                    $this->dttDateTime = null;
                }
            }
        }

        /**
         * Validate the control.
         * @return bool
         * @throws Caller
         * @throws DateMalformedStringException
         */
        public function validate(): bool
        {
            if (!parent::validate()) {
                return false;
            }

            if ($this->strText != '') {
                $dttDateTime = new QDateTime($this->strText, null, QDateTime::DATE_ONLY_TYPE);
                if ($dttDateTime->isDateNull()) {
                    $this->ValidationError = t("Invalid date");
                    return false;
                }
                if (!is_null($this->Minimum)) {
                    if ($dttDateTime->isEarlierThan($this->Minimum)) {
                        if ($this->strMinDateErrorMsg) {
                            $this->ValidationError = $this->strMinDateErrorMsg;
                        } else {
                            $this->ValidationError = t("Date is earlier than the minimum allowed");
                        }
                        return false;
                    }
                }

                if (!is_null($this->Maximum)) {
                    if ($dttDateTime->isLaterThan($this->Maximum)) {
                        if ($this->strMaxDateErrorMsg) {
                            $this->ValidationError = $this->strMaxDateErrorMsg;
                        } else {
                            $this->ValidationError = t("Date is later than the maximum allowed");
                        }
                        return false;
                    }
                }
            }
            return true;
        }

        /////////////////////////
        // Public Properties: GET
        /////////////////////////
        /**
         * PHP magic method implementation
         *
         * @param string $strName
         *
         * @return mixed
         * @throws Caller
         * @throws \Exception
         */
        public function __get(string $strName): mixed
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
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
            }
        }

        /////////////////////////
        // Public Properties: SET
        /////////////////////////
        /**
         * PHP magic method implementation
         *
         * @param string $strName Property name
         * @param mixed $mixValue Property value to be set
         *
         * @throws Caller
         * @throws DateMalformedStringException
         * @throws InvalidCast
         * @throws Throwable
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case 'MaxDate':
                case 'Maximum':
                    try {
                        if (is_string($mixValue)) {
                            if (preg_match('/[+-][0-9]+[dDwWmMyY]/', $mixValue)) {
                                parent::__set($strName, $mixValue);
                                break;
                            }
                        }
                        parent::__set('MaxDate',
                            new QDateTime ($mixValue, null, QDateTime::DATE_ONLY_TYPE));
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;

                case 'MinDate':
                case 'Minimum':
                    try {
                        if (is_string($mixValue)) {
                            if (preg_match('/[+-][0-9]+[dDwWmMyY]/', $mixValue)) {
                                parent::__set($strName, $mixValue);
                                break;
                            }
                        }
                        parent::__set('MinDate',
                            new QDateTime ($mixValue, null, QDateTime::DATE_ONLY_TYPE));
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;

                case 'DateTime':
                    try {
                        $this->dttDateTime = new QDateTime($mixValue, null, QDateTime::DATE_ONLY_TYPE);
                        if ($this->dttDateTime->isNull()) {
                            $this->dttDateTime = null;
                            $this->blnModified = true;
                        }
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

                case 'JqDateFormat':
                    try {
                        parent::__set($strName, $mixValue);
                        $this->strDateTimeFormat = Calendar::qcFrmt($this->JqDateFormat);
                        // trigger an update to reformat the text with the new format
                        $this->DateTime = $this->dttDateTime;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;

                case 'DateTimeFormat':
                case 'DateFormat':
                    try {
                        $this->strDateTimeFormat = Type::cast($mixValue, Type::STRING);
                        parent::__set('JqDateFormat', Calendar::jqFrmt($this->strDateTimeFormat));
                        // trigger an update to reformat the text with the new format
                        $this->DateTime = $this->dttDateTime;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;

                case 'Text':
                    parent::__set($strName, $mixValue);
                    $this->dttDateTime = new QDateTime($this->strText, null, QDateTime::DATE_ONLY_TYPE);
                    break;

                case 'MinDateErrorMsg':
                    $this->strMinDateErrorMsg = Type::cast($mixValue, Type::STRING);
                    break;

                case 'MaxDateErrorMsg':
                    $this->strMaxDateErrorMsg = Type::cast($mixValue, Type::STRING);
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
         * @return Q\ModelConnector\Param[]
         * @throws Caller
         */
        public static function getModelConnectorParams(): array
        {
            return array_merge(parent::getModelConnectorParams(), array(
                new Q\ModelConnector\Param (get_called_class(), 'DateFormat', 'How to format the date. Default: MM/DD/YY',
                    Type::STRING)
            ));
        }

    }