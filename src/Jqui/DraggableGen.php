<?php
namespace QCubed\Jqui;

use QCubed\Project\Control\ControlBase;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\ApplicationBase;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class DraggableGen
 *
 * This is the DraggableGen class that is automatically generated
 * by scraping the JQuery UI documentation website. As such, it includes all the options
 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
 * the DraggableBase class for any glue code to make this class more
 * usable in QCubed.
 *
 * @see DraggableBase
 * @package QCubed\Jqui
 * @property boolean $AddClasses
 * If set to false, it will prevent the ui-draggable class from being added.
 * This may be desired as a performance optimization when calling
 * .draggable() on hundreds of elements.
 *
 * @property mixed $AppendTo
 * Which element the draggable helper should be appended to while
 * dragging.
 * Note: The appendTo option only works when the helper option is set to
 * not use the original element.Multiple types supported:
 * 
 * 	* jQuery: A jQuery object containing the element to append the helper
 * to.
 * 	* Element: The element to append the helper to.
 * 	* Selector: A selector specifying which element to append the helper
 * to.
 * 	* String: The string "parent" will cause the helper to be a sibling
 * of the draggable.
 * 

 *
 * @property string $Axis
 * Constrains dragging to either the horizontal (x) or vertical (y) axis.
 * Possible values: "x", "y".
 *
 * @property mixed $Cancel
 * Prevents dragging from starting on specified elements.
 *
 * @property mixed $Classes
 * Specify additional classes to add to the widget elements. Any of
 * the classes specified in the Theming section can be used as keys to
 * override their value. To learn more about this option, check out the
 * learned article about the classes option.

 *
 * @property mixed $ConnectToSortable
 * Allows the draggable to be dropped onto the specified sortables. If
 * this option is used, a draggable can be dropped onto a sortable list
 * and then become part of it. Note: The helper option must be set to
 * "clone" in order to work flawlessly. Requires the jQuery UI Sortable
 * plugin to be included.
 *
 * @property mixed $Containment
 * Constrains dragging to within the bounds of the specified element or
 * region.Multiple types supported:
 * 
 * 	* Selector: The draggable element will be contained to the bounding
 * box of the first element found by the selector. If no element is
 * found, no containment will be set.
 * 	* Element: The draggable element will be contained to the bounding
 * box of this element.
 * 	* String: Possible values: "parent", "document", "window".
 * 	* Array: An array defining a bounding box in the form [ x1, y1, x2,
 * y2 ].
 * 

 *
 * @property string $Cursor
 * The CSS cursor during the drag operation.
 *
 * @property mixed $CursorAt
 * Set the offset of the dragging helper relative to the mouse cursor.
 * Coordinates can be given as a hash using a combination of one or two
 * keys: { top, left, right, bottom }.
 *
 * @property integer $Delay
 * Time in milliseconds after mousedown until dragging should start. This
 * option can be used to prevent unwanted drags when clicking on an
 * element. (version deprecated: 1.12)
 *
 * @property boolean $Disabled
 * Disables the draggable if set to true.
 *
 * @property integer $Distance
 * Distance in pixels after mousedown the mouse must move before dragging
 * should start. This option can be used to prevent unwanted drags when
 * clicking on an element. (version deprecated: 1.12)
 *
 * @property array $Grid
 * Snap the dragging helper to a grid, every x and y pixels. The array
 * must be of the form [ x, y ].
 *
 * @property mixed $Handle
 * If specified, restricts dragging from starting unless the mousedown
 * occurs on the specified element(s). Only elements that descend from
 * the draggable element are permitted.
 *
 * @property mixed $Helper
 * Allows for a helper element to be used for dragging display.Multiple
 *  types are supported:
 * 
 * 	* String: If set to "clone", then the element will be cloned and the
 * clone will be dragged.
 * 	* Function: A function that will return a DOMElement to use while
 * dragging.
 * 

 *
 * @property mixed $IframeFix
 * Prevent iframes from capturing the mousemove events during a drag.
 * Useful in combination with the cursorAt option, or in any case where
 * the mouse cursor may not be over the helper.Multiple types supported:
 * 
 * 	* Boolean: When set to true, transparent overlays will be placed over
 * all iframes on the page.
 * 	* Selector: Any iframes matching the selector will be covered by
 * transparent overlays.
 * 

 *
 * @property integer $Opacity
 * Opacity for the helper while being dragged.
 *
 * @property boolean $RefreshPositions
 * If set to true, all droppable positions are calculated on every
 * mousemove. _Caution: This solves issues on highly dynamic pages but
 * dramatically decreases performance._
 *
 * @property mixed $Revert
 * Whether the element should revert to its start position when dragging
 * stops.Multiple types supported:
 * 
 * 	* Boolean: If set to true, the element will always revert.
 * 	* String: If set to "invalid", revert will only occur if the
 * draggable has not been dropped on a droppable. For "valid", the
 * other way around.
 * 	* Function: A function to determine whether the element should
 * revert to its start position. The function must return true to revert
 * the element.
 * 

 *
 * @property integer $RevertDuration
 * The duration of the revert animation, in milliseconds. Ignored if the
 * revert option is false.
 *
 * @property string $Scope
 * Used to group sets of draggable and droppable items, in addition to
 * a droppable accept option. A draggable with the same scope value as a
 * droppable will be accepted by the droppable.
 *
 * @property boolean $Scroll
 * If set to true, the container auto-scrolls while dragging.
 * If set to true, the container auto-scrolls while dragging.
 *
 * @property integer $ScrollSensitivity
 * Distance in pixels from the edge of the viewport after which the
 * viewport should scroll. Distance is relative to a pointer, not the
 * draggable. Ignored if the scroll option is false.
 *
 * @property integer $ScrollSpeed
 * The speed at which the window should scroll once the mouse pointer
 * gets within the scrollSensitivity distance. Ignored if the scroll
 * option is false.
 *
 * @property mixed $Snap
 * Whether the element should snap to other elements.Multiple types
 * supported:
 * 
 * 	* Boolean: When set to true, the element will snap to all other
 * draggable elements.
 * 	* Selector: A selector specifying which elements to snap to.
 * 

 *
 * @property string $SnapMode
 * Determines which edges of snap elements the draggable will snap to.
 * Ignored if the snap option is false. Possible values: "inner",
 * "outer", "both".
 *
 * @property integer $SnapTolerance
 * The distance in pixels from the snap element edges at which snapping
 * should occur. Ignored if the snap option is false.
 *
 * @property mixed $Stack
 * Controls the z-index of the set of elements that match the selector,
 * always brings the currently dragged item to the front. Very useful in
 * things like window managers.
 *
 * @property integer $ZIndex
 * Z-index for the helper while being dragged.
 *
 */

