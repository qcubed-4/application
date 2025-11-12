<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Jqui;

    use QCubed as Q;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Application;
    use QCubed\Type;

    /**
     * Class SortableBase
     *
     * The SortableBase class defined here provides an interface between the generated
     * SortableGen class and QCubed. This file is part of the core and will be overwritten
     * when you update QCubed. To override, make your changes to the Sortable.class.php file instead.
     *
     * Sortable is a group of panels that can be dragged to reorder them. You will need to put
     * some care into the CSS styling of the objects so that the CSS allows them to be moved. It
     * will use the top level HTML objects inside the panel to decide what to sort. Make sure
     * they have IDs so they can return the IDs of the items in sort order.
     *
     * @property-read array $ItemArray    List of ControlIds in sort order.
     *
     * @link http://jqueryui.com/sortable/
     * @package QCubed\Jqui
     */
    class SortableBase extends SortableGen
    {
        /** @var array|null */
        protected ?array $aryItemArray = null;


        // Find out what the sort order is at the beginning so that aryItemArray is up to date

        /**
         * Prepares and returns an array of jQuery UI options for the control.
         *
         * @return array The array of jQuery UI options, including any custom configurations or event handlers.
         */
        public function makeJqOptions(): array
        {
            $jqOptions = parent::makeJqOptions();

            // TODO: Put this in the qcubed.js file, or something like it.
            $jqOptions['create'] = new Q\Js\Closure('
					var ary = jQuery(this).sortable("toArray");
						var str = ary.join(",");
			 			qcubed.recordControlModification("$this->ControlId", "_ItemArray", str);
				');
            return $jqOptions;
        }

        /**
         * Generates and returns the JavaScript code to be executed at the end of the control's lifecycle.
         *
         * @return string The JavaScript code to be executed, including event handling for a sortable control modification.
         * @throws Caller
         */
        public function getEndScript(): string
        {
            $strJS = parent::getEndScript();

            $strCtrlJs = <<<FUNC
			;\$j('#$this->ControlId').on("sortstop", function (event, ui) {
						var ary = jQuery(this).sortable("toArray");
						var str = ary.join(",");
			 			qcubed.recordControlModification("$this->ControlId", "_ItemArray", str);
					})						
FUNC;
            Application::executeJavaScript($strCtrlJs, Q\ApplicationBase::PRIORITY_HIGH);

            return $strJS;
        }

        /**
         * Sets the value of a property dynamically.
         *
         * @param string $strName The name of the property to set.
         * @param string $mixValue The value to assign to the property.
         *
         * @return void
         * @throws InvalidCast If the value cannot be cast to the required type.
         * @throws Caller If the property name is invalid or cannot be set.
         * @throws \Exception
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case '_ItemArray': // Internal only. Do not use. Used by JS above to track selections.
                    try {
                        $data = Type::cast($mixValue, Type::STRING);
                        $a = explode(",", $data);
                        $this->aryItemArray = $a;
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }

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
         * Handles the retrieval of property values dynamically.
         *
         * This method attempts to fetch the value of a given property by its name.
         * If the property name matches predefined conditions, the corresponding value is returned.
         * Otherwise, it delegates the retrieval to the parent class or handles exceptions appropriately.
         *
         * @param string $strName The name of the property being accessed.
         *
         * @return mixed The value of the requested property or an exception if the property does not exist.
         * @throws Caller
         * @throws \Exception
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case 'ItemArray':
                    return $this->aryItemArray;
                default:
                    try {
                        return parent::__get($strName);
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
            }
        }
    }
