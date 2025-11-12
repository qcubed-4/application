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
        protected ?int $dttMinimum = null;
        protected ?int $dttMaximum = null;
        protected string $strDateTimeFormat = "MMM D, YYYY";
        protected ?QDateTime $dttDateTime = null;

        protected ?string $strLabelForInvalid = 'For example, "Mar 20, 4:30pm" or "Mar 20"';
        protected mixed $calLinkedControl;

        //////////
        // Methods
        //////////

        /**
         * Parses the POST data to update the control's value and extracts the datetime value.
         *
         * @return void
         * @throws \Exception
         */
        public function parsePostData(): void
        {
            // Check to see if this Control's Value was passed in via the POST data
            if (array_key_exists($this->strControlId, $_POST)) {
                parent::parsePostData();
                $this->dttDateTime = self::parseForDateTimeValue($this->strText);
            }
        }

        /**
         * Parses a given text input and attempts to extract and convert it into a QDateTime value.
         *
         * @param string $strText The string containing a potential date and/or time value.
         *
         * @return QDateTime|null Returns a QDateTime object if the text contains a valid date or date-time value,
         *                        or null if the text does not represent a valid date.
         * @throws \Exception
         */
        public static function parseForDateTimeValue(string $strText): ?QDateTime
        {
            // Trim and Clean
            $strText = strtolower(trim($strText));
            while (str_contains($strText, '  ')) {
                $strText = str_replace('  ', ' ', $strText);
            }
            //$strText = str_replace('.', '', $strText);
            $strText = str_replace('@', ' ', $strText);

            // Are we ATTEMPTING to parse a Time value?
            if ((!str_contains($strText, ':')) &&
                (!str_contains($strText, 'am')) &&
                (!str_contains($strText, 'pm'))
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
            if ((str_contains($strText, 'pm')) &&
                (!str_contains($strText, ':'))
            ) {
                $strText = str_replace(' pm', ':00 pm', $strText, $intCount);
                if (!$intCount) {
                    $strText = str_replace('pm', ':00 pm', $strText, $intCount);
                }
            } else {
                if ((str_contains($strText, 'am')) &&
                    (!str_contains($strText, ':'))
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

        /**
         * Validates the control's value against the specified date and time constraints, ensuring it falls within
         * the allowable range. Adds an appropriate validation error message if the validation fails.
         *
         * @return bool Returns true if the value is valid, otherwise false.
         * @throws \Exception
         */
        public function validate(): bool
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
                            $strError = t('before ') . $dttToCompare;
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
                            $strError = t('after ') . $dttToCompare;
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

        /**
         * Magic getter method to retrieve property values based on the property name.
         *
         * @param string $strName Name of the property to retrieve.
         *
         * @return mixed The value of the requested property.
         * @throws Caller If the property does not exist.
         * @throws \Exception
         */
        public function __get(string $strName): mixed
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
         * Sets the value of a property dynamically, based on the given name and value, and updates related fields.
         *
         * @param string $strName The name of the property being set.
         * @param mixed $mixValue The value to assign to the property.
         *
         * @return void
         * @throws InvalidCast Throws when the value cannot be cast to the expected type.
         * @throws Caller Throws when an invalid property name is provided.
         * @throws \Exception
         * @throws \Throwable
         */
        public function __set(string $strName, mixed $mixValue): void
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
        public static function codegen_VarName(string $strPropName): string
        {
            return 'cal' . $strPropName;
        }
    }
