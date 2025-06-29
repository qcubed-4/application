<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use JsonSerializable;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Js\Helper;
use QCubed\Type;

/**
 * Class ListItem
 *
 * Utilized by the {@link ListControl} class which contains a private array of ListItems. Originally these
 * represented items in a select list, but now represent items in any kind of control that has repetitive items
 * in it. This includes list controls, menus, drop-downs, and hierarchical lists. This is a general-purpose container
 * for the options in each item. Note that not all the options are used by every control, and we don't do any drawing here.
 *
 * @property boolean $Selected  is a boolean of whether or not this item is selected or not (do only! use during initialization, otherwise this should be set by the {@link QListControl}!)
 * @property boolean $Disabled is a boolean of whether or not this item is disabled or not.
 * @property string $Mark is a string (if it is necessary for a special solution) in which the Item should be displayed.
 *                  This is an additional option, zero by default.
 * @property string $ItemGroup is the group (if any) in which the Item should be displayed.
 * @property string $Label     is an optional text to display instead of the Name for certain controls.
 *
 * @package QCubed\Control
 */
class ListItem extends ListItemBase implements JsonSerializable
{

    ///////////////////////////
    // Private Member Variables
    ///////////////////////////
    /** @var bool Is the item selected? */
    protected ?bool $blnSelected = false;
    /** @var bool Is the item disabled? */
    protected ?bool $blnDisabled = false;
    /** @var string|null text for the item. */
    protected ?string $strItemGroup = null;
    /** @var string|null Label text for the item. */
    protected ?string $strLabel = null;

    /////////////////////////
    // Methods
    /////////////////////////
    /**
     * Constructs a new instance of the class with specified parameters.
     *
     * @param string $strName The name of the control.
     * @param mixed|null $strValue The value of the control. Default is null.
     * @param bool $blnSelected Whether the control is selected. Default is false.
     * @param bool $blnDisabled Whether the control is disabled. Default is false.
     * @param string|null $strItemGroup The item group the control belongs to. Default is null.
     * @param mixed|null $mixOverrideParameters Additional parameters to override attributes. Default is null.
     * @return void
     * @throws Caller
     */
    public function __construct(
        string      $strName,
        ?string     $strValue = null,
        ?bool       $blnSelected = false,
        ?bool       $blnDisabled = false,
        ?string     $strItemGroup = null,
        mixed       $mixOverrideParameters = null
    ) {
        parent::__construct($strName, $strValue);
        $this->blnSelected = $blnSelected;
        $this->blnDisabled = $blnDisabled;
        $this->strItemGroup = $strItemGroup;

        // Override parameters get applied here
        $strOverrideArray = func_get_args();

        if (count($strOverrideArray) > 5) {
            throw new Caller ("Please provide either a string, or an array, but not multiple parameters");
        }
        if ($mixOverrideParameters) {
            $this->getStyle()->overrideAttributes($mixOverrideParameters);
        }
    }

    /**
     * Returns the details of the control as JavaScript string. This is customized for the JQuery UI autocomplete. If your
     * widget requires something else, you will need to subclass and override this.
     * @return string
     */
    public function toJsObject(): string
    {
        $strId = $this->strValue;
        if (is_null($strId)) {
            $strId = $this->strId;
        }

        $a = array('value' => $this->strName, 'id' => $strId);
        if ($this->strLabel) {
            $a['label'] = $this->strLabel;
        }
        if ($this->strItemGroup) {
            $a['category'] = $this->strItemGroup;
        }
        return Helper::toJsObject($a);
    }

    /**
     * Serializes the object into a JSON-compatible format, returning an associative array with
     * details such as value, ID, label, and category.
     * @return array
     */
    public function jsonSerialize(): array
    {
        $strId = $this->strValue;
        if (!$strId) {
            $strId = $this->strId;
        }

        $a = array('value' => $this->strName, 'id' => $strId);
        if ($this->strLabel) {
            $a['label'] = $this->strLabel;
        }
        if ($this->strItemGroup) {
            $a['category'] = $this->strItemGroup;
        }
        return $a;
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * Magic method to retrieve the value of a property by name.
     *
     * @param string $strName The name of the property to retrieve.
     * @return mixed The value of the property, or the result of the parent::__get method if the property does not exist.
     * @throws Caller If the property name is invalid or not accessible.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case "Selected":
                return $this->blnSelected;
            case "Disabled":
                return $this->blnDisabled;
            case "ItemGroup":
                return $this->strItemGroup;
            case "Label":
                return $this->strLabel;
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
     * Magic method to set the value of a property dynamically.
     * Handles specific properties by casting the provided value to the appropriate type.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property.
     * @return void
     * @throws InvalidCast Thrown if the value cannot be cast to the required type for the property.
     * @throws Caller Thrown if the property does not exist or cannot be set in the parent context.
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case "Selected":
                try {
                    $this->blnSelected = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Disabled":
                try {
                    $this->blnDisabled = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "ItemGroup":
                try {
                    $this->strItemGroup = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Label":
                try {
                    $this->strLabel = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
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
}
