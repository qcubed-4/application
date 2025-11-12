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
use Exception;
use QCubed\Html;
use QCubed\Project\Control\ControlBase;
use QCubed\Type;

/**
 * Class CheckboxColumn
 *
 * A column of checkboxes.
 *
 * Print checkboxes in a column, including the header. Override this class and implement whatever hooks you need. In
 * particular, implement the CheckId hooks, and IsChecked hooks.
 *
 * To get the checkbox values to post values back to PHP, each checkbox must have an ID of the form:
 *
 * ControlId index
 *
 * This class does not detect and record changes in the checkbox list. You can detect changes from within
 * ParsePostData by calling $this->objForm->CheckableControlValue,
 * or use the QHtmlTableCheckBoxColumn_ClickEvent to detect a change to a checkbox.
 *
 * You will need to detect whether
 * the header check all box was clicked, or a regular box was clicked and respond accordingly. In response to a
 * click, you could store the array of IDs of the checkboxes clicked in a session variable, the database, or
 * a cache variable. You would just give an ID to each checkbox. This would cause internet traffic every time
 * a box is clicked.
 *
 * @property bool $ShowCheckAll
 * @package QCubed\Table
 */
class CheckboxColumn extends DataColumn
{
    /** @var bool|null */
    protected bool $blnHtmlEntities = false;    // turn off HTML entities
    /** @var callable|null Callback function to modify checkbox parameters. */
    protected $checkParamCallback = null;

    /** @var bool */
    protected ?bool $blnShowCheckAll = false;

    /**
     * Returns a header cell with a checkbox. This could be used as a check-all box. Override this and return
     * an empty string to turn it off.
     *
     * @return string
     */
    public function fetchHeaderCellValue(): string
    {
        if ($this->blnShowCheckAll) {
            $aParams = $this->getCheckboxParams(null);
            $aParams['type'] = 'checkbox';
            return Html::renderTag('input', $aParams, null, true);
        } else {
            return $this->Name;
        }
    }

    /**
     * Generates and returns the HTML string for a checkbox input element based on the provided item.
     *
     * @param mixed $item The data used to configure the checkbox parameters.
     * @return string The HTML string representing the checkbox input element.
     */
    public function fetchCellObject(mixed $item): string
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
    public function getCheckboxParams(mixed $item): array
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

        $aParams['value'] = $this->getCheckboxValue($item); // note that value is required for HTML checkboxes but is not used by QCubed

        if ($this->checkParamCallback) {
            $a = call_user_func($this->checkParamCallback, $item);
            $aParams = array_merge($aParams, $a);
        }

        return $aParams;
    }

    /**
     * An optional callback to control the appearance of checkboxes. You can use a callback or subclass for this.
     * If a callback, it should be of the form:
     * func($item)
     *
     * $item is either the line item or null to indicate the header
     *
     * This should return the following values in an array to indicate what should be put as attributes for the checkbox tag:
     * id
     * name
     * value
     * checked (only return a value here if you want it checked. Otherwise, do not include in the array)
     *
     * See below for a description of what should be returned for each item.
     *
     * @param callable $callable The callback function to assign for parameter validation.
     * @return void
     */
    public function setCheckParamCallback(callable $callable): void
    {
        $this->checkParamCallback = $callable;
    }

    /**
     * Return the CSS ID of the checkbox. Return null to not give it an ID. If $item is null, it indicates we are asking for
     * the ID of a header cell.
     *
     * @param mixed|null $item
     * @return string|null
     */
    protected function getCheckboxId(mixed $item): ?string
    {
        return null;
    }

    /**
     * Return true if the checkbox should be drawn checked. Override this to provide the correct value.
     * @param mixed|null $item Null to get the ID for the header checkbox
     * @return bool
     */
    protected function isChecked(mixed $item): bool
    {
        return false;
    }

    /**
     * Retrieves the name attribute for a checkbox based on the given item.
     *
     * @param mixed $item The item for which the checkbox name is to be determined.
     * @return string|null Returns the checkbox name as a string or null if not applicable.
     */
    protected function getCheckboxName(mixed $item): ?string
    {
        return null;
    }

    /**
     * Return the value attribute of the checkbox tag. Checkboxes are required to have a value in HTML.
     * This value will be what is posted by form post.
     *
     * @param mixed|null $item Null to get the ID for the header checkbox
     * @return string
     */
    protected function getCheckboxValue(mixed $item): string
    {
        return "1"; // Means that if the checkbox is checked, the POST value corresponding to the name of the checkbox will be 1.
    }

    /**
     * Fix up a possible embedded reference to the form.
     */
    public function sleep(): void
    {
        $this->checkParamCallback = ControlBase::sleepHelper($this->checkParamCallback);
        parent::sleep();
    }

    /**
     * Restore embedded objects.
     *
     * @param FormBase $objForm
     */
    public function wakeup(FormBase $objForm): void
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
    public function __get(string $strName): mixed
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
     * @param mixed $mixValue
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     * @throws Exception
     */
    public function __set(string $strName, mixed $mixValue): void
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