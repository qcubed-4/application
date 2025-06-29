<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Action\ActionBase;
use QCubed\Action\ActionParams;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed as Q;
use QCubed\Type;

/**
 * Class JsTimerBase
 *
 * Timer Control:
 * This control uses a JavaScript timer to execute Actions after a defined time
 * Periodic or one shot timers are possible.
 * You can add only one type of Event to this control: TimerExpiredEvent,
 * but multiple actions can be registered for this event
 * @property int $DeltaTime Time till the timer fires and executes the Actions added.
 * @property boolean $Periodic  <ul>
 *                      <li><strong>true</strong>: timer is restarted after firing</li>
 *                      <li><strong>false</strong>: you have to restart the timer by calling Start()</li>
 *                              </ul>
 *
 * @property boolean $Started <strong>true</strong>: timer is running / <strong>false</strong>: stopped
 * @property boolean $RestartOnServerAction After a 'Server Action' (QServerAction) the executed JavaScript
 *                                                        (including the timer) is stopped!
 *                                                        Set this parameter to true to restart the timer automatically.
 * @notes <ul><li>You do not need to render this control!</li>
 *            <li>QTimerExpiredEvent - condition and delay parameters of the constructor are ignored (for now) </li>
 * @package QCubed\Event
 */
class JsTimerBase extends Q\Project\Control\ControlBase
{
    // Values determining the state of the timer
    /** Constant used to indicate that the timer has stopped */
    const STOPPED = 0;
    /** Constant used to indicate that the timer has started */
    const STARTED = 1;
    /** Constant used to indicate that the timer has autostarted enabled (starts with the page load) */


    const AUTO_START = 2;

    /** @var bool does the timer run periodically once started? */
    protected bool $blnPeriodic = true;
    /** @var int The duration after which the timer will fire (in milliseconds) */
    protected int $intDeltaTime = 0;
    /** @var int default state in which timer will be (stopped) */
    protected int $intState = self::STOPPED;
    /** @var bool should the timer start after a QServerAction occurs? */
    protected ?bool $blnRestartOnServerAction = false;


    /**
     * @param ControlBase|FormBase $objParentObject the form or parent control
     * @param int|null $intTime timer interval in ms
     * @param boolean $blnPeriodic if true, the timer is "restarted" automatically after it has fired
     * @param boolean $blnStartNow starts the timer automatically after adding the first action
     * @param string|null $strTimerId
     *
     * @throws Caller
     */
    public function __construct(
        FormBase|ControlBase    $objParentObject,
        ?int                     $intTime = 0,
        bool                    $blnPeriodic = true,
        bool                    $blnStartNow = true,
        ?string                 $strTimerId = null
    ) {
        try {
            parent::__construct($objParentObject, $strTimerId);
            $this->blnAutoRender = true;    // don't need to explicitly render a timer
            $this->blnUseWrapper = false; // never use a wrapper!
        } catch (Caller $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }

        $this->intDeltaTime = $intTime;
        $this->blnPeriodic = $blnPeriodic;
        if ($intTime != self::STOPPED && $blnStartNow) {
            $this->intState = self::AUTO_START;
        } //prepare to start the timer after the first action gets added
    }

    /**
     * Returns the callback string
     * @return string
     */
    private function callbackString(): string
    {
        return "qcubed._objTimers['" . $this->strControlId . "_cb']";
    }

    /**
     * Returns a timer ID (string) as an element of the 'qcubed._objTimers' JavaScript array.
     * This array is used to start and stop timers (and keep track)
     * @return string
     */
    private function tidString(): string
    {
        return "qcubed._objTimers['" . $this->strControlId . "_tId']";
    }

    /**
     * @param ActionParams|null $intTime (optional)
     *              Sets the interval/delay, after that the timer executes the registered actions
     *              if no parameter is given the time stored in $intDeltaTime is used
     * @return void
     */
    public function start(?ActionParams $intTime = null): void
    {
        $this->stop();
        if ($intTime != null && is_int($intTime)) {
            $this->intDeltaTime = $intTime;
        }
        Application::executeJsFunction('qc.startTimer', $this->strControlId, $this->intDeltaTime, $this->blnPeriodic);

        // Is the timer periodic or runs only once?
        /*
        if ($this->blnPeriodic) {
            // timer is periodic. We will set the interval
            $strJS = $this->tidString() . ' = window.setInterval("' . $this->callbackString() . '()", ' . $this->intDeltaTime . ');';
        } else {
            // timer is not periodic. We will set the timeout
            $strJS = $this->tidString() . ' = window.setTimeout("' . $this->callbackString() . '()", ' . $this->intDeltaTime . ');';
        }
        Application::executeJavaScript($strJS);*/
        $this->intState = self::STARTED;
    }