abstract class DraggableGen extends ControlBase
{
    protected string $strJavaScripts = QCUBED_JQUI_JS;
    protected string $strStyleSheets = QCUBED_JQUI_CSS;
    /** @var boolean */
    protected ?bool $blnAddClasses = null;
    /** @var mixed */
    protected mixed $mixAppendTo = null;
    /** @var string|null */
    protected ?string $strAxis = null;
    /** @var mixed */
    protected mixed $mixCancel = null;
    /** @var mixed */
    protected mixed $mixClasses = null;
    /** @var mixed */
    protected mixed $mixConnectToSortable = null;
    /** @var mixed */
    protected mixed $mixContainment = null;
    /** @var string|null */
    protected ?string $strCursor = null;
    /** @var mixed */
    protected mixed $mixCursorAt = null;
    /** @var integer|null */
    protected ?int $intDelay = null;
    /** @var boolean */
    protected ?bool $blnDisabled = null;
    /** @var integer|null */
    protected ?int $intDistance = null;
    /** @var array|null */
    protected ?array $arrGrid = null;
    /** @var mixed */
    protected mixed $mixHandle = null;
    /** @var mixed */
    protected mixed $mixHelper = null;
    /** @var mixed */
    protected mixed $mixIframeFix = null;
    /** @var integer|null */
    protected ?int $intOpacity = null;
    /** @var boolean */
    protected ?bool $blnRefreshPositions = null;
    /** @var mixed */
    protected mixed $mixRevert = null;
    /** @var integer|null */
    protected ?int $intRevertDuration = null;
    /** @var string|null */
    protected ?string $strScope = null;
    /** @var boolean */
    protected ?bool $blnScroll = null;
    /** @var integer|null */
    protected ?int $intScrollSensitivity = null;
    /** @var integer|null */
    protected ?int $intScrollSpeed = null;
    /** @var mixed */
    protected mixed $mixSnap = null;
    /** @var string|null */
    protected ?string $strSnapMode = null;
    /** @var integer|null */
    protected ?int $intSnapTolerance = null;
    /** @var mixed */
    protected mixed $mixStack = null;
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
        if (!is_null($val = $this->AddClasses)) {$jqOptions['addClasses'] = $val;}
        if (!is_null($val = $this->AppendTo)) {$jqOptions['appendTo'] = $val;}
        if (!is_null($val = $this->Axis)) {$jqOptions['axis'] = $val;}
        if (!is_null($val = $this->Cancel)) {$jqOptions['cancel'] = $val;}
        if (!is_null($val = $this->Classes)) {$jqOptions['classes'] = $val;}
        if (!is_null($val = $this->ConnectToSortable)) {$jqOptions['connectToSortable'] = $val;}
        if (!is_null($val = $this->Containment)) {$jqOptions['containment'] = $val;}
        if (!is_null($val = $this->Cursor)) {$jqOptions['cursor'] = $val;}
        if (!is_null($val = $this->CursorAt)) {$jqOptions['cursorAt'] = $val;}
        if (!is_null($val = $this->Delay)) {$jqOptions['delay'] = $val;}
        if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
        if (!is_null($val = $this->Distance)) {$jqOptions['distance'] = $val;}
        if (!is_null($val = $this->Grid)) {$jqOptions['grid'] = $val;}
        if (!is_null($val = $this->Handle)) {$jqOptions['handle'] = $val;}
        if (!is_null($val = $this->Helper)) {$jqOptions['helper'] = $val;}
        if (!is_null($val = $this->IframeFix)) {$jqOptions['iframeFix'] = $val;}
        if (!is_null($val = $this->Opacity)) {$jqOptions['opacity'] = $val;}
        if (!is_null($val = $this->RefreshPositions)) {$jqOptions['refreshPositions'] = $val;}
        if (!is_null($val = $this->Revert)) {$jqOptions['revert'] = $val;}
        if (!is_null($val = $this->RevertDuration)) {$jqOptions['revertDuration'] = $val;}
        if (!is_null($val = $this->Scope)) {$jqOptions['scope'] = $val;}
        if (!is_null($val = $this->Scroll)) {$jqOptions['scroll'] = $val;}
        if (!is_null($val = $this->ScrollSensitivity)) {$jqOptions['scrollSensitivity'] = $val;}
        if (!is_null($val = $this->ScrollSpeed)) {$jqOptions['scrollSpeed'] = $val;}
        if (!is_null($val = $this->Snap)) {$jqOptions['snap'] = $val;}
        if (!is_null($val = $this->SnapMode)) {$jqOptions['snapMode'] = $val;}
        if (!is_null($val = $this->SnapTolerance)) {$jqOptions['snapTolerance'] = $val;}
        if (!is_null($val = $this->Stack)) {$jqOptions['stack'] = $val;}
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
        return 'draggable';
    }


    /**
     * Removes the draggable functionality completely. This will return the
     * element back to its pre-init state.
     * 
     * 	* This method does not accept any arguments.
     */
    public function destroy(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", ApplicationBase::PRIORITY_LOW);
    }
    /**
     * Disables the draggable.
     * 
     * 	* This method does not accept any arguments.
     */
    public function disable(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", ApplicationBase::PRIORITY_LOW);
    }
    /**
     * Enables the draggable.
     * 
     * 	* This method does not accept any arguments.
     */
    public function enable(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", ApplicationBase::PRIORITY_LOW);
    }
    /**
     * Retrieves the draggable instance object. If the element does not have
     * an associated instance, undefined is returned.
     * 
     * Unlike other widget methods, instance() is safe to call on any element
     * after the draggable plugin has loaded.
     * 
     * 	* This method does not accept any arguments.
     */
    public function instance(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", ApplicationBase::PRIORITY_LOW);
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
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, ApplicationBase::PRIORITY_LOW);
    }
    /**
     * Gets an object containing key/value pairs representing the current
     * draggable options hash.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function option1(): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Sets the value of the draggable option associated with the specified
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
     * @param string $value
     */
    public function option2(string $optionName, string $value): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, $value, ApplicationBase::PRIORITY_LOW);
    }

    /**
     * Sets one or more options for the draggable.
     *
     *    * options Type: Object A map of option-value pairs to set.
     * @param array $options
     */
    public function option3(array $options): void
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, ApplicationBase::PRIORITY_LOW);
    }


    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'AddClasses': return $this->blnAddClasses;
            case 'AppendTo': return $this->mixAppendTo;
            case 'Axis': return $this->strAxis;
            case 'Cancel': return $this->mixCancel;
            case 'Classes': return $this->mixClasses;
            case 'ConnectToSortable': return $this->mixConnectToSortable;
            case 'Containment': return $this->mixContainment;
            case 'Cursor': return $this->strCursor;
            case 'CursorAt': return $this->mixCursorAt;
            case 'Delay': return $this->intDelay;
            case 'Disabled': return $this->blnDisabled;
            case 'Distance': return $this->intDistance;
            case 'Grid': return $this->arrGrid;
            case 'Handle': return $this->mixHandle;
            case 'Helper': return $this->mixHelper;
            case 'IframeFix': return $this->mixIframeFix;
            case 'Opacity': return $this->intOpacity;
            case 'RefreshPositions': return $this->blnRefreshPositions;
            case 'Revert': return $this->mixRevert;
            case 'RevertDuration': return $this->intRevertDuration;
            case 'Scope': return $this->strScope;
            case 'Scroll': return $this->blnScroll;
            case 'ScrollSensitivity': return $this->intScrollSensitivity;
            case 'ScrollSpeed': return $this->intScrollSpeed;
            case 'Snap': return $this->mixSnap;
            case 'SnapMode': return $this->strSnapMode;
            case 'SnapTolerance': return $this->intSnapTolerance;
            case 'Stack': return $this->mixStack;
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
            case 'AddClasses':
                try {
                    $this->blnAddClasses = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'addClasses', $this->blnAddClasses);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

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

            case 'ConnectToSortable':
                $this->mixConnectToSortable = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'connectToSortable', $mixValue);
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

            case 'IframeFix':
                $this->mixIframeFix = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'iframeFix', $mixValue);
                break;

            case 'Opacity':
                try {
                    $this->intOpacity = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'opacity', $this->intOpacity);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'RefreshPositions':
                try {
                    $this->blnRefreshPositions = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'refreshPositions', $this->blnRefreshPositions);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Revert':
                $this->mixRevert = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'revert', $mixValue);
                break;

            case 'RevertDuration':
                try {
                    $this->intRevertDuration = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'revertDuration', $this->intRevertDuration);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Scope':
                try {
                    $this->strScope = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'scope', $this->strScope);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

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

            case 'Snap':
                $this->mixSnap = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'snap', $mixValue);
                break;

            case 'SnapMode':
                try {
                    $this->strSnapMode = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'snapMode', $this->strSnapMode);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'SnapTolerance':
                try {
                    $this->intSnapTolerance = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'snapTolerance', $this->intSnapTolerance);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Stack':
                $this->mixStack = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'stack', $mixValue);
                break;

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
            new QModelConnectorParam (get_called_class(), 'AddClasses', 'If set to false, it will prevent the ui-draggable class from being added. This may be desired as a performance optimization when calling.draggable() on hundreds of elements.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'Axis', 'Constrains dragging to either the horizontal (x) or vertical (y) axis.Possible values: \"x\", \"y\".', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'Cursor', 'The CSS cursor during the drag operation.', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'Delay', 'Time in milliseconds after mousedown until dragging should start. This option can be used to prevent unwanted drags when clicking on an element. (version deprecated: 1.12)', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the draggable if set to true.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'Distance', 'Distance in pixels after mousedown the mouse must move before dragging should start. This option can be used to prevent unwanted drags when clicking on an element. (version deprecated: 1.12)', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Grid', 'Snap the dragging helper to a grid, every x and y pixels. The array must be of the form [ x, y ].', Type::ARRAY_TYPE),
            new QModelConnectorParam (get_called_class(), 'Opacity', 'Opacity for the helper while being dragged.', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'RefreshPositions', 'If set to true, all droppable positions are calculated on every mousemove. _Caution: This solves issues on highly dynamic pages dramatically decreases performance._', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'RevertDuration', 'The duration of the revert animation, in milliseconds. Ignored if the revert option is false.', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Scope', 'Used to group sets of draggable and droppable items, addition-droppable accept option. A draggable with the same scope value as a droppable will be accepted by the droppable.', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'Scroll', 'If set to true, the container auto-scrolls while dragging.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'ScrollSensitivity', 'Distance in pixels from the edge of the viewport after which the viewport should scroll. Distance is relative to a pointer, not the draggable. Ignored if the scroll option is false.', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'ScrollSpeed', 'The speed at which the window should scroll once the mouse pointer gets within the scrollSensitivity distance. Ignored if the scroll option is false.', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'SnapMode', 'Determines which edges of snap elements the draggable will snap to.Ignored if the snap option is false. Possible values: \"inner\",\"outer\", \"both\".', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'SnapTolerance', 'The distance in pixels from the snap element edges at which snapping should occur. Ignored if the snap option is false.', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'ZIndex', 'Z-index for the helper while being dragged.', Type::INTEGER),
        ));
    }
}
