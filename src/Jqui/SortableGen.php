<?php
namespace QCubed\Jqui;

use QCubed;
use QCubed\Control\Panel;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class SortableGen
 *
 * This is the SortableGen class that is automatically generated
 * by scraping the JQuery UI documentation website. As such, it includes all the options
 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
 * the SortableBase class for any glue code to make this class more
 * usable in QCubed.
 *
 * @see SortableBase
 * @package QCubed\Jqui
 * @property mixed $AppendTo
 * Defines where the helper that moves with the mouse is being appended
 * to during the drag (for example, to resolve overlap/zIndex
 * issues). Multiple types are supported:
 * 
 * 	* jQuery: A jQuery object containing the element to append the helper
 * to.
 * 	* Element: The element to append the helper to.
 * 	* Selector: A selector specifying which element to append the helper
 * to.
 * 	* String: The string "parent" will cause the helper to be a sibling
 * of the sortable item.
 * 

 *
 * @property string $Axis
 * If defined, the items can be dragged only horizontally or vertically.
 * Possible values: "x", "y".
 *
 * @property mixed $Cancel
 * Prevents sorting if you start on elements matching the selector.
 *
 * @property mixed $Classes
 * Specify additional classes to add to the widget elements. Any of
 * the classes specified in the Theming section can be used as keys to
 * override their value. To learn more about this option, check out the
 * learned article about the classes option.

 *
 * @property mixed $ConnectWith
 * A selector of other sortable elements that the items from this list
 * should be connected to. This is a one-way relationship, if you want
 * the items to be connected in both directions, the connectWith option
 * must be set on both sortable elements.
 *
 * @property mixed $Containment
 * Define a bounding box that the sortable items are constrained to
 * while dragging.
 * 
 * Note: The element specified for containment must have a calculated
 * width and height (though it need not be explicit). For example, if you
 * have float: left sortable children and specify containment: "parent"
 * be sure to have floated: left on the sortable/parent container as well,
 * or it will have height: 0, causing undefined behavior.
 * Multiple types are supported:
 * 
 * 	* Element: An element to use as the container.
 * 	* Selector: A selector specifying an element to use as the
 * container.
 * 	* String: A string identifying an element to use as the container.
 * Possible values: "parent", "document", "window".
 * 

 *
 * @property string $Cursor
 * Define the cursor that is being shown while sorting.
 *
 * @property mixed $CursorAt
 * Moves the sorting element or helper so the cursor always appears to
 * drag from the same position. Coordinates can be given as a hash using
 * a combination of one or two keys: { top, left, right, bottom }.
 *
 * @property integer $Delay
 * Time in milliseconds to define when the sorting should start. Adding a
 * delay helps to prevent unwanted drags when clicking on an
 * element. (version deprecated: 1.12)
 *
 * @property boolean $Disabled
 * Disables the sortable if set to true.
 *
 * @property integer $Distance
 * Tolerance, in pixels, for when sorting should start. If specified,
 * sorting will not start until after the mouse is dragged beyond distance.
 * It Can be used to allow for clicks on elements within a handle. (version
 * deprecated: 1.12)
 *
 * @property boolean $DropOnEmpty
 * If false, items from this sortable can't be dropped on an empty connected
 * sortable (see the connectWith option).
 *
 * @property boolean $ForceHelperSize
 * If true, forces the helper to have a size.
 *
 * @property boolean $ForcePlaceholderSize
 * If true, forces the placeholder to have a size.
 *
 * @property array $Grid
 * Snap the sorting element or helper to a grid, every x and y pixels.
 * Array values: [ x, y ].
 *
 * @property mixed $Handle
 * Restricts sort starts to click to the specified element.
 *
 * @property mixed $Helper
 * Allows for a helper element to be used for dragging display.Multiple
 *  types are supported:
 * 
 * 	* String: If set to "clone", then the element will be cloned and the
 * clone will be dragged.
 * 	* Function: A function that will return a DOMElement to use while
 * dragging. The function receives the event and the element being
 * sorted.
 * 

 *
 * @property mixed $Items
 * Specify which items inside the element should be sortable.
 *
 * @property integer $Opacity
 * Defines the opacity of the helper while sorting. From 0.01 to 1.
 *
 * @property string $Placeholder
 * A class name that gets applied to the otherwise white space.
 *
 * @property mixed $Revert
 * Whether the sortable items should revert to their new positions using
 * a smooth animation.Multiple types supported:
 * 
 * 	* Boolean: When set to true, the items will animate with the default
 * duration.
 * 	* Number: The duration for the animation, in milliseconds.
 * 

 *
 * @property boolean $Scroll
 * If set to true, the page scrolls when coming to an edge.
 *
 * @property integer $ScrollSensitivity
 * Defines how near the mouse must be to an edge to start scrolling.
 *
 * @property integer $ScrollSpeed
 * The speed at which the window should scroll once the mouse pointer
 * gets within the scrollSensitivity distance.
 *
 * @property string $Tolerance
 * Specify which mode to use for testing whether the item being moved
 * is hovering over another item. Possible values: 
 * 
 * 	* "intersect": The item overlaps the other item by at least 50%.
 * 	* "pointer": The mouse pointer overlaps the other item.
 * 

 * @property integer $ZIndex
 * Z-index for an element/helper while being sorted.
 *
 */