    /**
     * Stops the timer
     */
    public function stop(): void
    {
        Application::executeJsFunction('qc.stopTimer', $this->strControlId, $this->blnPeriodic);
        $this->intState = self::STOPPED;
    }

    /**
     * Adds an action to the control
     *
     * @param Q\Event\EventBase $objEvent has to be an instance of QTimerExpiredEvent
     * @param Q\Action\ActionBase $objAction Only a QTimerExpiredEvent can be added,
     *                                         but multiple Actions using the same event are possible!
     *
     * @throws Caller
     * @return void
     */
    public function addAction(Q\Event\EventBase $objEvent, Q\Action\ActionBase $objAction): void
    {
        if (!($objEvent instanceof Q\Event\TimerExpired)) {
            throw new Caller('The first parameter of JsTimer::AddAction is expecting an object of type Event\\TimerExpired');
        }

        parent::addAction($objEvent, $objAction);

        if ($this->intState === self::AUTO_START && $this->intDeltaTime != 0) {
            $this->start();
        }
    }

    /**
     * Returns all actions connected/attached to the timer
     * @param string $strEventName
     * @param string|null $strActionClass
     * @return ActionBase[]
     */
    public function getAllActions(string $strEventName, ?string $strActionClass = null): array
    {
        if (($strEventName == Q\Event\TimerExpired::EVENT_NAME && !$this->blnPeriodic) &&
            (($strActionClass == '\QCubed\Action\Ajax' && Application::isAjax()) ||
                ($strActionClass == '\QCubed\Action\Server' && Application::instance()->context()->requestMode() == Q\Context::REQUEST_MODE_QCUBED_SERVER))
        ) {
            //if we are in an ajax or server post and our timer is not periodic
            //and this method gets called, then the timer has finished(stopped) --> set the State flag to "stopped"
            $this->intState = self::STOPPED;
        }
        return parent::getAllActions($strEventName, $strActionClass);
    }

    /**
     * Remove all actions attached to the timer
     * @param string|null $strEventName
     */
    public function removeAllActions(?string $strEventName = null): void
    {
        $this->stop(); //no actions are registered for this timer to stop it
        parent::removeAllActions($strEventName);
    }

    /**
     * Returns all JavaScript that needs to be executed after rendering of this control
     * (It overrides the GetEndScript of the parent to handle a specific case of JsTimers)
     * @return string
     */
    public function getEndScript(): string
    {
        if (Application::instance()->context()->requestMode() == Q\Context::REQUEST_MODE_QCUBED_SERVER) {
            //this point is not reached on initial rendering
            if ($this->blnRestartOnServerAction && $this->intState === self::STARTED) {
                $this->start();
            } //restart after a server action
            else {
                $this->intState = self::STOPPED;
            }
        }

        return parent::getEndScript();
    }

    /**
     * PHP magic function to get value of properties of an object of this class
     * @param string $strName Name of the properties
     *
     * @return mixed
     * @throws Caller
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'DeltaTime':
                return $this->intDeltaTime;
            case 'Periodic':
                return $this->blnPeriodic;
            case 'Started':
                return ($this->intState === self::STARTED);
            case 'RestartOnServerAction':
                return $this->blnRestartOnServerAction;
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
     * PHP Magic function to set property values for an object of this class
     * @param string $strName Name of the property
     * @param mixed $mixValue Value of the property
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case "DeltaTime":
                try {
                    $this->intDeltaTime = Type::cast($mixValue, Type::INTEGER);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'Periodic':
                try {
                    $newMode = Type::cast($mixValue, Type::BOOLEAN);
                    if ($this->blnPeriodic != $newMode) {
                        if ($this->intState === self::STARTED) {
                            $this->stop();
                            $this->blnPeriodic = $newMode;
                            $this->start();
                        } else {
                            $this->blnPeriodic = $newMode;
                        }
                    }
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
            case 'RestartOnServerAction':
                try {
                    $this->blnRestartOnServerAction = Type::cast($mixValue, Type::BOOLEAN);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
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

    /**
     * Add a child control to the current control (useless because JsTimer cannot have children)
     * @param ControlBase $objControl
     *
     * @throws Caller
     */
    public function addChildControl(ControlBase $objControl): void
    {
        throw new Caller('Do not add child-controls to an instance of JsTimer!');
    }

    /**
     * Get the HTML for the control (blank in this case because JsTimer cannot be rendered)
     * @return string
     */
    protected function getControlHtml(): string
    {
        return parent::renderTag('span');   // render invisible tag so we get a control id in JavaScript to attach events to
    }

    /**
     * This function would typically parse the data posted back by the control.
     */
    public function parsePostData(): void
    {
    }

    /**
     * Validation logic for control. Since we never render, we must return true to continue using the control.
     * @return bool
     */
    public function validate(): bool
    {
        return true;
    }
}
