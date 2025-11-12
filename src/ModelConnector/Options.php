<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\ModelConnector;

    use QCubed\ObjectBase;

    /**
     * Interface to the ModelConnector options that let you specify various options per a field to be placed in the codegen
     * ModelConnectorGen classes.
     *
     * Note that this ties table and field names in the database to options iModelCruiseControl. If the table or field name
     * changes in the database, the options will be lost. We can try to guess as to whether changes were made based upon
     * the index of the changes in the field list, but not entirely easy to do. Best would be for a developer to hand-code
     * the changes in the JSON file in this case.
     *
     * This will be used by the designer to record the changes in preparation for codegen.
     *
     * @package QCubed\ModelConnector
     */
    class Options extends ObjectBase
    {
        const string FORMGEN_BOTH = 'both';
        const string FORMGEN_LABEL_ONLY = 'label'; //Generate only a label
        const string FORMGEN_CONTROL_ONLY = 'control'; //Generate only a control
        const string FORMGEN_NONE = 'none'; // Do not generate anything for this database object

        const int CREATE_OR_EDIT = 1; // Do not generate anything for this database object
        const int CREATE_ON_RECORD_NOT_FOUND = 2;
        const int EDIT_ONLY = 3;

        protected array $options = array();
        protected ?bool $blnChanged = false;

        const string TABLE_OPTIONS_FIELD_NAME = '*';

        public function __construct()
        {
            if (file_exists(QCUBED_CONFIG_DIR . '/codegen_options.json')) {
                $strContent = file_get_contents(QCUBED_CONFIG_DIR . '/codegen_options.json');

                if ($strContent) {
                    $this->options = json_decode($strContent, true);
                }
            }

            // TODO: Analyze the result for changes and make a guess as to whether a table name or field name was changed
        }

        /**
         * Saves the current options to a configuration file if changes have been made.
         *
         * @return void
         */
        public function save(): void
        {
            if (!$this->blnChanged) {
                return;
            }
            $flags = 0;
            if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
                $flags = JSON_PRETTY_PRINT;
            }
            $strContent = json_encode($this->options, $flags);

            file_put_contents(QCUBED_CONFIG_DIR . '/codegen_options.json', $strContent);
            $this->blnChanged = false;
        }

        /**
         * Destructor method to handle cleanup operations.
         *
         * Calls the save() method to ensure any necessary data is persisted
         * before the object is destroyed.
         *
         * @return void
         */
        public function __destruct()
        {
            $this->save();
        }


        /**
         * Set an option value.
         *
         * @param string $strTableName The name of the table to configure.
         * @param string $strFieldName The name of the field within the table.
         * @param string $strOptionName The name of the option to be set.
         * @param mixed $mixValue The value to assign to the specified option.
         * @return void
         */
        public function setOption(string $strTableName, string $strFieldName, string $strOptionName, mixed $mixValue): void
        {
            $this->options[$strTableName][$strFieldName][$strOptionName] = $mixValue;
            $this->blnChanged = true;
        }

        /**
         * Sets or updates an option value. Removes the option if the provided value is empty.
         *
         * @param string $strClassName The name of the class associated with the option.
         * @param string $strFieldName The name of the field associated with the option.
         * @param mixed $mixValue The value to set for the specified option.
         * @return void
         */
        public function setOptions(string $strClassName, string $strFieldName, mixed $mixValue): void
        {
            if (empty($mixValue)) {
                unset($this->options[$strClassName][$strFieldName]);
            } else {
                $this->options[$strClassName][$strFieldName] = $mixValue;
            }
            $this->blnChanged = true;
        }

        /**
         * Unset an option.
         *
         * @param string $strClassName Name of the class.
         * @param string $strFieldName Name of the field.
         * @param string $strOptionName Name of the option to be unset.
         * @return void
         */
        public function unsetOption(string $strClassName, string $strFieldName, string $strOptionName): void
        {
            unset($this->options[$strClassName][$strFieldName][$strOptionName]);
            $this->blnChanged = true;
        }

        /**
         * Retrieves the value of a specific option for the given class, field, and option name.
         *
         * @param string $strClassName The name of the class associated with the option.
         * @param string $strFieldName The name of the field associated with the option.
         * @param string $strOptionName The specific option name to retrieve.
         * @return mixed The value of the specified option if it exists, otherwise null.
         */
        public function getOption(string $strClassName, string $strFieldName, string $strOptionName): mixed
        {
            return $this->options[$strClassName][$strFieldName][$strOptionName] ?? null;
        }

        /**
         * Retrieves the options associated with a given class and field.
         *
         * @param string $strClassName The name of the class for which options are retrieved.
         * @param string $strFieldName The name of the field for which options are retrieved.
         * @return array The options for the specified class and field, or an empty array if none exist.
         */
        public function getOptions(string $strClassName, string $strFieldName): array
        {
            return $this->options[$strClassName][$strFieldName] ?? array();
        }
    }