class SortableGen extends Panel
{
    protected string $strJavaScripts = QCUBED_JQUI_JS;
    protected string $strStyleSheets = QCUBED_JQUI_CSS;
    /** @var mixed */
    protected mixed $mixAppendTo = null;
    /** @var string|null */
    protected ?string $strAxis = null;
    /** @var mixed */
    protected mixed $mixCancel = null;
    /** @var mixed */
    protected mixed $mixClasses = null;
    /** @var mixed */
    protected mixed $mixConnectWith = null;
    /** @var mixed */
    protected mixed $mixContainment = null;
    /** @var string|null */
    protected ?string $strCursor = null;
    /** @var mixed */
    protected mixed $mixCursorAt = null;
    /** @var integer|null */
    protected ?int $intDelay = null;
    /** @var boolean|null */
    protected ?bool $blnDisabled = null;
    /** @var integer|null */
    protected ?int $intDistance = null;
    /** @var boolean|null */
    protected ?bool $blnDropOnEmpty = null;
    /** @var boolean|null */
    protected ?bool $blnForceHelperSize = null;
    /** @var boolean|null */
    protected ?bool $blnForcePlaceholderSize = null;
    /** @var array|null */
    protected ?array $arrGrid = null;
    /** @var mixed */
    protected mixed $mixHandle = null;
    /** @var mixed */
    protected mixed $mixHelper = null;
    /** @var mixed */
    protected mixed $mixItems = null;
    /** @var float|null */
    protected ?float $fltOpacity = null;
    /** @var string|null */
    protected ?string $strPlaceholder = null;
    /** @var mixed */
    protected mixed $mixRevert = null;
    /** @var boolean|null */
    protected ?bool $blnScroll = null;
    /** @var integer|null */
    protected ?int $intScrollSensitivity = null;
    /** @var integer|null */
    protected ?int $intScrollSpeed = null;
    /** @var string|null */
    protected ?string $strTolerance = null;
    /** @var integer|null */
    protected ?int $intZIndex = null;

