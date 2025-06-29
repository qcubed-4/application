<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Event;

use QCubed\Exception\Caller;

/**
 * Class QCellClickEvent
 * An event to detect clicking on a table cell.
 * Lots of things can be determined using this event by changing the JsReturnParam values. When this event fires,
 * the JavaScript environment will have the following local variables defined:
 * - this: The HTML object for the cell clicked.
 * - event: The event object for the click.
 *
 * Here are some examples of return params you can specify to return data to your action handler:
 * 	this.id - the cell id
 *  this.tagName - the tag for the cell (either th or td)
 *  this.cellIndex - the column index that was clicked on, starting on the left with column zero
 *  $j(this).data('value') - the "data-value" attribute of the cell (if you specify one). Use this formula for any kind of "data-" attribute.
 *  $j(this).parent() - the jQuery row object
 *  $j(this).parent()[0] - the HTML row object
 *  $j(this).parent()[0].rowIndex - the index of the row clicked, starting with zero at the top (including any header rows).
 *  $j(this).parent().attr('id') or $j(this).parent()[0].id - the id of the row clicked on
 *  $j(this).parent().data("value") - the "data-value" attribute of the row. Use this formula for any kind of "data-" attribute.
 *  $j(this).parent().closest('table').find('thead').find('th')[this.cellIndex].id - the id of the column clicked in
 *  event.target - the HTML object clicked in. If your table cell had other objects in it, this will return the
 *    object clicked inside the cell. This could be important, for example, if you had form objects inside the cell,
 *    and you wanted to behave differently if a form object was clicked on, verses clicking outside the form object.
 *
 * You can put your items in a JavaScript array, and an array will be returned as the strParameter in the action.
 * Or you can put it in a JavaScript object, and a named array(hash) will be returned.
 *
 * The default returns the array(row=>rowIndex, col=>colIndex), but you can override this with your action. For
 * example:
 *
 * new QAjaxAction ('yourFunction', null, 'this.cellIndex')
 *
 * will return the column index into the strParameter, instead of the default.
 *
 * @package QCubed\Event
 */
class CellClick extends Click
{
    // Shortcuts to specify common return parameters
    const ROW_INDEX = '$j(this).parent()[0].rowIndex';
    const COLUMN_INDEX = 'this.cellIndex';
    const CELL_ID = 'this.id';
    const ROW_ID = '$j(this).parent().attr("id")';
    const ROW_VALUE = '$j(this).parent().data("value")';
    const COL_ID = '$j(this).parent().closest("table").find("thead").find("th")[this.cellIndex].id';

    protected string $strReturnParam;

    /**
     * Constructor method for initializing the object with optional delay, condition, return parameters, and event blocking settings.
     *
     * @param int|null $intDelay Optional delay in milliseconds, default is 0.
     * @param string|null $strCondition Optional condition used to trigger certain actions, default is null.
     * @param mixed $mixReturnParams Optional return parameters for customizing return data; can be an array or a string, default is null.
     * @param bool $blnBlockOtherEvents Determines whether to block other events while this is active, default is false.
     *
     * @throws Caller
     */
    public function __construct(?int $intDelay = 0, ?string $strCondition = null, mixed $mixReturnParams = null, ?bool $blnBlockOtherEvents = false)
    {
        parent::__construct($intDelay, $strCondition, 'th,td', $blnBlockOtherEvents);

        if (!$mixReturnParams) {
            $this->strReturnParam = '{"row": $j(this).parent()[0].rowIndex, "col": this.cellIndex}'; // default returns the row and colum indexes of the cell clicked
        } elseif (is_array($mixReturnParams)) {
            $combined = array_map(function ($key, $val) {
                return '"' . $key . '":' . $val;
            }, array_keys($mixReturnParams), array_values($mixReturnParams));

            $this->strReturnParam = '{' . implode(',', $combined) . '}';
        } elseif (is_string($mixReturnParams)) {
            $this->strReturnParam = $mixReturnParams;
        }
    }

    /**
     * Retrieves the data value associated with the specified key from the parent element.
     *
     * @param string $strKey The key for which the data value needs to be fetched.
     * @return string Returns the JavaScript code string to access the data value.
     */
    public static function rowDataValue(string $strKey): string
    {
        return    '$j(this).parent().data("' . $strKey . '")';
    }

    /**
     * Retrieves the data value associated with the specified key from the current element.
     *
     * @param string $strKey The key for which the data value needs to be fetched.
     * @return string Returns the JavaScript code string to access the data value.
     */
    public static function cellDataValue(string $strKey): string
    {
        return    '$j(this).data("' . $strKey . '")';
    }

    /**
     * Magic method to retrieve the value of a property by name.
     *
     * @param string $strName The name of the property to retrieve.
     *
     * @return mixed The value of the property if it exists, or the result of the parent::__get method.
     *
     * @throws Caller If the property name does not exist or an error occurs in the parent::__get method.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'JsReturnParam':
                return $this->strReturnParam;

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