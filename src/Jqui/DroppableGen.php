<?php
namespace QCubed\Jqui;

use QCubed;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class DroppableGen
 *
 * This is the DroppableGen class which is automatically generated
 * by scraping the JQuery UI documentation website. As such, it includes all the options
 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
 * the DroppableBase class for any glue code to make this class more
 * usable in QCubed.
 *
 * @see DroppableBase
 * @package QCubed\Jqui
 * @property mixed $Accept
 * Controls which draggable elements are accepted by the
 * droppable.Multiple types supported:
 * 
 * 	* Selector: A selector indicating which draggable elements are
 * accepted.
 * 	* Function: A function that will be called for each draggable on the
 * page (passed as the first argument to the function). The function must
 * return true if the draggable should be accepted.
 * 

 *
 * @property string $ActiveClass
 * If specified, the class will be added to the droppable while an
 * acceptable draggable is being dragged.
 * 
 * The activeClass option has been deprecated in favor of the classes
 * option, using the ui-droppable-active property.
 * (version deprecated: 1.12)
 *
 * @property boolean $AddClasses
 * If set to false, will prevent the ui-droppable class from being added.
 * This may be desired as a performance optimization when calling
 * .droppable() init on hundreds of elements.
 *
 * @property mixed $Classes
 * Specify additional classes to add to the widgets elements. Any of
 * classes specified in the Theming section can be used as keys to
 * override their value. To learn more about this option, check out the
 * learn article about the classes option.

 *
 * @property boolean $Disabled
 * Disables the droppable if set to true.
 *
 * @property boolean $Greedy
 * By default, when an element is dropped on nested droppables, each
 * droppable will receive the element. However, by setting this option to
 * true, any parent droppables will not receive the element. The drop
 * event will still bubble normally, but the event.target can be checked
 * to see which droppable received the draggable element.
 *
 * @property string $HoverClass
 * If specified, the class will be added to the droppable while an
 * acceptable draggable is being hovered over the droppable.
 * 
 * The hoverClass option has been deprecated in favor of the classes
 * option, using the ui-droppable-hover property.
 * (version deprecated: 1.12)
 *
 * @property string $Scope
 * Used to group sets of draggable and droppable items, in addition to
 * the accept option. A draggable with the same scope value as a
 * droppable will be accepted.
 *
 * @property string $Tolerance
 * Specifies which mode to use for testing whether a draggable is
 * hovering over a droppable. Possible values: 
 * 
 * 	* "fit": Draggable overlaps the droppable entirely.
 * 	* "intersect": Draggable overlaps the droppable at least 50% in both
 * directions.
 * 	* "pointer": Mouse pointer overlaps the droppable.
 * 	* "touch": Draggable overlaps the droppable any amount.
 * 

 *
 * @was QDroppableGen

 */

abstract class DroppableGen extends QCubed\Project\Control\ControlBase
{
    protected $strJavaScripts = QCUBED_JQUI_JS;
    protected $strStyleSheets = QCUBED_JQUI_CSS;
    /** @var mixed */
    protected $mixAccept = null;
    /** @var string */
    protected $strActiveClass = null;
    /** @var boolean */
    protected $blnAddClasses = null;
    /** @var mixed */
    protected $mixClasses = null;
    /** @var boolean */
    protected $blnDisabled = null;
    /** @var boolean */
    protected $blnGreedy = null;
    /** @var string */
    protected $strHoverClass = null;
    /** @var string */
    protected $strScope = null;
    /** @var string */
    protected $strTolerance = null;