    /**
     * Builds the option array to be sent to the widget constructor.
     *
     * @return array key=>value array of options
     */
    protected function makeJqOptions(): array
    {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->AppendTo)) {$jqOptions['appendTo'] = $val;}
        if (!is_null($val = $this->Axis)) {$jqOptions['axis'] = $val;}
        if (!is_null($val = $this->Cancel)) {$jqOptions['cancel'] = $val;}
        if (!is_null($val = $this->Classes)) {$jqOptions['classes'] = $val;}
        if (!is_null($val = $this->ConnectWith)) {$jqOptions['connectWith'] = $val;}
        if (!is_null($val = $this->Containment)) {$jqOptions['containment'] = $val;}
        if (!is_null($val = $this->Cursor)) {$jqOptions['cursor'] = $val;}
        if (!is_null($val = $this->CursorAt)) {$jqOptions['cursorAt'] = $val;}
        if (!is_null($val = $this->Delay)) {$jqOptions['delay'] = $val;}
        if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
        if (!is_null($val = $this->Distance)) {$jqOptions['distance'] = $val;}
        if (!is_null($val = $this->DropOnEmpty)) {$jqOptions['dropOnEmpty'] = $val;}
        if (!is_null($val = $this->ForceHelperSize)) {$jqOptions['forceHelperSize'] = $val;}
        if (!is_null($val = $this->ForcePlaceholderSize)) {$jqOptions['forcePlaceholderSize'] = $val;}
        if (!is_null($val = $this->Grid)) {$jqOptions['grid'] = $val;}
        if (!is_null($val = $this->Handle)) {$jqOptions['handle'] = $val;}
        if (!is_null($val = $this->Helper)) {$jqOptions['helper'] = $val;}
        if (!is_null($val = $this->Items)) {$jqOptions['items'] = $val;}
        if (!is_null($val = $this->Opacity)) {$jqOptions['opacity'] = $val;}
        if (!is_null($val = $this->Placeholder)) {$jqOptions['placeholder'] = $val;}
        if (!is_null($val = $this->Revert)) {$jqOptions['revert'] = $val;}
        if (!is_null($val = $this->Scroll)) {$jqOptions['scroll'] = $val;}
        if (!is_null($val = $this->ScrollSensitivity)) {$jqOptions['scrollSensitivity'] = $val;}
        if (!is_null($val = $this->ScrollSpeed)) {$jqOptions['scrollSpeed'] = $val;}
        if (!is_null($val = $this->Tolerance)) {$jqOptions['tolerance'] = $val;}
        if (!is_null($val = $this->ZIndex)) {$jqOptions['zIndex'] = $val;}
        return $jqOptions;
    }

    /**
     * Return the JavaScript function to call to associate the widget with the control.
     *
     * @return string
     */
    public function getJqSetupFunction(): string
    {
        return 'sortable';
    }

    /**
     * Cancels a change in the current sortable and reverts it to the state
     * prior to when the current sort was started. Useful in the stop and
     * receive callback functions.
     * 
     * 	* This method does not accept any arguments.
     */
    public function cancel(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "cancel", QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Removes the sortable functionality completely. This will return the
     * element back to its pre-init state.
     * 
     * 	* This method does not accept any arguments.
     */
    public function destroy(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Disables the sortable.
     * 
     * 	* This method does not accept any arguments.
     */
    public function disable(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Enables the sortable.
     * 
     * 	* This method does not accept any arguments.
     */
    public function enable(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Retrieves the sortables instance object. If the element does not have
     * an associated instance, undefined is returned.
     * 
     * Unlike other widget methods, instance() is safe to call on any element
     * after the sortable plugin has loaded.
     * 
     * 	* This method does not accept any arguments.
     */
    public function instance(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Gets the value currently associated with the specified optionName.
     *
     * Note: For options that have objects as their value, you can get the
     * value of a specific key by using dot notation. For example, "foo.bar"
     * would get the value of the bar property on the foo option.
     *
     *    * optionName Type: String The name of the option to get.
     * @param string $optionName
     */
    public function option(string $optionName): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Gets an object containing key/value pairs representing the current
     * sortable options hash.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function option1(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Sets the value of the sortable option associated with the specified
     * optionName.
     *
     * Note: For options that have objects as their value, you can set the
     * value of just one property by using dot notation for optionName. For
     * example, "foo.bar" would update only the bar property of the foo
     * option.
     *
     *    * optionName Type: String The name of the option to set.
     *    * value Type: Object A value to set for the option.
     * @param string $optionName
     * @param object $value
     */
    public function option2(string $optionName, object $value): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, $value, QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Sets one or more options for the sortable.
     *
     *    * options Type: Object A map of option-value pairs to set.
     * @param object $options
     */
    public function option3(object $options): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Refresh the sortable items. Triggers the reloading of all sortable
     * items, causing new items to be recognized.
     * 
     * 	* This method does not accept any arguments.
     */
    public function refresh(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "refresh", QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Refresh the cached positions of the sortable items. Calling this
     * method refreshes the cached item positions of all sortables.
     * 
     * 	* This method does not accept any arguments.
     */
    public function refreshPositions(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "refreshPositions", QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Serializes the sortables item IDs into a form/ajax submittable string.
     * Calling this method produces a hash that can be appended to any url to
     * easily submit a new item order back to the server.
     *
     * It works by default by looking at the id of each item in the format
     * "setname_number", and it spits out a hash like
     * "setname[]=number&setname[]=number".
     *
     * _Note: If serialize returns an empty string, make sure the id
     * attributes include an underscore. They must be in the form:
     * "set_number," For example, a 3-element list with id attributes "foo_1",
     * "foo_5", "foo_2" will serialize to "foo[]=1&foo[]=5&foo[]=2". You can
     * use an underscore, equal sign or hyphen to separate the set and
     * number. For example, "foo=1", "foo-1", and "foo_1" are all serialized to
     * "foo[]=1".
     *
     *    * options Type: Object Options to customize the serialization.
     *
     *    * key (default: the part of the attribute in front of the separator)
     * Type: String Replaces part1[] with the specified value.
     *    * attribute (default: "id") Type: String The name of the attribute
     * to use for the values.
     *    * expression (default: /(.+)[-=_](.+)/) Type: RegExp is A regular
     * expression used to split the attribute value into key and value parts.
     * @param object $options
     */
    public function serialize(object $options): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "serialize", $options, QCubed\ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Serializes the sortables item IDs into an array of string.
     *
     *    * options Type: Object Options to customize the serialization.
     *
     *    * attribute (default: "id") Type: String The name of the attribute to
     * use for the values.
     * @param object $options
     */
    public function toArray(object $options): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "toArray", $options, QCubed\ApplicationBase::PRIORITY_LOW);
    }


    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'AppendTo': return $this->mixAppendTo;
            case 'Axis': return $this->strAxis;
            case 'Cancel': return $this->mixCancel;
            case 'Classes': return $this->mixClasses;
            case 'ConnectWith': return $this->mixConnectWith;
            case 'Containment': return $this->mixContainment;
            case 'Cursor': return $this->strCursor;
            case 'CursorAt': return $this->mixCursorAt;
            case 'Delay': return $this->intDelay;
            case 'Disabled': return $this->blnDisabled;
            case 'Distance': return $this->intDistance;
            case 'DropOnEmpty': return $this->blnDropOnEmpty;
            case 'ForceHelperSize': return $this->blnForceHelperSize;
            case 'ForcePlaceholderSize': return $this->blnForcePlaceholderSize;
            case 'Grid': return $this->arrGrid;
            case 'Handle': return $this->mixHandle;
            case 'Helper': return $this->mixHelper;
            case 'Items': return $this->mixItems;
            case 'Opacity': return $this->fltOpacity;
            case 'Placeholder': return $this->strPlaceholder;
            case 'Revert': return $this->mixRevert;
            case 'Scroll': return $this->blnScroll;
            case 'ScrollSensitivity': return $this->intScrollSensitivity;
            case 'ScrollSpeed': return $this->intScrollSpeed;
            case 'Tolerance': return $this->strTolerance;
            case 'ZIndex': return $this->intZIndex;
            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case 'AppendTo':
                $this->mixAppendTo = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'appendTo', $mixValue);
                break;

            case 'Axis':
                try {
                    $this->strAxis = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'axis', $this->strAxis);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Cancel':
                $this->mixCancel = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'cancel', $mixValue);
                break;

            case 'Classes':
                $this->mixClasses = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'classes', $mixValue);
                break;

            case 'ConnectWith':
                $this->mixConnectWith = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'connectWith', $mixValue);
                break;

            case 'Containment':
                $this->mixContainment = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'containment', $mixValue);
                break;

            case 'Cursor':
                try {
                    $this->strCursor = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'cursor', $this->strCursor);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'CursorAt':
                $this->mixCursorAt = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'cursorAt', $mixValue);
                break;

            case 'Delay':
                try {
                    $this->intDelay = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'delay', $this->intDelay);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Disabled':
                try {
                    $this->blnDisabled = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'disabled', $this->blnDisabled);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Distance':
                try {
                    $this->intDistance = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'distance', $this->intDistance);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'DropOnEmpty':
                try {
                    $this->blnDropOnEmpty = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'dropOnEmpty', $this->blnDropOnEmpty);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ForceHelperSize':
                try {
                    $this->blnForceHelperSize = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'forceHelperSize', $this->blnForceHelperSize);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ForcePlaceholderSize':
                try {
                    $this->blnForcePlaceholderSize = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'forcePlaceholderSize', $this->blnForcePlaceholderSize);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Grid':
                try {
                    $this->arrGrid = Type::Cast($mixValue, Type::ARRAY_TYPE);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'grid', $this->arrGrid);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Handle':
                $this->mixHandle = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'handle', $mixValue);
                break;

            case 'Helper':
                $this->mixHelper = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'helper', $mixValue);
                break;

            case 'Items':
                $this->mixItems = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'items', $mixValue);
                break;

            case 'Opacity':
                try {
                    $this->fltOpacity = Type::Cast($mixValue, Type::FLOAT);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'opacity', $this->fltOpacity);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Placeholder':
                try {
                    $this->strPlaceholder = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'placeholder', $this->strPlaceholder);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Revert':
                $this->mixRevert = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'revert', $mixValue);
                break;

            case 'Scroll':
                try {
                    $this->blnScroll = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'scroll', $this->blnScroll);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ScrollSensitivity':
                try {
                    $this->intScrollSensitivity = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'scrollSensitivity', $this->intScrollSensitivity);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ScrollSpeed':
                try {
                    $this->intScrollSpeed = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'scrollSpeed', $this->intScrollSpeed);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Tolerance':
                try {
                    $this->strTolerance = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tolerance', $this->strTolerance);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ZIndex':
                try {
                    $this->intZIndex = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'zIndex', $this->intZIndex);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }


            case 'Enabled':
                $this->Disabled = !$mixValue;	// Tie in standard QCubed functionality
                parent::__set($strName, $mixValue);
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
     * If this control is attachable to a codegenerated control in a ModelConnector, this function will be
     * used by the ModelConnector designer dialog to display a list of options for the control.
     * @return QModelConnectorParam[]
     *
     * @throws Caller
     */
    public static function getModelConnectorParams(): array
    {
        return array_merge(parent::GetModelConnectorParams(), array(
            new QModelConnectorParam (get_called_class(), 'Axis', 'If defined, the items can be dragged only horizontally or vertically. Possible values: \"x\", \"y\".', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'Cursor', 'Define the cursor that is being shown while sorting.', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'Delay', 'Time in milliseconds to define when the sorting should start. Adding delay helps to prevent unwanted drags when clicking on an element. (version deprecated: 1.12)', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the sortable if set to true.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'Distance', 'Tolerance, in pixels, for when sorting should start. If specified, sorting will not start until after a mouse is dragged beyond distance. It Can be used to allow for clicks on elements within a handle. (version deprecated: 1.12)', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'DropOnEmpty', 'If false, items from this sortable can\'t be dropped on an empty connected Portable (see the connectWith option.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'ForceHelperSize', 'If true, forces the helper to have a size.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'ForcePlaceholderSize', 'If true, forces the placeholder to have a size.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'Grid', 'Snap the sorting element or helper to a grid, every x and y pixels. Array values: [ x, y ].', Type::ARRAY_TYPE),
            new QModelConnectorParam (get_called_class(), 'Opacity', 'Defines the opacity of the helper while sorting. From 0.01 to 1.', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Placeholder', 'A class name that gets applied to the otherwise white space.', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'Scroll', 'If set to true, the page scrolls when coming to an edge.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'ScrollSensitivity', 'Defines how near the mouse must be to an edge to start scrolling.', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'ScrollSpeed', 'The speed at which the window should scroll once the mouse pointer gets within the scrollSensitivity distance.', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Tolerance', 'Specify which mode to use for testing whether the item being moved hovering over another item. Possible values: 	* \"intersect\": The item overlaps the other item by at least 50%.	* \"pointer\": The mouse pointer overlaps the other item.', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'ZIndex', 'Z-index for an element/helper while being sorted.', Type::INTEGER),
        ));
    }
}
