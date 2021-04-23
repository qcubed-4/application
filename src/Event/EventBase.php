<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Event;

use QCubed\Action\ActionBase;
use QCubed\Control\ControlBase;
use QCubed\Control\Proxy;
use QCubed\Exception\Caller;
use QCubed\ObjectBase;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class Base
 *
 * Events are used in conjunction with actions to respond to user actions, like clicking, typing, etc.,
 * or even programmable timer events.
 *
 * @property-read string $EventName the javascript event name that will be fired
 * @property-read string $Condition a javascript condition that is tested before the event is sent
 * @property-read integer $Delay ms delay before action is fired
 * @property-read string $JsReturnParam the javascript used to create the strParameter that gets sent to the event handler registered with the event.
 * @property-read string $Selector a jquery selector, causes the event to apply to child items matching the selector, and then get sent up the chain to this object
 * @property-read boolean $Block indicates that other events after this event will be thrown away until the browser receives a response from this event.
 * @package QCubed\Event
 * @was QEvent
 */
abstract class EventBase extends ObjectBase
{
    /** @var string|null The JS condition in which an event would fire */
    protected $strCondition = null;
    /** @var int|mixed The number of second after which the event has to be fired */
    protected $intDelay = 0;
    protected $strSelector = null;
    /** @var  boolean True to block all other events until a response is received. */
    protected $blnBlock;
    /** @var  ActionBase[] Used by the control mechanism to group actions by event. */
    protected $objActions;

    /**
     * Create an event.
     * @param integer $intDelay ms delay to wait before action is fired
     * @param string $strCondition javascript condition to check before firing the action
     * @param string $strSelector jquery selector to cause event to be attached to child items instead of this item
     * @param boolean $blnBlockOtherEvents True to "debounce" the event by throwing away all other events until the browser receives a response from this event.
     *                            Only use this on Server and Ajax events. Do not use on Javascript events, or the browser will stop responding to Ajax and Server events.
     * @throws Caller
     */
    public function __construct($intDelay = 0, $strCondition = null, $strSelector = null, $blnBlockOtherEvents = false)
    {
        try {
            if ($intDelay) {
                $this->intDelay = Type::cast($intDelay, Type::INTEGER);
            }
            if ($strCondition) {
                if ($this->strCondition) {
                    $this->strCondition = sprintf('(%s) && (%s)', $this->strCondition, $strCondition);
                } else {
                    $this->strCondition = Type::cast($strCondition, Type::STRING);
                }
            }
            if ($strSelector) {
                $this->strSelector = $strSelector;
            }
            $this->blnBlock = $blnBlockOtherEvents;
        } catch (Caller $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
    }

    /**
     * Used internally to set the actions for an event.
     *
     * @param ActionBase[] $objActions
     * @internal
     */
    public function setActions(array $objActions)
    {
        $this->objActions = $objActions;
    }

    /**
     * Returns the actions triggered by this event.
     *
     * @return ActionBase[]
     */
    public function getActions() {
        return $this->objActions;
    }

    /**
     * Renders the actions associated with the events as javascript.
     * @param ControlBase $objControl
     * @return string
     */
    public function renderActions(ControlBase $objControl) {
        if (!$this->objActions) {
            return '';
        }

        $strJs = '';
        $strJqUiProperty = null;

        if ($objControl->ActionsMustTerminate) {
            $strJs .= 'event.preventDefault();' . _nl();
        }

        foreach ($this->objActions as $objAction) {
            $strJs .= $objAction->renderScript($objControl) . ';' . _nl();
        }

        if ($this->blnBlock) {
            $strJs .=  'qc.blockEvents = true;' . _nl();
        }


        if ($this->intDelay > 0) {
            $strJs = sprintf(" qc.setTimeout('%s', \$j.proxy(function(){%s},this), %s);",
                $objControl->ControlId,
                _nl() . _indent(trim($strJs)) . _nl(),
                $this->intDelay);
        }

        // Add Condition (if applicable)
        if ($this->strCondition) {
            $strJs = sprintf(' if (%s) {%s}', $this->strCondition,
                _nl() . _indent(trim($strJs)) . _nl());
        }

        $strJs = _indent($strJs);

        $strEventName = $this->EventName;

        if ($objControl instanceof Proxy) {
            $strJs = sprintf('$j("#%s").on("%s", "[data-qpxy=\'%s\']", function(event, ui){%s});',
                $objControl->Form->FormId, $strEventName, $objControl->ControlId, $strJs);
        } else {
            $strJs = sprintf('$j("#%s").on("%s", function(event, ui){%s});',
                $objControl->getJqControlId(),
                $strEventName, $strJs);
        }

        if (!Application::instance()->minimize()) {
            // Render a comment
            $strJs = _nl() . _nl() .
                sprintf('/*** Event: %s  Control Type: %s, Control Name: %s, Control Id: %s  ***/',
                    $strEventName, get_class($objControl), $objControl->Name, $objControl->ControlId) .
                _nl() .
                _indent($strJs) .
                _nl() . _nl();
        }
        return $strJs;

    }


    /**
     * The PHP Magic function for this class
     * @param string $strName Name of the property to fetch
     *
     * @return int|mixed|null|string
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case 'EventName':
                $strEvent = constant(get_class($this) . '::EVENT_NAME');
                if ($this->strSelector) {
                    $strEvent .= '","' . addslashes($this->strSelector);
                }
                return $strEvent;
            case 'Condition':
                return $this->strCondition;
            case 'Delay':
                return $this->intDelay;
            case 'JsReturnParam':
                $strConst = get_class($this) . '::JS_RETURN_PARAM';
                return defined($strConst) ? constant($strConst) : '';
            case 'Selector':
                return $this->strSelector;
            case 'Block':
                return $this->blnBlock;

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





