<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Table;

use QCubed\Control\FormBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Html;
use QCubed\Project\Control\ControlBase;
use QCubed\Type;

/**
 * Class CheckboxColumn
 *
 * A column of checkboxes.
 *
 * Prints checkboxes in a column, including the header. Override this class and implement whatever hooks you need. In
 * particular implement the CheckId hooks, and IsChecked hooks.
 *
 * To get the checkbox values to post values back to PHP, each checkbox must have an id of the form:
 *
 * QcontrolId_index
 *
 * This class does not detect and record changes in the checkbox list. You can detect changes from within
 * ParsePostData by calling $this->objForm->CheckableControlValue,
 * or use the QHtmlTableCheckBoxColumn_ClickEvent to detect a change to a checkbox.
 *
 * You will need to detect whether
 * the header check all box was clicked, or a regular box was clicked and respond accordingly. In response to a
 * click, you could store the array of ids of the checkboxes clicked in a session variable, the database, or
 * a cache variable. You would just give an id to each checkbox. This would cause internet traffic every time
 * a box is clicked.
 *
 * @property bool $ShowCheckAll
 * @was QHtmlTableCheckBoxColumn
 * @package QCubed\Table
 */
class CheckboxColumn extends DataColumn
{
    protected $blnHtmlEntities = false;    // turn off html entities
    protected $checkParamCallback = null;
    protected $blnShowCheckAll = false;

    /**
     * Returns a header cell with a checkbox. This could be used as a check all box. Override this and return
     * an empty string to turn it off.
     *
     * @return string
     */
    public function fetchHeaderCellValue()
    {
        if ($this->blnShowCheckAll) {
            $aParams = $this->getCheckboxParams(null);
            $aParams['type'] = 'checkbox';
            return Html::renderTag('input', $aParams, null, true);
        } else {
            return $this->Name;
        }
    }

    public function fetchCellObject($item)
    {
        $aParams = $this->getCheckboxParams($item);
        $aParams['type'] = 'checkbox';
        return Html::renderTag('input', $aParams, null, true);
    }

    /**
     * Returns an array of parameters to attach to the checkbox tag. Includes whether the
     * checkbox should appear as checked. Will try the callback first, and if not present,
     * will try overridden functions.
     *
     * @param mixed|null $item Null to indicate that we want the params for the header cell.
     * @return array
     */
    public function getCheckboxParams($item)
    {
        $aParams = array();

        if ($strId = $this->getCheckboxId($item)) {
            $aParams['id'] = $strId;
        }

        if ($this->isChecked($item)) {
            $aParams['checked'] = 'checked';
        }

        if ($strName = $this->getCheckboxName($item)) {
            $aParams['name'] = $strName; // name is not used by QCubed
        }

        $aParams['value'] = $this->getCheckboxValue($item); // note that value is required for html checkboxes, but is not used by QCubed

        if ($this->checkParamCallback) {
            $a = call_user_func($this->checkParamCallback, $item);
            $aParams = array_merge($aParams, $a);
        }

        return $aParams;
    }

    /**
     * Optional callback to control the appearance of the checkboxes. You can use a callback, or subclass to do this.
     * If a callback, it should be of the form:
     *    func($item)
     *
     *    $item is either the line item, or null to indicate the header
     *
     * This should return the following values in an array to indicate what should be put as attributes for the checkbox tag:
     *    id
     *  name
     *  value
     *  checked (only return a value here if you want it checked. Otherwise, do not include in the array)
     *
     *  See below for a description of what should be returned for each item.
     *
     * @param $callable
     */
    public function setCheckParamCallback($callable)
    {
        $this->checkParamCallback = $callable;
    }

    /**
     * Return the css id of the checkbox. Return null to not give it an id. If $item is null, it indicates we are asking for
     * the id of a header cell.
     *
     * @param mixed|null $item
     * @return null
     */
    protected function getCheckboxId($item)
    {
        return null;
    }

    /**
     * Return true if the checkbox should be drawn checked. Override this to provide the correct value.
     * @param mixed|null $item Null to get the id for the header checkbox
     * @return bool
     */
    protected function isChecked($item)
    {
        return false;
    }

    /**
     * Return the name attribute for the checkbox. If you return null, the checkbox will not get submitted to the form.
     * If you return a name, then that will be the key for the value submitted by the form. If you return a name
     * ending with brackets [], then this checkbox will be part of an array of values posted to that name.
     *
     * @param mixed|null $item Null to get the id for the header checkbox
     * @return null|string
     */
    protected function getCheckboxName($item)
    {
        return null;
    }

    /**
     * Return the value attribute of the checkbox tag. Checkboxes are required to have a value in html.
     * This value will be what is posted by form post.
     *
     * @param mixed|null $item Null to get the id for the header checkbox
     * @return string
     */
    protected function getCheckboxValue($item)
    {
        return "1"; // Means that if the checkbox is checked, the POST value corresponding to the name of the checkbox will be 1.
    }

    /**
     * Fix up possible embedded reference to the form.
     */
    public function sleep()
    {
        $this->checkParamCallback = ControlBase::sleepHelper($this->checkParamCallback);
        parent::sleep();
    }

    /**
     * Restore embedded objects.
     *
     * @param FormBase $objForm
     */
    public function wakeup(FormBase $objForm)
    {
        parent::wakeup($objForm);
        $this->checkParamCallback = ControlBase::wakeupHelper($objForm, $this->checkParamCallback);
    }

    /**
     * PHP magic method
     *
     * @param string $strName
     *
     * @return mixed
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case 'ShowCheckAll':
                return $this->blnShowCheckAll;
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
     * PHP magic method
     *
     * @param string $strName
     * @param string $mixValue
     *
     * @return mixed|void
     * @throws Caller
     * @throws InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "ShowCheckAll":
                try {
                    $this->blnShowCheckAll = Type::cast($mixValue, Type::BOOLEAN);
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
}
