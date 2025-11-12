<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Jqui;

    use QCubed\ApplicationBase;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Project\Application;
    use QCubed\Type;

    /**
     * Class SelectableBase
     *
     * The SelectableBase class defined here provides an interface between the generated
     * SelectableGen class and QCubed. This file is part of the core and will be overwritten
     * when you update QCubed. To override, make your changes to the Selectable.php file instead.
     *
     * A selectable box makes the items inside of it selectable. This is a QPanel, so
     * whatever top level items drown inside of it will become selectable. Make sure
     * the items have IDs.
     *
     * @property array $SelectedItems ControlIds of the items selected
     *
     * @link http://jqueryui.com/selectable/
     * @package QCubed\Jqui
     */
    class SelectableBase extends SelectableGen
    {
        /** @var array|null */
        protected ?array $arySelectedItems = null;


        // These functions are used to keep track of the selected items

        /**
         * Attaches the JQueryUI widget to the HTML object if a widget is specified.
         */
        protected function makeJqWidget(): void
        {
            parent::makeJqWidget();

            Application::executeJsFunction('qcubed.selectable', $this->getJqControlId(), ApplicationBase::PRIORITY_HIGH);
        }


        /**
         * Sets a property value based on the given property name.
         *
         * This method is a magic PHP method used to dynamically set the value of a
         * property using the specified name and value. The property names are case-sensitive
         * and can include attributes like Text, Format, Template, TagName, among others.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the specified property.
         *
         * @return void
         *
         * @throws InvalidCast If the type of the value does not match the property's expected type.
         * @throws Caller If the property name is invalid or the template file does not exist.
         * @throws \Exception
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case '_SelectedItems':    // Internal only. Do not use. Used by JS above to keep track of selections.
                    try {
                        $strItems = Type::cast($mixValue, Type::STRING);
                        $this->arySelectedItems = explode(",", $strItems);
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;

                case 'SelectedItems':
                    // Set the selected items to an array of object IDs
                    try {
                        $aValues = Type::cast($mixValue, Type::ARRAY_TYPE);
                        $aJqIds = array();
                        foreach ($aValues as $val) {
                            $aJqIds[] = '"#' . $val . '"';
                        }
                        $strJqItems = join(',', $aJqIds);

                        $strJS = <<<FUNC
							var item = jQuery("#$this->ControlId");
							
							jQuery(".ui-selectee", item).each(function() {
								jQuery(this).removeClass('ui-selected');
							});
							
							jQuery($strJqItems).each(function() {
								jQuery(this).addClass('ui-selected');
							});
FUNC;
                        $this->arySelectedItems = $aValues;
                        Application::executeJavascript($strJS);
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
         * PHP __get magic method implementation
         *
         * @param string $strName Name of the property
         *
         * @return mixed
         * @throws Caller
         * @throws \Exception
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case 'SelectedItems':
                    return $this->arySelectedItems;

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
