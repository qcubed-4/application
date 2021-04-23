<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Jqui;

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class SelectableBase
 *
 * The SelectableBase class defined here provides an interface between the generated
 * SelectableGen class, and QCubed. This file is part of the core and will be overwritten
 * when you update QCubed. To override, make your changes to the Selectable.php file instead.
 *
 * A selectable box makes the items inside of it selectable. This is a QPanel, so
 * whatever top level items drown inside of it will become selectable. Make sure
 * the items have ids.
 *
 * @property array $SelectedItems ControlIds of the items selected
 *
 * @link http://jqueryui.com/selectable/
 * @was QSelectableBase
 * @package QCubed\Jqui
 */
class SelectableBase extends SelectableGen
{
    /** @var array */
    protected $arySelectedItems = null;


    // These functions are used to keep track of the selected items

    protected function makeJqWidget()
    {
        parent::makeJqWidget();

        Application::executeJsFunction('qcubed.selectable', $this->getJqControlId(), Application::PRIORITY_HIGH);
    }


    public function __set($strName, $mixValue)
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
                // Set the selected items to an array of object ids
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

    public function __get($strName)
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