    /**
     * Builds the option array to be sent to the widget constructor.
     *
     * @return array key=>value array of options
     */
    protected function makeJqOptions() {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->Accept)) {$jqOptions['accept'] = $val;}
        if (!is_null($val = $this->ActiveClass)) {$jqOptions['activeClass'] = $val;}
        if (!is_null($val = $this->AddClasses)) {$jqOptions['addClasses'] = $val;}
        if (!is_null($val = $this->Classes)) {$jqOptions['classes'] = $val;}
        if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
        if (!is_null($val = $this->Greedy)) {$jqOptions['greedy'] = $val;}
        if (!is_null($val = $this->HoverClass)) {$jqOptions['hoverClass'] = $val;}
        if (!is_null($val = $this->Scope)) {$jqOptions['scope'] = $val;}
        if (!is_null($val = $this->Tolerance)) {$jqOptions['tolerance'] = $val;}
        return $jqOptions;
    }

    /**
     * Return the JavaScript function to call to associate the widget with the control.
     *
     * @return string
     */
    public function getJqSetupFunction()
    {
        return 'droppable';
    }


    /**
     * Removes the droppable functionality completely. This will return the
     * element back to its pre-init state.
     * 
     * 	* This method does not accept any arguments.
     */
    public function destroy()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", Application::PRIORITY_LOW);
    }
    /**
     * Disables the droppable.
     * 
     * 	* This method does not accept any arguments.
     */
    public function disable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", Application::PRIORITY_LOW);
    }
    /**
     * Enables the droppable.
     * 
     * 	* This method does not accept any arguments.
     */
    public function enable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", Application::PRIORITY_LOW);
    }
    /**
     * Retrieves the droppables instance object. If the element does not have
     * an associated instance, undefined is returned.
     * 
     * Unlike other widget methods, instance() is safe to call on any element
     * after the droppable plugin has loaded.
     * 
     * 	* This method does not accept any arguments.
     */
    public function instance()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", Application::PRIORITY_LOW);
    }
    /**
     * Gets the value currently associated with the specified optionName.
     * 
     * Note: For options that have objects as their value, you can get the
     * value of a specific key by using dot notation. For example, "foo.bar"
     * would get the value of the bar property on the foo option.
     * 
     * 	* optionName Type: String The name of the option to get.
     * @param $optionName
     */
    public function option($optionName)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, Application::PRIORITY_LOW);
    }
    /**
     * Gets an object containing key/value pairs representing the current
     * droppable options hash.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function option1()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", Application::PRIORITY_LOW);
    }
    /**
     * Sets the value of the droppable option associated with the specified
     * optionName.
     * 
     * Note: For options that have objects as their value, you can set the
     * value of just one property by using dot notation for optionName. For
     * example, "foo.bar" would update only the bar property of the foo
     * option.
     * 
     * 	* optionName Type: String The name of the option to set.
     * 	* value Type: Object A value to set for the option.
     * @param $optionName
     * @param $value
     */
    public function option2($optionName, $value)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, $value, Application::PRIORITY_LOW);
    }
    /**
     * Sets one or more options for the droppable.
     * 
     * 	* options Type: Object A map of option-value pairs to set.
     * @param $options
     */
    public function option3($options)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, Application::PRIORITY_LOW);
    }


    public function __get($strName)
    {
        switch ($strName) {
            case 'Accept': return $this->mixAccept;
            case 'ActiveClass': return $this->strActiveClass;
            case 'AddClasses': return $this->blnAddClasses;
            case 'Classes': return $this->mixClasses;
            case 'Disabled': return $this->blnDisabled;
            case 'Greedy': return $this->blnGreedy;
            case 'HoverClass': return $this->strHoverClass;
            case 'Scope': return $this->strScope;
            case 'Tolerance': return $this->strTolerance;
            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case 'Accept':
                $this->mixAccept = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'accept', $mixValue);
                break;

            case 'ActiveClass':
                try {
                    $this->strActiveClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'activeClass', $this->strActiveClass);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'AddClasses':
                try {
                    $this->blnAddClasses = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'addClasses', $this->blnAddClasses);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Classes':
                $this->mixClasses = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'classes', $mixValue);
                break;

            case 'Disabled':
                try {
                    $this->blnDisabled = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'disabled', $this->blnDisabled);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Greedy':
                try {
                    $this->blnGreedy = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'greedy', $this->blnGreedy);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'HoverClass':
                try {
                    $this->strHoverClass = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'hoverClass', $this->strHoverClass);
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

            case 'Tolerance':
                try {
                    $this->strTolerance = Type::Cast($mixValue, Type::STRING);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'tolerance', $this->strTolerance);
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
    **/
    public static function getModelConnectorParams()
    {
        return array_merge(parent::GetModelConnectorParams(), array(
            new QModelConnectorParam (get_called_class(), 'ActiveClass', 'If specified, the class will be added to the droppable while anacceptable draggable is being dragged.The activeClass option has been deprecated in favor of the classesoption, using the ui-droppable-active property.(version deprecated: 1.12)', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'AddClasses', 'If set to false, will prevent the ui-droppable class from being added.This may be desired as a performance optimization when calling.droppable() init on hundreds of elements.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the droppable if set to true.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'Greedy', 'By default, when an element is dropped on nested droppables, eachdroppable will receive the element. However, by setting this option totrue, any parent droppables will not receive the element. The dropevent will still bubble normally, but the event.target can be checkedto see which droppable received the draggable element.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'HoverClass', 'If specified, the class will be added to the droppable while anacceptable draggable is being hovered over the droppable.The hoverClass option has been deprecated in favor of the classesoption, using the ui-droppable-hover property.(version deprecated: 1.12)', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'Scope', 'Used to group sets of draggable and droppable items, in addition tothe accept option. A draggable with the same scope value as adroppable will be accepted.', Type::STRING),
            new QModelConnectorParam (get_called_class(), 'Tolerance', 'Specifies which mode to use for testing whether a draggable ishovering over a droppable. Possible values: 	* \"fit\": Draggable overlaps the droppable entirely.	* \"intersect\": Draggable overlaps the droppable at least 50% in bothdirections.	* \"pointer\": Mouse pointer overlaps the droppable.	* \"touch\": Draggable overlaps the droppable any amount.', Type::STRING),
        ));
    }
}
