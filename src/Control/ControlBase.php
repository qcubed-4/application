<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Action\ActionParams;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Html;
use QCubed\Exception;
use QCubed as Q;
use QCubed\Project\Application;
use QCubed\Action\ActionBase as QAction;
use QCubed\Event\EventBase;
use QCubed\Project\Jqui\Draggable;
use QCubed\Project\Jqui\Droppable;
use QCubed\Project\Jqui\Resizable;
use QCubed\Query\Node\NodeBase;
use QCubed\TagStyler;
use QCubed\Type;
use QCubed\Project\Watcher\Watcher as Watcher;
use QCubed\ModelConnector\Param as ModelConnectorParam;
use QCubed\Watcher\WatcherBase;
use ReflectionClass;
use Throwable;
use ReflectionException;

/**
 * This is the base class of all QCubed Controls and shares their common properties.
 *
 * Not every control will utilize every single one of these properties.
 *
 * All Controls must implement the following abstract functions:
 * <ul>
 *        <li>{@link Base::getControlHtml()}</li>
 *        <li>{@link Base::parsePostData()}</li>
 *        <li>{@link Base::validate()}</li>
 * </ul>
 *
 * A QCubed Control conceptually is an object in an HTML form that manages data or that can be controlled via PHP.
 * In the early days of the internet, this was simply an HTML input or select tag that was submitted via a POST.
 * As the internet has evolved, so has QCubed Controls, but the basic idea is the same. Its object on the screen that
 * you would like to either control from PHP or receive information from. The parts of a QCubed Control that are
 * sent to the browser are:
 *  - The base tag and its contents, as returned by GetControlHtml(). This would be an Input tag, or a Button, or
 *    even just a div. Many JavaScript widget libraries will take a div and add to it to create a control. The tag
 *    will include an ID in all cases. If you do not assign one, a unique ID will be created automatically.
 *  - An optional Name, often sent to the browser in a Label tag.
 *  - Optional instructions
 *  - Optional validation error text
 *  - Optional JavaScript attached to the control as part of its inherited functionality, or to control settable options
 *    that are handled by a jQuery wrapper function of some kind.
 *  - Optional JavaScript attached to the control through the AddActions mechanism.
 *
 * You control how these parts are rendered by implementing Render* methods in your own QCubed Control class. Some basic
 * ones are included in this class for you to start with.
 *
 * Depending on the control and the application, a control may need or want to be rendered with a wrapper tag,
 * controlled by the blnUseWrapper member. For example, if you want a form object with a name,
 * instructions, and error text, you may need a wrapper to make sure all of these parts are redrawn when something changes
 * the control. Bootstrap's formObjectGroup is an example of a control that has all of these parts.
 * Also, if you know that a JavaScript widget library is going to wrap your HTML in additional HTML,
 * you should include a wrapper here so the additional HTML is included inside your wrapper, and thus the entire
 * control will get redrawn on a refresh (jQueryI's Dialog is an example of this kind of widget.)
 *
 * QCubed Controls are part of a tree type hierarchy, whose parent can either be a FormBase or another QCubed Control.
 *
 * The QCubed Control system is designed to manage the process of redrawing a control automatically when something about
 * the control changes. You can force a redrawing by using the Refresh command from the outside of a control or by setting
 * the blnModified member variable from a subclass. You can also use the Watcher mechanism to automatically redraw
 * when something in the database changes.
 *
 * QCubed Controls are the base objects for actions to be attached to events. When attaching actions to multiple objects
 * of the same type, consider attaching the event to a parent object and using event delegation for your action,
 * as it can be more efficient in certain cases.
 *
 * QCubed Controls can trigger validation and are part of the validation system. QCubed Controls that are not Enabled or not
 * Visible will not go through the form's Validation routine.
 *
 * Controls can be made visible using either the Visible or Display PHP parameters. Both are booleans.
 * - Setting Visible to false completely removes the control from the DOM, leaving either just its
 *   wrapper or an invisible span stub in its place. When the control is made visible again, it is entirely
 *   redrawn.
 * - Setting Display to false leaves the control in the DOM but simply sets its display property to 'none' in CSS.
 *   Show and hide are much faster.
 *
 * @property-read boolean $ActionsMustTerminate Prevent the default action from happening upon an event trigger. See documentation for "protected $blnActionsMustTerminate" below.
 * @property-read boolean $ScriptsOnly Whether the control only generates JavaScripts and not HTML.
 * @property mixed $ActionParameter This property allows you to pass your own parameters to the handlers for actions applied to this control.
 *             this can be a string or an object of type Q\Js\Closure. If you pass in a Q\Js\Closure, it is possible to return JavaScript objects/arrays
 *             when using an ajax or server action.
 * @property mixed $CausesValidation flag says whether or not the form should run through its validation routine if this control has an action defined and is acted upon
 * @property-read string $ControlId returns the ID of this control
 * @property-read FormBase $Form returns the parent form object
 * @property-read array $FormAttributes
 * @property string $HtmlAfter HTML that is shown after the control {@link ControlBase::RenderWithName}
 * @property string $HtmlBefore HTML that is shown before the control {@link ControlBase::RenderWithName}
 * @property string $Instructions instructions that is shown next to the control's name label {@link ControlBase::RenderWithName}
 * @property-read string $JavaScripts
 * @property-read boolean $Modified indicates if the control has been changed. Used to tell Qcubed to rerender the control or not (Ajax calls).
 * @property boolean $Moveable
 * @property boolean $Resizable
 * @property string $Name sets the Name of the Control (see {@link ControlBase::RenderWithName})
 * @property-read boolean $OnPage is true if the control is connected to the form
 * @property-read FormBase|ControlBase $ParentControl returns the parent control
 * @property-read boolean $Rendered
 * @property-read boolean $Rendering
 * @property-read string $RenderMethod carries the name of the function, which were initially used for rendering
 * @property string $PreferredRenderMethod carries the name of the function, which were initially used for rendering
 * @property boolean $Required specifies whether or not this is required (will cause a validation error if the form is trying to be validated and this control is left blank)
 * @property-read string $StyleSheets
 * @property string $ValidationError is the string that contains the validation error (if applicable) or will be blank if (1) the form did not undergo its validation routine or (2) this control had no error
 * @property boolean $Visible specifies whether or not the control should be rendered in the page.  This is in contrast to Display, which will just hide the control via CSS styling.
 * @property  string $Warning is the warning text that is displayed next to the control's name tag {@link ControlBase::RenderWithName}
 * @property boolean $UseWrapper defaults to true
 * @property NodeBase $LinkedNode A database node that this control is directly editing
 * @property-read boolean $WrapperModified
 * @property string $WrapperCssClass
 * @property boolean $WrapLabel For checkboxes, radio buttons, and similar controls, whether to wrap the label around
 *        the control, or place the label next to the control. Two legal styles of label creation that different CSS and JS frameworks expect.
 * @property-write boolean $SaveState set to true to have the control remember its state between visits to the form that the control is on.
 * @property boolean $Minimize True to force the entire control and child controls to draw minimized. This is helpful when drawing inline-block items to prevent spaces from appearing between them.
 * @property boolean $AutoRender true to have the control be automatically rendered without an explicit "Render..." call. This is used by Dialogs.
 *        and other similar controls that are controlled via JavaScript and generally start out hidden on the page. These controls
 *        are appended to the form after all other controls.
 * @was QControlBase
 */
abstract class ControlBase extends Q\Project\HtmlAttributeManager
{

    public const COMMENT_START = 'Begin';
    public const COMMENT_END = 'End';

    /**
     * Contains The 'CausesValidation' property options
     * used mostly by buttons which take actions on Forms and controls.
     */

    public const CAUSES_VALIDATION_NONE = false;
    public const CAUSES_VALIDATION_ALL = true;
    public const CAUSES_VALIDATION_SIBLINGS_AND_CHILDREN = 2;
    public const CAUSES_VALIDATION_SIBLINGS_ONLY = 3;

    public const ACTION_PARAM = 'Param';
    public const ACTION_ORIGINAL_PARAM = 'OriginalParam';
    public const ACTION_OBJ = 'Action';
    public const ACTION_CONTROL_ID = 'ControlId';
    public const ACTION_FORM_ID = 'FormId'; // This not really used. It is here in the event we ever support more than one form on a page.

    /**
     * Protected members
     */

    /** @var TagStyler|null */
    protected ?TagStyler $objWrapperStyler = null;
    /** @var mixed Controls how this control will affect the validation system. One of CAUSES_VALIDATION_* constants */
    protected mixed $mixCausesValidation = false;
    /** @var bool Is it mandatory for the control to receive data on a POST back for the control to be called valid? */
    protected bool $blnRequired = false;
    /** @var string|null Tab-index */
    protected ?string $strValidationError = null;
    /** @var bool Should the control be visible or not (it normally effects whether Render method will be called or not) */
    protected bool $blnVisible = true;
    /** @var bool should the control be displayed? */
    protected bool $blnDisplay = true;
    /** @var string Preferred method to be used for rendering e.g. Render, RenderWithName, RenderWithError */
    protected string $strPreferredRenderMethod = 'Render';

    /** @var string|null HTML to render before the actual control */
    protected ?string $strHtmlBefore = null;
    /** @var string|null HTML to render after the actual control */
    protected ?string $strHtmlAfter = null;
    /** @var string|null the Instructions for the control (useful for controls used in data entry) */
    protected ?string $strInstructions = null;
    /** @var string|null Same as a validation error message but is supposed to contain custom messages */
    protected ?string $strWarning = null;

    /** @var Draggable|null When initialized, it implements the jQuery UI Draggable capabilities on to this control. */
    protected ?Draggable $objDraggable = null;
    /** @var Resizable|null When initialized, it implements the jQuery UI Resizable capabilities on to this control. */
    protected ?Resizable $objResizable = null;
    /** @var Droppable|null When initialized, it implements the jQuery UI Droppable capabilities on to this control. */
    protected ?Droppable $objDroppable = null;

    // MISC
    /**
     * @var null|string The control ID of this control. Used to represent the control internally
     *            And used for the HTML 'id' attribute on the control.
     */
    protected ?string $strControlId;
    /** @var FormBase|null A redundant copy of the global $_FORM variable. */
    protected ?FormBase $objForm = null;
    /** @var ControlBase|null An immediate parent of this control, if a control */
    protected ?ControlBase $objParentControl = null;
    /** @var ControlBase[] Controls which have this control as their parent */
    protected array $objChildControlArray = array();
    /** @var string|null Name of the control - used as a label for the control when RenderWithName is used to render */
    protected ?string $strName = null;
    /** @var bool Has the control already been rendered? */
    protected bool $blnRendered = false;
    /** @var bool Is the control in the middle of rendering? */
    protected bool $blnRendering = false;
    /** @var bool Is the control available on the page? Useful when "re-rendering" a control with children. */
    protected bool $blnOnPage = false;
    /** @var bool Has the control been modified? Used mostly in Ajax or Server callbacks */
    protected bool $blnModified = false;
    /** @var bool Has the control's wrapper been modified? Used in Ajax or Server callbacks */
    protected bool $blnWrapperModified = false;
    /** @var string Render method to be used */
    protected string $strRenderMethod = '';
    /** @var string|null Custom HTML attributes for the control */
    protected ?string $strCustomAttributeArray = null;
    /** @var string|null Custom CSS style attributes for the control */
    protected ?string $strCustomStyleArray = null;
    /** @var EventBase[] Array of events we are triggering actions on */
    protected array $objEventArray = array();
    /** @var string|Q\Js\Closure|null The action parameter (typically a small amount of data) for the Ajax or Server Callback */
    protected string|Q\Js\Closure|null $mixActionParameter = null;
    /** @var bool Should the wrapper be used when rendering? */
    protected bool $blnUseWrapper = true;
    /** @var array|null  One-time scripts associated with the control. */
    protected ?array $strAttributeScripts = null;
    /** @var string The INITIAL class for the object. Only subclasses should set this before calling the parent constructor. */
    protected string $strCssClass = '';
    /** @var  bool|null Force this control, and all sub controls to draw minimized. This is important when using inline-block styles, as not doing so will cause spaces between the objects. */
    protected ?bool $blnMinimize = false;

    // SETTINGS
    /** @var string List of JavaScript files to be attached with the control when rendering */
    protected string $strJavaScripts = '';
    /** @var string List of CSS files to be attaches with the control when rendering */
    protected string $strStyleSheets = '';
    /** @var array Form attributes for the control */
    protected array $strFormAttributes = [];
    /**
     * @var bool Should the default action execution stop when and even occur?
     *
     * 1. When a link is clicked which has an action associated with it - the browser will try to
     *    navigate to the link.
     * 2. When someone presses enter on a textbox - the form will try to submit.
     *
     * This variable stops the default behavior (navigation to link / form submission) when set to true.
     * Modification of this variable is to be done by using the 'ActionMustTerminate' property exposed as a property
     */
    protected bool $blnActionsMustTerminate = false;
    /** @var bool True if this control only generates JavaScripts and not HTML. */
    protected bool $blnScriptsOnly = false;
    /** @var bool Is this control a block type element? This determines whether the control will be wrapped in
     *  a div or a span if blnUseWrapper is true. For example, if */
    protected bool $blnIsBlockElement = false;
    /** @var Watcher|null Stores information about watched tables. */
    protected ?Watcher $objWatcher = null;
    /** @var ?Q\Query\Node\NodeBase  Used by the designer to associate a db node with this control */
    protected ?Q\Query\Node\NodeBase $objLinkedNode = null;
    /**
     * @var bool|null For controls that also produce built-in labels (QCheckBox, QCheckBoxList, etc.)
     * True to wrap the checkbox with the label (the Bootstrap way). False to put the label next to the
     * checkbox (the jQueryUI way).
     */
    protected bool $blnWrapLabel = false;

    /** @var bool true to remember the state of this control to restore if the user comes back to it. */
    protected ?bool $blnSaveState = false;

    /** @var bool true to have the control be automatically rendered without an explicit "Render..." call. This is used by Dialogs.
     * and other similar controls that are controlled via JavaScript, and generally start out hidden on the page. These controls
     * are appended to the form after all other controls.
     */
    protected bool $blnAutoRender = false;

    private string $strFormId;
    private string $strParentControlId;

    //////////
    // Methods
    //////////
    /**
     * Creates a ControlBase object
     * This constructor will generally not be used to create a QCubed Control object. Instead, it is used by the
     * classes which extend the class.  Only the parent object parameter is required. If the option strControlId
     * parameter is not used, QCubed will generate the ID.
     *
     * @param ControlBase|FormBase $objParentObject
     * @param string|null $strControlId
     *   an optional ID of this Control. In HTML, this will be set as the value of the ID attribute. The ID can only
     *   contain alphanumeric characters.  If this parameter is not passed, QCubed will generate the ID.
     *
     * @throws Caller
     */
    public function __construct(FormBase|ControlBase $objParentObject, ?string $strControlId = null)
    {
        if ($this->isFormBase($objParentObject)) {
            $this->objForm = $objParentObject;
        } elseif ($this->isControlBase($objParentObject)) {
            $this->objParentControl = $objParentObject;
            $this->objForm = $objParentObject->Form;
        } else {
            throw new Exception\Caller('ParentObject must be either a FormBase or ControlBase object');
        }

        if ($strControlId === null || strlen($strControlId) == 0) {
            $this->strControlId = $this->objForm->generateControlId();
        } else {
            if (ctype_alnum($strControlId)) {
                $this->strControlId = $strControlId;
            } else {
                throw new Exception\Caller('ControlIds must be only alphanumeric characters: ' . $strControlId);
            }
        }

        if ($this->strCssClass) {
            $this->addCssClass($this->strCssClass);
        }

        try {
            $this->objForm->addControl($this);
            $this->objParentControl?->addChildControl($this);
        } catch (Exception\Caller $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }
    }

    /**
     * @param $object
     * @return bool
     */
    private function isFormBase($object): bool
    {
        return $object instanceof FormBase;
    }

    /**
     * @param $object
     * @return bool
     */
    private function isControlBase($object): bool
    {
        return $object instanceof ControlBase;
    }


    /**
     * ParsePostData parses the value of this control from FormState
     *
     * This abstract method must be implemented by all controls.
     *
     * When utilizing formgen, the programmer should never access form variables directly (e.g.,
     * via the $_FORM array). It can be assumed that at *ANY* given time, a control's
     * values/properties will be "up to date" with whatever the webserver has entered in.
     *
     * When a Form is Created via Form::create(string), the form will go through to check and
     * see if it is a first-run of a form, or if it is a post-back.  If it is a postback, it
     * will go through its own private array of controls and call ParsePostData on EVERY control
     * it has.  Each control is responsible for "knowing" how to parse the $_POST data to update
     * its own values/properties based on what was returned to via the postback.
     */
    abstract public function parsePostData(): void;

    /**
     * Checks if this control contains a valid value.
     *
     * This abstract method defines how a control should validate itself based on the value/
     * properties it has. It should also include the handling of ensuring the "Required"
     * requirements are obeyed if this control's "Required" flag is set to true.
     *
     * For Controls that can't realistically be "validated" (e.g., labels, datagrids, etc.),
     * those controls should simply have Validate() return true.
     *
     */
    abstract public function validate(): bool;

    /**
     * Object persistence support.
     */

    /**
     * Save the state of the control to restore it later, so that if the user comes back to this page, the control
     * will be in the showing the same thing. Subclasses should put minimally important information into the state that
     * is needed to restore the state later.
     *
     * This implementation puts the state into the session. Override to provide a different method if so desired.
     *
     * Should normally be called only by Form code. See GetState and PutState for the control side implementation.
     */
    public function _WriteState(): void
    {
        global $_FORM;

        assert($_FORM !== null);
        if (defined('QCUBED_SESSION_SAVED_STATE') && $this->blnSaveState) {
            $formName = get_class($_FORM);    // must use global $_FORM here instead of $this->objForm, since serialization will have nulled the objForm.
            $_SESSION[QCUBED_SESSION_SAVED_STATE][$formName][$this->ControlId] = $this->getState();
        }
    }

    /**
     * Restore the state of the control.
     */
    public function _ReadState(): void
    {
        if (defined('QCUBED_SESSION_SAVED_STATE') && $this->blnSaveState) {
            $formName = get_class($this->objForm);
            if (isset ($_SESSION[QCUBED_SESSION_SAVED_STATE][$formName][$this->ControlId])) {
                $state = $_SESSION[QCUBED_SESSION_SAVED_STATE][$formName][$this->ControlId];
                $this->putState($state);
            }
        }
    }

    /**
     * Control subclasses should return their state data that they will use to restore later.
     * @return array|null
     */
    protected function getState(): ?array
    {
        return null;
    }

    /**
     * Restore the state of the control. The control will have already been
     * created and initialized. Subclasses should verify that the restored state is still valid for the data
     * available.
     * @param mixed $state
     */
    protected function putState(mixed $state): void
    {
    }

    /**
     * Completely forget the saved state for this control.
     */
    public function forgetState(): void
    {
        if (defined('QCUBED_SESSION_SAVED_STATE')) {
            $formName = get_class($this->objForm);
            unset($_SESSION[QCUBED_SESSION_SAVED_STATE][$formName][$this->ControlId]);
        }
    }

    /**
     * A utility function to convert a template file name into a full path.
     *
     * @param string $strTemplate name of template
     * @return string
     */
    public function getTemplatePath(string $strTemplate): string
    {
        // If no path is specified, or a relative path, use the path of the child control's file as the starting point.
        if (!str_starts_with($strTemplate, DIRECTORY_SEPARATOR)) {
            $strOriginalPath = $strTemplate;

            // Try the control's subclass location
            $reflector = new ReflectionClass(get_class($this));
            $strDir = dirname($reflector->getFileName());
            $strTemplate = $strDir . DIRECTORY_SEPARATOR . $strTemplate;

            if (!file_exists($strTemplate)) {
                // Try the control's parent
                if ($this->objParentControl) {
                    $reflector = new ReflectionClass(get_class($this->objParentControl));
                    $strDir = dirname($reflector->getFileName());
                    $strTemplate = $strDir . DIRECTORY_SEPARATOR . $strTemplate;
                }
            }

            if (!file_exists($strTemplate)) {
                // Try the form's location
                $reflector = new ReflectionClass(get_class($this->objForm));
                $strDir = dirname($reflector->getFileName());
                $strTemplate = $strDir . DIRECTORY_SEPARATOR . $strTemplate;

                if (!file_exists($strTemplate)) {
                    $strTemplate = $strOriginalPath;    // not found, but return the original name
                }
            }
        }
        return $strTemplate;
    }

    /**
     * This function evaluates a template and is used by a variety of controls. It is similar to the function found in the
     * Form, but recreated here so that the "$this" in the template will be the control, instead of the form,
     * and the protected members of the control are available to draw directly.
     * @param string $strTemplate Path to the HTML template file
     *
     * @return string|null The evaluated HTML string
     */
    public function evaluateTemplate(string $strTemplate): ?string
    {
        global $_ITEM;        // used by data repeater
        global $_CONTROL;
        global $_FORM;

        if ($strTemplate) {
            $blnProcessing = Application::setProcessOutput(false);
            // Store the Output Buffer locally
            $strAlreadyRendered = ob_get_contents();
            if ($strAlreadyRendered) {
                ob_clean();
            }

            // Evaluate the new template
            ob_start('\\QCubed\\Control\\FormBase::EvaluateTemplate_ObHandler');

            $strTemplate = $this->getTemplatePath($strTemplate);
            require($strTemplate);
            $strTemplateEvaluated = ob_get_contents();
            ob_end_clean();

            // Restore the output buffer and return the evaluated template
            if ($strAlreadyRendered) {
                print($strAlreadyRendered);
            }
            Application::setProcessOutput($blnProcessing);

            return $strTemplateEvaluated;
        }

        return null;
    }

    /**
     * This function passes control of action parameter processing to the control that caused the action so that
     * the control can further process the action parameters.
     * This is useful for widgets that need to pass more information to the action than just a
     * simple string and allows actions to get more information as well. This also allows widgets to modify
     * the action parameter while preserving the original action parameter so that the action can see both.
     *
     * @param ActionParams $params
     * @return void
     */
    public static function _processActionParams(ActionParams $params): void
    {
        $objSourceControl =  $params->Control;
        $objSourceControl->processActionParameters($params);
    }

    /**
     * An opportunity for a control to process the params coming from JavaScript and make them more useful
     * to actions. This also provides additional
     * information to the action about the triggering control.
     *
     * @param ActionParams $params
     * @return void
     */
    protected function processActionParameters(ActionParams $params): void
    {
       // Subclasses can alter the Param property if needed.
    }

    /**
     * Executes a specified action method on a destination control with provided parameters.
     *
     * @param ControlBase $objDestControl The control instance on which the method will be called.
     * @param string $strMethodName The name of the method to invoke on the destination control.
     * @param ActionParams $params The parameters to be passed to the method.
     *                       If the method accepts multiple parameters, the method will receive `FormId`, `ControlId`, and `Param` from $params.
     *                       If the method accepts a single parameter, $params itself will be passed.
     *
     * @return void
     * @throws ReflectionException
     */
    public static function _callActionMethod(ControlBase $objDestControl, string $strMethodName, Q\Action\ActionParams $params): void
    {
        /**
         * To transition to actions that just take a $params object and nothing else, we use reflection
         */

        $ref = new ReflectionClass(get_class($objDestControl));
        $argCount = $ref->getMethod($strMethodName)->getNumberOfParameters();

        if ($argCount > 1) {
            $objDestControl->$strMethodName($params->FormId, $params->Control->ControlId, $params->Param);
        }
        else {
            $objDestControl->$strMethodName($params);
        }
    }

    /**
     * Prepare the control for serialization. All pointers to forms and form objects should be
     * converted to something that can be restored using Wakeup().
     *
     * The main problem we are resolving is that the PHP serialization process will convert an internal reference
     * to the object being serialized into a copy of the object. After deserialization, you would have the form,
     * and then somewhere inside the form, a separate copy of the form. This is a long-standing bug in PHP.
     */
    public function sleep(): array
    {
        $this->objForm = null;

        return [];
    }

    /**
     * Assigns the provided FormBase object to the internal form property.
     *
     * @param FormBase $objForm The form object to be assigned.
     *
     * @return void
     */
    public function wakeup(FormBase $objForm): void
    {
        $this->objForm = $objForm;
    }

    /**
     * A helper function to fix up a 'callable', a formObj, or any other object that we would like to represent
     * in the serialized stream differently than the default. If a QCubed Control, make sure this isn't the only
     * instance of the control in the stream, or have some other way to serialize the control.
     *
     * @param FormBase|ControlBase|array|callable $obj
     * @return mixed
     */
    public static function sleepHelper(mixed $obj): mixed
    {
        if ($obj instanceof FormBase) {
            // assume its form
            return '**QF;';
        } elseif ($obj instanceof ControlBase) {
            return '**QC;' . $obj->strControlId;
        } elseif (is_array($obj)) {
            return array_map(function ($val) {
                return self::sleepHelper($val);
            }, $obj);
        }
        return $obj;
    }

    /**
     * Helper method for processing and waking up serialized objects or arrays,
     * restoring them to their appropriate structure or state. The method supports
     * recognizing special serialized references related to a form and its controls.
     *
     * @param FormBase $objForm The form instance associated with the serialized object.
     * @param mixed $obj The serialized object, string, array, or other data to process.
     *
     * @return mixed The restored object, array, or the original value if no transformation was applied.
     */
    public static function wakeupHelper(FormBase $objForm, mixed $obj): mixed
    {
        if (is_array($obj)) {
            return array_map(function ($val) use ($objForm) {
                return self::wakeupHelper($objForm, $val);
            }, $obj);
        } elseif (is_string($obj)) {
            if (str_starts_with($obj, '**QF;')) {
                return $objForm;
            } elseif (str_starts_with($obj, '**QC;')) {
                return $objForm->getControl(substr($obj, 5));
            }
        }
        return $obj;
    }

    /**
     * Adds a control as a child of this control.
     *
     * @param ControlBase $objControl the control to add
     */
    public function addChildControl(ControlBase $objControl): void
    {
        $this->blnModified = true;
        $this->objChildControlArray[$objControl->ControlId] = $objControl;
        $objControl->objParentControl = $this;
    }

    /**
     * Returns all child controls as an array
     *
     * @param boolean $blnUseNumericIndexes
     * @return ControlBase[]
     */
    public function getChildControls(bool $blnUseNumericIndexes = true): array
    {
        if ($blnUseNumericIndexes) {
            $objToReturn = array();
            foreach ($this->objChildControlArray as $objChildControl) {
                $objToReturn[] = $objChildControl;
            }
            return $objToReturn;
        } else {
            return $this->objChildControlArray;
        }
    }

    /**
     * Returns the child control with the given ID
     * @param string $strControlId
     * @return ControlBase|null
     */
    public function getChildControl(string $strControlId): ?ControlBase
    {
        return $this->objChildControlArray[$strControlId] ?? null;
    }

    /**
     * Removes all child controls
     * @param boolean $blnRemoveFromForm
     * @throws Caller
     */
    public function removeChildControls(bool $blnRemoveFromForm): void
    {
        foreach ($this->objChildControlArray as $objChildControl) {
            $this->removeChildControl($objChildControl->ControlId, $blnRemoveFromForm);
        }
    }

    /**
     * Removes the child control with the given ID
     * @param string $strControlId
     * @param boolean $blnRemoveFromForm should the control be removed from the form, too?
     * @throws Caller
     */
    public function removeChildControl(string $strControlId, bool $blnRemoveFromForm): void
    {
        $this->blnModified = true;
        if ($blnRemoveFromForm) {
            $this->objForm->removeControl($strControlId); // will call back to here with $blnRemoveFromForm = false
        } else {
            if (isset($this->objChildControlArray[$strControlId])) {
                $objChildControl = $this->objChildControlArray[$strControlId];
                $objChildControl->objParentControl = null;
                unset($this->objChildControlArray[$strControlId]);
            }
        }
    }

    /**
     * Adds an action to the control
     *
     * @param Q\Event\EventBase $objEvent
     * @param Q\Action\ActionBase $objAction
     */
    public function addAction(EventBase $objEvent, QAction $objAction): void
    {
        // Modified
        $this->blnModified = true;

        // Store the Event object in the Action object
        if ($objAction->Event) {
            //this Action is in use -> clone it
            $objAction = clone($objAction);
        }
        $objAction->Event = $objEvent;

        $objEvent->setActions([$objAction]);
        $this->objEventArray[] = $objEvent;
    }

    /**
     * Adds an array of actions to the control for the given event, grouping the actions so that they are fired
     * one after the other by the specific event parameters.
     *
     * @param EventBase $objEvent
     * @param array $objActionArray
     */
    public function addActionArray(EventBase $objEvent, array $objActionArray): void
    {
        $objActions = [];
        foreach ($objActionArray as $objAction) {
            $objAction = clone($objAction);
            $objAction->Event = $objEvent;
            $objActions[] = $objAction;
        }

        $this->blnModified = true;

        $objEvent->setActions($objActions);
        $this->objEventArray[] = $objEvent;
    }

    /**
     * Removes all events for a given event name.
     *
     * @param string|null $strEventName
     */
    public function removeAllActions(?string $strEventName = null): void
    {
        $this->objEventArray = array_filter($this->objEventArray,
            function($objEvent) use ($strEventName) {
                return $objEvent->EventName != $strEventName;
            }
        );
    }

    /**
     * Shortcut for adding a debounced click action with a tiny delay. This is effective for most situations like submit
     * buttons and things that need to popup things after other actions and then wait for a response before proceeding.
     *
     * @param array|QAction $objAction
     * @throws Caller
     */
    public function onClick(array|Q\Action\ActionBase $objAction): void
    {
        if (is_array($objAction)) {
            $this->addActionArray(new Q\Event\Click(5, null, null, true), $objAction);
        } else {
            $this->addAction(new Q\Event\Click(5, null, null, true), $objAction);
        }
    }

    /**
     * Shortcut for adding a change event action.
     *
     * @param QAction $objAction
     */
    public function onChange(QAction $objAction): void
    {
        $this->addAction(new Q\Event\Change(), $objAction);
    }

    /**
     * Returns all actions that are connected with specific events
     *
     * @param string $strEventName the type of the event. Be sure and use a
     *                              FooEvent::EVENT_NAME here. (\QCubed\Event\Click::EVENT_NAME, for example)
     * @param string|null $strActionClass if given, only actions of this type will be
     *                              returned
     *
     * @return QAction[]
     */
    public function getAllActions(string $strEventName, ?string $strActionClass = null): array
    {
        $retActions = [];
        foreach ($this->objEventArray as $objEvent) {
            $objActions = $objEvent->getActions();

            foreach ($objActions as $objAction) {
                if ($strActionClass) {
                    if ($objAction instanceof $strActionClass) {
                        $retActions[] = $objAction;
                    }
                } else {
                    $retActions[] = $objAction;
                }
            }
        }
        return $retActions;
    }

    /**
     * Sets one custom attribute
     *
     * Custom attributes refer to HTML name-value pairs that can be rendered in a control and are not
     * covered by an explicit method. For example, you can render any number of additional name-value
     * pairs in a text box to specify additional javascript actions, additional formatting, etc.
     * <code>
     * <?php
     * $txtTextbox = new Textbox("txtTextbox");
     * $txtTextbox->setCustomAttribute("onfocus", "alert('You are about to edit this field');");
     * $txtTextbox->setCustomAttribute("nowrap", "nowrap");
     * $txtTextbox->setCustomAttribute("blah", "foo");
     * ?>
     * </code>
     * Will render:
     * <code>
     *   <input type="text" ...... onfocus="alert('You are about to edit this field');" nowrap="nowrap" blah="foo" />
     * </code>
     *
     * @param string $strName
     * @param string $strValue
     * @deprecated Use SetHtmlAttribute instead
     */
    public function setCustomAttribute(string $strName, string $strValue): void
    {
        $this->setHtmlAttribute($strName, $strValue);
    }

    /**
     * Returns the value of a custom attribute
     *
     * @param string $strName
     *
     * @return string
     * @deprected Use GetHtmlAttribute instead
     */
    public function getCustomAttribute(string $strName): string
    {
        return $this->getHtmlAttribute($strName);
    }

    /**
     * Removes the given custom attribute
     *
     * @param string $strName
     *
     * @deprecated Use RemoveHtmlAttribute instead
     */
    public function removeCustomAttribute(string $strName): void
    {
        $this->removeHtmlAttribute($strName);
    }

    /**
     * Adds a custom style property/value to the HTML style attribute
     *
     * Sets a custom CSS property. For example,
     * <code>
     * <?php
     * $txtTextbox = new Textbox("txtTextbox");
     * $txtTextbox->setCustomStyle("white-space", "nowrap");
     * $txtTextbox->setCustomStyle("margin", "10px"); * >
     * </code>
     * Will render:
     * <code>
     *        <input type="text" … style="white-space:nowrap;margin:10px" />
     * </code>
     *
     * @param string $strName
     * @param string $strValue
     * @deprecated Use SetCssStyle instead
     */
    public function setCustomStyle(string $strName, string $strValue): void
    {
        $this->setCssStyle($strName, $strValue);
    }

    /**
     * Returns the value of the given custom style
     *
     * @param string $strName
     *
     * @return string
     */
    public function getCustomStyle(string $strName): string
    {
        return $this->getCssStyle($strName);
    }

    /**
     * Deletes the given custom style
     *
     * @param string $strName
     *
     * @deprecated use RemoveCssStyle instead
     */
    public function removeCustomStyle(string $strName): void
    {
        $this->removeCssStyle($strName);
    }

    /**
     * Add JavaScript file to be included in the form.
     * The include mechanism will take care of duplicates and also change the given URL in the following ways:
     *  - If the file name begins with 'http', it will use it directly as a URL
     *  - If the file name begins with, '/', the url will be absolute
     *  - If the file name begins with anything else, an error is thrown
     *
     * @param string $strJsFileName url, path, or file name to include
     * @throws Exception\Caller
     */
    public function addJavascriptFile(string $strJsFileName): void
    {
        $strJsFileName = trim($strJsFileName);
        if (str_starts_with($strJsFileName, "http") || $strJsFileName[0] === "/") {
            if ($this->strJavaScripts) {
                $this->strJavaScripts .= ',' . $strJsFileName;
            } else {
                $this->strJavaScripts = $strJsFileName;
            }
        } else {
            throw new Exception\Caller('Relative urls are not supported anymore. ' . $strJsFileName);
        }
    }

    /**
     * Add a style sheet file to be included in the form.
     * The include mechanism will take care of duplicates and also change the given URL in the following ways:
     *  - If the file name begins with 'http', it will use it directly as a URL
     *  - If the file name begins with, '/', the url will be absolute
     *  - If the file name begins with anything else, an error is thrown
     *
     * @param string $strCssFileName url, path, or file name to include
     * @throws Exception\Caller
     */
    public function addCssFile(string $strCssFileName): void
    {
        $strCssFileName = trim($strCssFileName);
        if (str_starts_with($strCssFileName, "http") || $strCssFileName[0] === "/") {
            if ($this->strStyleSheets) {
                $this->strStyleSheets .= ',' . $strCssFileName;
            } else {
                $this->strStyleSheets = $strCssFileName;
            }
        } else {
            throw new Exception\Caller('Relative urls are not supported anymore. ' . $strCssFileName);
        }
    }

    /**
     * Returns all attributes in the correct HTML format
     *
     * This is utilized by Render methods to display various name-value HTML attributes for the
     * control.
     *
     * This implementation contains the very basic set of HTML attributes... it is expected
     * that most subclasses will extend this method's functionality to add Control-specific HTML
     * attributes (e.g., textbox will likely add the maxlength HTML attribute, etc.)
     *
     * @return string
     * @deprecated Use renderHtmlAttributes instead
     */
    public function getAttributes(): string
    {
        return $this->renderHtmlAttributes() . ' ';
    }

    /**
     * Returns the custom attributes HTML formatted
     *
     * All attributes will be returned as concatenated the string of the form
     * key1="value1" key2="value2"
     * Note: if the value is === false, then the key will be rendered as is, without any value
     *
     * @return string
     * @deprecated Unused
     */
    public function getCustomAttributes(): string
    {
        return $this->renderHtmlAttributes();
    }

    /**
     * Returns the HTML for the attributes for the base control of the Control.
     * Allows the given arrays to override the attributes and styles before
     * rendering. This inserts the control ID into the rendering of the tag.
     * @param array|null $attributeOverrides
     * @param array|null $styleOverrides
     * @return string
     */
    public function renderHtmlAttributes(?array $attributeOverrides = null, ?array $styleOverrides = null): string
    {
        $attributes['id'] = $this->strControlId;
        if ($attributeOverrides) {
            $attributes = array_merge($attributes, $attributeOverrides);
        }
        return parent::renderHtmlAttributes($attributes, $styleOverrides);
    }

    /**
     * Returns all action attributes for this Control
     *
     * @return string
     */
    public function renderActionScripts(): string
    {
        $strToReturn = '';
        foreach ($this->objEventArray as $objEvent) {
            $strToReturn .= $this->getJavaScriptForEvent($objEvent);
        }

        return $strToReturn;
    }

    /**
     * Get the JavaScript for a given event
     * @param EventBase $objEvent
     *
     * @return string
     */

    public function getJavaScriptForEvent(EventBase $objEvent): string
    {
        return $objEvent->renderActions($this);
    }

    /**
     * Returns all style-attributes
     *
     * Similar to GetAttributes, but specifically for CSS name/value pairs that will render
     * within a control's HTML "style" attribute
     *
     * <code>
     * <?php
     * $txtTextbox = new Textbox("txtTextbox");
     * $txtTextbox->setCustomStyle("white-space", "nowrap");
     * $txtTextbox->setCustomStyle("margin", "10px");
     * $txtTextBox->Height = 20;
     * $txtTextBox->getStyleAttributes();
     * will return:
     * white-space:nowrap;margin:10px;height:20px;
     *
     * @return string
     * @deprected Use
     */
    public function getStyleAttributes(): string
    {
        return $this->renderCssStyles();
    }

    /**
     * Returns the styler for the wrapper tag.
     * @return null|TagStyler
     */
    public function getWrapperStyler(): ?TagStyler
    {
        if (!$this->objWrapperStyler) {
            $this->objWrapperStyler = new TagStyler();
        }
        return $this->objWrapperStyler;
    }

    /**
     * Adds the given class to the wrapper tag.
     * @param string $strClass
     */
    public function addWrapperCssClass(string $strClass): void
    {
        if ($this->getWrapperStyler()->addCssClass($strClass)) {
            $this->markAsWrapperModified();
        }
        /**
         * TODO: This can likely be done just in javascript without a complete refresh of the control.
         *
         * if ($this->blnRendered && $this->blnOnScreen) {
         *   Change using javascript
         * }
         */
    }

    /**
     * Removes a specified CSS class from the wrapper element's class list.
     * If the class is successfully removed, the wrapper is marked as modified.
     *
     * @param string $strClass The name of the CSS class to remove from the wrapper.
     *
     * @return void
     */
    public function removeWrapperCssClass(string $strClass): void
    {
        if ($this->getWrapperStyler()->removeCssClass($strClass)) {
            $this->markAsWrapperModified();
        }

        // TODO: do this in javascript
        // QApplication::executeControlCommand($this->getWrapperId(), 'removeClass', $this->strValidationState);
    }

    /**
     * Returns all wrapper-style-attributes
     * Similar to GetStyleAttributes, but specifically for CSS name/value pairs that will render
     * within a "wrapper's" HTML "style" attribute
     *
     * @param bool|null $blnIsBlockElement Indicates whether the wrapper element is treated as a block element.
     *                                     Defaults to false if not explicitly specified.
     *
     * @return string The rendered CSS style attributes for the wrapper.
     */
    protected function getWrapperStyleAttributes(?bool $blnIsBlockElement = false): string
    {
        return $this->getWrapperStyler()->renderCssStyles();
    }


    /**
     * Overrides the default CSS renderer in order to deal with a special situation:
     * Since there is the possibility of a wrapper, we have to delegate certain CSS properties to the wrapper so
     * that the whole control gets those properties. Those are mostly positioning properties. In this override,
     * we detect when we do NOT have a wrapper and therefore have to copy the positioning properties from the
     * wrapper styler down to the control itself.
     *
     * @param array|null $styleOverrides
     * @return string
     */
    public function renderCssStyles(?array $styleOverrides = null): string
    {
        $styles = $this->styles;
        if ($styleOverrides) {
            $styles = array_merge($this->styles, $styleOverrides);
        }

        if (!$this->blnUseWrapper) {
            // Add wrapper styles if no wrapper. Control must stand on its own.
            // This next line sucks just the given attributes out of the wrapper styler
            $aWStyles = array_intersect_key($this->getWrapperStyler()->styles,
                ['position' => 1, 'top' => 1, 'left' => 1]);
            $styles = array_merge($styles, $aWStyles);
            if (!$this->blnDisplay) {
                $styles['display'] = 'none';
            }
        }
        return Html::renderStyles($styles);
    }

    /**
     * Returns an array of wrapper attributes used for drawing, including CSS styles. Make sure the control is hidden when the screen is off.
     * @param array|null $attributeOverrides
     * @return array
     */
    protected function getWrapperAttributes(?array $attributeOverrides = null): array
    {
        $styleOverrides = null;
        if (!$this->blnDisplay) {
            $styleOverrides = ['display' => 'none'];
        }

        return $this->getWrapperStyler()->getHtmlAttributes($attributeOverrides, $styleOverrides);
    }

    /**
     * Renders the given output with the current wrapper.
     *
     * @param string $strOutput
     * @param bool|null $blnForceAsBlockElement
     *
     * @return string
     */
    protected function renderWrappedOutput(string $strOutput, ?bool $blnForceAsBlockElement = false): string
    {
        $strTag = ($this->blnIsBlockElement || $blnForceAsBlockElement) ? 'div' : 'span';
        $overrides = ['id' => $this->getWrapperId()];
        $attributes = $this->getWrapperAttributes($overrides);

        return Html::renderTag($strTag, $attributes, $strOutput);
    }

    /**
     * RenderHelper should be called from all "Render" functions FIRST in order to check for and
     * perform attribute overrides (if any).
     * All render methods should take in an optional first boolean parameter blnDisplayOutput
     * (default to true), and then any number of attribute overrides.
     * Any "Render" method (e.g., Render, RenderWithName, RenderWithError) should call the
     * RenderHelper FIRST in order to:
     * <ul>
     * <li>Check for and perform attribute overrides</li>
     * <li>Check to see if this control is "Visible".  If it is Visible=false, then
     *        the renderhelper will cause the method to immediately return</li>
     * </ul>
     * Proper usage within the first line of any Render() method is:
     *        <code>$this->renderHelper(func_get_args(), __FUNCTION__);</code>
     * See {@link ControlBase::renderWithName()} as example.
     *
     * @param mixed $mixParameterArray the parameters given to the render call
     * @param string $strRenderMethod the method which has been used to render the
     *                           control. This is important for ajax rendering
     *
     * @throws Exception\Caller
     * @throws \Exception
     * @see QControlBase::renderOutput()
     */
    protected function renderHelper(mixed $mixParameterArray, string $strRenderMethod): void
    {
        // Make sure the form is already "RenderBegun"
        if ((!$this->objForm) || ($this->objForm->FormStatus != FormBase::FORM_STATUS_RENDER_BEGUN)) {
            if (!$this->objForm) {
                $objExc = new Exception\Caller('Control\'s form does not exist. It could be that you are attempting to render after RenderEnd() has been called on the form.');
            } else {
                if ($this->objForm->FormStatus == FormBase::FORM_STATUS_RENDER_ENDED) {
                    $objExc = new Exception\Caller('Control cannot be rendered after RenderEnd() has been called on the form.');
                } else {
                    $objExc = new Exception\Caller('Control cannot be rendered until RenderBegin() has been called on the form.');
                }
            }

            // Increment because we are two-deep below the call stack
            // (e.g., the Render function call, and then this RenderHelper call)
            $objExc->incrementOffset();
            throw $objExc;
        }

        // Make sure this hasn't yet been rendered
        if (($this->blnRendered) || ($this->blnRendering)) {
            $objExc = new Exception\Caller('This control has already been rendered: ' . $this->strControlId);

            // Increment because we are two-deep below the call stack
            // (e.g., the Render function call, and then this RenderHelper call)
            $objExc->incrementOffset();
            throw $objExc;
        }

        // Let's remember *which* render method was used to render this control
        $this->strRenderMethod = $strRenderMethod;

        // Remove non-overrides from the parameter array
        while (!empty($mixParameterArray) && gettype(reset($mixParameterArray)) != "NULL" && gettype(reset($mixParameterArray)) != "array") {
            array_shift($mixParameterArray);
        }

        // Apply any overrides (if applicable)
        if (!empty($mixParameterArray)) {
            // Override
            try {
                $this->overrideAttributes($mixParameterArray);
            } catch (Exception\Caller $objExc) {
                // Increment Twice because we are two-deep below the call stack
                // (e.g., the Render function call, and then this RenderHelper call)
                $objExc->incrementOffset();
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        // Because we may be re-rendering a parent control, we need to make sure all "children" controls are marked as NOT being on the page.
        foreach ($this->getChildControls() as $objChildControl) {
            $objChildControl->blnOnPage = false;
        }

        // Finally, let's specify that we have begun rendering this control
        $this->blnRendering = true;
    }

    /**
     * Placeholder for non-wrapped HTML content.
     * Currently returning nothing. Please change when implementing the function.
     */
    protected function getNonWrappedHtml()
    {

    }

    /**
     * Sets focus to this control
     * TODO: Turn this into a specific command to avoid the javascript eval that happens on the other end.
     */
    public function focus(): void
    {
        Application::executeControlCommand($this->strControlId, 'focus');
    }

    /**
     * Same as "Focus": Sets focus to this control
     */
    public function setFocus(): void
    {
        $this->focus();
    }

    /**
     * Let this control blink
     *
     * @param string $strFromColor start color
     * @param string $strToColor blink color
     * TODO: Turn this into a specific command to avoid the javascript eval that happens on the other end.
     * @throws Caller
     */
    public function blink(string $strFromColor = '#ffff66', string $strToColor = '#ffffff'): void
    {
        Application::executeJavaScript(sprintf('qc.getW("%s").blink("%s", "%s");', $this->strControlId, $strFromColor,
            $strToColor));
    }

    /**
     * Returns and fires the JavaScript that is associated with this control. The HTML for the control will have already
     * been rendered, so refer to the HTML object with "\$j(#{$this->ControlId})". You should do the following:
     *  - Return any script that attaches a JavaScript widget to the HTML control.
     *  - Use functions like ExecuteControlCommand to fire commands to execute AFTER all controls have been attached.
     *
     * @return string
     */
    public function getEndScript(): string
    {
        $strToReturn = '';

        try {
            $this->makeJqWidget();

            $this->objResizable?->makeJqWidget();

            $this->objDraggable?->makeJqWidget();

            $this->objDroppable?->makeJqWidget();

            $strToReturn .= $this->renderActionScripts();

            $this->strAttributeScripts = [];

        } catch (Throwable $e) {
            error_log("Error in getEndScript: " . $e->getMessage());
        }

        return $strToReturn;
    }

    /**
     * Processes and executes attribute scripts for the control.
     * The scripts are executed using the control ID and the given script arguments.
     * After execution, the attribute scripts are cleared.
     *
     * @return void
     */
    public function renderAttributeScripts(): void
    {
        if ($this->strAttributeScripts) {
            foreach ($this->strAttributeScripts as $scriptArgs) {
                array_unshift($scriptArgs, $this->getJQControlId());
                call_user_func_array('\QCubed\ApplicationBase::executeControlCommand', $scriptArgs);
            }
        }

        $this->strAttributeScripts = null;
    }

    /**
     * Executes a JavaScript associated with the control. These scripts are specifically for the purpose of
     * changing some attributes of the control that would also be taken care of during a refresh of the entire
     * control. The script will only be executed in ajax if the entire control is not redrawn.
     *
     * Note that these will execute after most of the other commands execute, so do not count on the order
     * in which they will execute relative to other commands.
     *
     * @param string $strMethod The name of the JavaScript function to call on this control.
     * @param string $args One or more arguments to send to the method that will cause the control to change
     *
     * @return void
     */
    public function addAttributeScript(string $strMethod, string $args /*, ... */): void
    {
        $args = func_get_args();
        $this->strAttributeScripts[] = $args;
    }

    /**
     * For any HTML code that needs to be rendered at the END of the FormBase when this control is
     * INITIALLY rendered.
     *
     */
    public function getEndHtml(): string
    {
        return '';
    }

    /**
     * Refreshes the control
     *
     * If not yet rendered during this ajax event, it will set the Modified variable to true. This will
     * have the effect of forcing a refresh of this control when it is supposed to be rendered.
     * Otherwise, this will do nothing
     */
    public function refresh(): void
    {
        if ((!$this->blnRendered) &&
            (!$this->blnRendering)
        ) {
            $this->markAsModified();
        }
    }

    /**
     * RenderOutput should be the last call in your custom RenderMethod. It is responsible for the following:
     * - Creating the wrapper if you are using a wrapper, or
     * - Possibly creating a dummy control if not using a wrapper and the control is hidden.
     * - Generating the control's output in one of 3 ways:
     * - Generate straight HTML if drawing the control as part of a complete page refresh
     * - Generate straight HTML if in an ajax call, but a parent is getting redrawn, which requires this
     *   whole control to get drawn
     * - If in an ajax call, and we are the top level control getting drawn, then generate special code that
     *   out javascript will read and put into the control's spot on the page. Requires coordination with
     *   the code in qcubed.js.
     *
     * @param string $strOutput The generated HTML output to be rendered.
     * @param bool $blnDisplayOutput Whether to directly output the rendered HTML or return it as a string.
     * @param bool $blnForceAsBlockElement Whether to force the output to be rendered as a block element.
     *
     * @return string The rendered HTML output if $blnDisplayOutput is false, otherwise an empty string.
     */
    protected function renderOutput(string $strOutput, bool|array $blnDisplayOutput, bool $blnForceAsBlockElement = false): string
    {
        if ($blnForceAsBlockElement) {
            $this->blnIsBlockElement = true;    // must be remembered for ajax drawing
        }

        if ($this->blnUseWrapper) {
            if (!$this->blnVisible) {
                $strOutput = '';
            }
        } else {
            if (!$this->blnVisible) {
                /* No wrapper is used, and the control is not visible. We must enter a span with the control ID and
                 *	display:none in order to be able to change blnVisible to true in an Ajax call later and redraw the control.
                 */
                $strOutput = sprintf('<span id="%s" style="display:none;"></span>', $this->strControlId);
            }
        }

        if (Application::isAjax()) {
            if ($this->objParentControl) {
                if ($this->objParentControl->Rendered || $this->objParentControl->Rendering) {
                    // If we have a ParentControl and the ParentControl has NOT been rendered, then output
                    // as standard HTML
                    if ($this->blnUseWrapper) {
                        $strOutput = $this->renderWrappedOutput($strOutput,
                                $blnForceAsBlockElement) . $this->getNonWrappedHtml();
                    } else {
                        $strOutput = $strOutput . $this->getNonWrappedHtml();
                    }
                }
            } else {
                // if this is an injected top-level control, then we need to render the whole thing
                if (!$this->blnOnPage) {
                    if ($this->blnUseWrapper) {
                        $strOutput = $this->renderWrappedOutput($strOutput,
                                $blnForceAsBlockElement) . $this->getNonWrappedHtml();
                    } else {
                        $strOutput = $strOutput . $this->getNonWrappedHtml();
                    }
                }
            }
        } else {
            if ($this->blnUseWrapper) {
                $strOutput = $this->renderWrappedOutput($strOutput) . $this->getNonWrappedHtml();
            } else {
                $strOutput = $strOutput . $this->getNonWrappedHtml();
            }

            $strOutput = $this->renderComment(self::COMMENT_START) . _indent($strOutput) . $this->renderComment(self::COMMENT_END);
        }

        // Update watcher
        $this->objWatcher?->makeCurrent();

        $this->blnRendering = false;
        $this->blnRendered = true;
        $this->blnOnPage = true;

        // Output or Return
        if ($blnDisplayOutput) {
            print($strOutput);
            return '';
        } else {
            return $strOutput;
        }
    }

    /**
     * This method will render the control, itself, and will return the rendered HTML as a string
     *
     * As an abstract method, any class extending ControlBase must implement it.  This ensures that
     * each control has its own specific HTML.
     *
     * When outputting HTML, you should call GetHtmlAttributes to get the attributes for the main control.
     *
     * If you are outputting a complex control and need to include IDs in sub controls, your IDs should be of the form:
     *    $parentControl->ControlId. '_' . $strSubcontrolId.
     * The underscore indicates that actions and posting data should first be directed to parent control and parent
     * management will handle the rest.
     *
     * @return string
     */
    abstract protected function getControlHtml(): string;

    /**
     * This render method is the most basic render-method available.
     * It will perform an attribute overriding (if any) and will either display the rendered
     * HTML (if blnDisplayOutput is true, which it is by default), or it will return the
     * rendered HTML as a string.
     *
     * @param boolean $blnDisplayOutput render the control or return as string
     *
     * @throws Exception\Caller
     * @return string
     * @throws Caller
     */
    public function render(bool|array $blnDisplayOutput = true /* ... */): string
    {
        $blnMinimized = Application::instance()->setMinimize($this->blnMinimize);

        $this->renderHelper(func_get_args(), __FUNCTION__);

        if ($this->blnVisible) {
            $strOutput = sprintf('%s%s%s',
                $this->strHtmlBefore,
                $this->getControlHtml(),
                $this->strHtmlAfter
            );
        } else {
            // Avoid going through the time to render the control if we are not going to display it.
            $strOutput = "";
        }

        // Call RenderOutput, returning its contents
        $strOut = $this->renderOutput($strOutput, $blnDisplayOutput);

        Application::instance()->setMinimize($blnMinimized);

        return $strOut;
    }

    /**
     * RenderAjax will be called during an Ajax rendering of the controls. Every control gets called. Each control
     * is responsible for rendering itself. Some objects automatically render their child objects, and some don't,
     * so we detect whether the parent is being rendered and assume the parent is taking care of rendering for
     * us if so.
     *
     * Override if you want more control over ajax drawing, for example, you identify parts of your control that have changed
     * and then want to draw only those parts. This is called on every ajax pull request on every control.
     * It is up to you to test the blnRendered flag of the control to know whether the control was already rendered
     * by a parent control before drawing here.
     *
     * @return array[] array of control arrays to be interpreted by the response function in qcubed.js
     */
    public function renderAjax(): array
    {
        // Only render if this control has been modified at all
        $controls = [];
        if ($this->isModified()) {
            // Render if (1) object has no parent or (2) parent was not rendered nor currently being rendered
            if ((!$this->objParentControl) || ((!$this->objParentControl->Rendered) && (!$this->objParentControl->Rendering))) {
                $strRenderMethod = $this->strRenderMethod;
                if (!$strRenderMethod && $this->AutoRender) {
                    // This is an auto-injected control (a dialog, for instance) that is not on the page, so go ahead and render it
                    $strRenderMethod = $this->strPreferredRenderMethod;
                }
                if ($strRenderMethod) {
                    $strOutput = $this->$strRenderMethod(false);
                    $controls[] = [Q\JsResponse::ID => $this->strControlId, Q\JsResponse::HTML => $strOutput];
                }
            }
        }

        if ($this->blnWrapperModified && ($this->blnVisible) && ($this->blnUseWrapper)) {
            // Top level ajax response will usually just draw the innerText of the wrapper
            // If something changed in the wrapper attributes, we need to tell the jQuery response to handle that too.
            // In particular, if the wrapper was hidden and is now displayed, we need to make sure that the control
            // becomes visible before other scripts execute, or those other scripts will not see the control.
            $wrapperAttributes = $this->getWrapperAttributes();
            if (!isset($wrapperAttributes['style'])) {
                $wrapperAttributes['style'] = '';    // must specifically turn off styles if none were drawn, in case the previous state had a style and it had changed
            }
            $controls[] = [Q\JsResponse::ID => $this->getWrapperId(), Q\JsResponse::ATTRIBUTES => $wrapperAttributes];
        }
        return $controls;
    }

    /**
     * Returns true if the control should be redrawn.
     * @return boolean
     */
    public function isModified(): bool
    {
        return ($this->blnModified ||
            ($this->objWatcher && !$this->objWatcher->isCurrent()));
    }

    /**
     * Renders all Children
     * @param boolean $blnDisplayOutput display output (echo out) or just return as string
     * @return string|null
     */
    protected function renderChildren(bool $blnDisplayOutput = true): ?string
    {
        $strToReturn = "";

        foreach ($this->getChildControls() as $objControl) {
            if (!$objControl->Rendered) {
                $renderMethod = $objControl->strPreferredRenderMethod;
                if ($renderMethod) {
                    $strToReturn .= $objControl->$renderMethod($blnDisplayOutput);
                }
            }
        }

        if ($blnDisplayOutput) {
            print($strToReturn);
            return null;
        } else {
            return $strToReturn;
        }
    }

    /**
     * This render method outputs the control HTML with error and warning messages, if applicable.
     * It will perform an attribute overriding (if any) and will either display the rendered
     * HTML (if $blnDisplayOutput is true, which it is by default), or it will return the
     * rendered HTML as a string. Error and warning messages, if present, are appended to the control's output.
     *
     * @param boolean $blnDisplayOutput Render the control directly or return as a string
     *
     * @return string The rendered HTML with error or warning messages if present
     * @throws Exception\Caller
     */
    public function renderWithError(bool $blnDisplayOutput = true): string
    {
        // Call RenderHelper
        $this->renderHelper(func_get_args(), __FUNCTION__);

        /**
         * If we are not using a wrapper, then we are going to tag related elements so that qcubed.js
         * can remove them when we redraw. Otherwise, they will be repeatedly added instead of replaced.
         */
        $strDataRel = '';
        if (!$this->blnUseWrapper) {
            $strDataRel = sprintf('data-qrel="#%s" ', $this->strControlId);
        }

        $strOutput = $this->getControlHtml();

        if ($this->strValidationError) {
            $strOutput .= sprintf('<br %s/><span %sclass="error">%s</span>', $strDataRel, $strDataRel,
                Html::renderString($this->strValidationError));
        } else {
            if ($this->strWarning) {
                $strOutput .= sprintf('<br %s/><span %sclass="warning">%s</span>', $strDataRel, $strDataRel,
                    Html::renderString($this->strWarning));
            }
        }

        // Call RenderOutput, Returning its Contents
        return $this->renderOutput($strOutput, $blnDisplayOutput);
    }

    /**
     * Renders the control with an attached name
     *
     * This will call {@link QControlBase::getControlHtml()} for the bulk of the work, but will add layout HTML as well.  It will include
     * the rendering of the Controls' name label, any errors or warnings, instructions, and HTML before/after (if specified).
     * As this is the parent class of all controls, this method defines how ALL controls will render when rendered with a name.
     * If you need certain controls to display differently, override this function in that control's class.
     *
     * @param boolean $blnDisplayOutput true to send the display buffer, false to just return, then HTML
     * @throws Exception\Caller
     * @return string Rendered control HTML
     */
    public function renderWithName(bool $blnDisplayOutput = true): string
    {
        ////////////////////
        // Call RenderHelper
        $this->renderHelper(func_get_args(), __FUNCTION__);
        ////////////////////

        $aWrapperAttributes = array();
        if (!$this->blnUseWrapper) {
            //there is no wrapper --> add the special attribute data-qrel to the name control
            $aWrapperAttributes['data-qrel'] = $this->strControlId;
            if (!$this->blnDisplay) {
                $aWrapperAttributes['style'] = 'display: none';
            }
        }

        // Custom Render Functionality Here

        // Because this example RenderWithName will render a block-based element (e.g., a DIV), let's ensure
        // that IsBlockElement is set to true
        $this->blnIsBlockElement = true;

        // Render the Left side
        $strLabelClass = "form-name";
        if ($this->blnRequired) {
            $strLabelClass .= ' required';
        }
        if (!$this->Enabled) {
            $strLabelClass .= ' disabled';
        }

        if ($this->strInstructions) {
            $strInstructions = '<br/>' .
                Html::renderTag('span', ['class' => "instructions"], Html::renderString($this->strInstructions));
        } else {
            $strInstructions = '';
        }
        $strLabel = Html::renderTag('label', null, Html::renderString($this->strName));
        $strToReturn = Html::renderTag('div', ['class' => $strLabelClass], $strLabel . $strInstructions);

        // Render the Right side
        $strMessage = '';
        if ($this->strValidationError) {
            $strMessage = sprintf('<span class="error">%s</span>', Html::renderString($this->strValidationError));
        } else {
            if ($this->strWarning) {
                $strMessage = sprintf('<span class="warning">%s</span>', Html::renderString($this->strWarning));
            }
        }

        $strToReturn .= sprintf('<div class="form-field">%s%s%s%s</div>',
            $this->strHtmlBefore,
            $this->getControlHtml(),
            $this->strHtmlAfter,
            $strMessage);

        // Render control dressing, which is essentially a wrapper. Not sure why we are not just rendering a wrapper here!
        $strToReturn = Html::renderTag('div', $aWrapperAttributes, $strToReturn);

        ////////////////////////////////////////////
        // Call RenderOutput, Returning its Contents

        return $this->renderOutput($strToReturn, $blnDisplayOutput);

        ////////////////////////////////////////////
    }

    /**
     * Format a comment block if we are not in MINIMIZE mode.
     *
     * @param string $strType Either ControlBase::COMMENT_START or ControlBase::COMMENT_END
     * @return string
     */
    public function renderComment(string $strType): string
    {
        return Html::comment($strType . ' ' . get_class($this) . ' ' . $this->strName . ' id:' . $this->strControlId);
    }


    /**
     * Renders the specified method of a given class dynamically by creating an instance
     * of the provided class and invoking the method with the given arguments.
     *
     * @param string $classname The name of the class to instantiate.
     * @param string $methodname The name of the method to invoke on the instantiated class.
     * @param array $args An array of arguments to pass to the method being invoked.
     *
     * @return mixed Returns the result of the invoked method.
     */
    public function renderExtensionRenderer(string $classname, string $methodname, array $args = array()): mixed
    {
        $RenderExtensionInstance = new $classname;
        return $RenderExtensionInstance->{$methodname}($args);
    }

    /**
     * Validate self + child controls. Controls must mark themselves modified, or somehow redraw themselves
     * if by failing the validation; they change their visual look in some way (like by adding warning text, turning
     * red, etc.)
     *
     * @return bool
     */
    public function validateControlAndChildren(): bool
    {
        // Initially Assume Validation is True
        $blnToReturn = true;

        // Check the Control Itself
        if (!$this->validate()) {
            $blnToReturn = false;
        }

        // Recursive call on Child Controls
        foreach ($this->getChildControls() as $objChildControl) {
            // Only Enabled and Visible and Rendered controls should be validated
            if (($objChildControl->Visible) && ($objChildControl->Enabled) && ($objChildControl->RenderMethod) && ($objChildControl->OnPage)) {
                if (!$objChildControl->validateControlAndChildren()) {
                    $blnToReturn = false;
                }
            }
        }

        return $blnToReturn;
    }



    // The following three methods are only intended to be called by code within the Form class.
    // It must be declared as public so that a form object can have access to them, but it really should never be
    // called by user code.
    /**
     * Reset the control flags by default
     */
    public function resetFlags(): void
    {
        $this->blnRendered = false;
        $this->blnModified = false;
        $this->blnWrapperModified = false;
    }

    /**
     * Reset the On-Page status by default (false)
     */
    public function resetOnPageStatus(): void
    {
        $this->blnOnPage = false;
    }

    /**
     * Marks this control as modified
     */
    public function markAsModified(): void
    {
        $this->blnModified = true;
        /*
         TODO: Implement and test the code below to reduce the amount of redrawing. In particular, the current
            implementation will cause invisible and display:none controls to be redrawn whenever something changes,
            even though its not needed.

        if ($this->blnVisible &&
        $this->blnDisplay) {
            $this->blnModified = true;
        } */
    }

    /**
     * Marks the wrapper of this control as modified
     */
    public function markAsWrapperModified(): void
    {
        $this->blnWrapperModified = true;
        $this->blnModified = true;
    }

    /**
     * Marks this control as Rendered
     */
    public function markAsRendered(): void
    {
        $this->blnRendered = true;
    }

    /**
     * Sets the Form of this Control
     * @param FormBase $objForm
     */
    public function setForm(FormBase $objForm): void
    {
        $this->objForm = $objForm;
    }

    /**
     * Sets the parent control for this control
     * @param ControlBase $objControl The control which has to be set as this control's parent
 *
     * @return void
     * @throws Caller
     */
    public function setParentControl(ControlBase $objControl): void
    {
        // Mark this object as modified
        $this->markAsModified();

        // Mark the old parent (if applicable) as modified
        $this->objParentControl?->removeChildControl($this->ControlId, false);

        $objControl->addChildControl($this);
    }

    /**
     * Resets the validation state to default
     */
    public function validationReset(): void
    {
        if (($this->strValidationError) || ($this->strWarning)) {
            $this->blnModified = true;
        }
        $this->strValidationError = null;
        $this->strWarning = null;
    }

    /**
     * Prepares the object for export by clearing references to other objects
     * and storing their identifiers as strings in separate properties.
     *
     * @return void
     */
    public function prepareForExport(): void
    {
        if ($this->objForm) {
            $this->strFormId = $this->objForm->FormId; // We store the ID as a string in a separate property (strFormId)
            $this->objForm = null; //Clear reference
        }
        if ($this->objParentControl) {
            $this->strParentControlId = $this->objParentControl->ControlId; // We save the ID as a string
            $this->objParentControl = null; // Clear reference
        }
    }

    /**
     * Restores the form and parent control after export by associating them with their respective IDs.
     *
     * @return void
     */
    public function restoreAfterExport(): void
    {
        if ($this->strFormId) {
            $this->objForm = Q\ApplicationBase::$forms[$this->strFormId]; // Retrieves form by ID
        }
        if ($this->strParentControlId && $this->objForm) {
            $this->objParentControl = $this->objForm->getControl($this->strParentControlId); // Restore control
        }
    }

    /**
     * Exports the current object state as a parsable string representation.
     * The method ensures that circular references and non-exportable properties are handled properly.
     * This is useful for debugging or saving object states.
     *
     * @param boolean $blnReturn If true, the exported value is returned as a string.
     *                            If false, it is output directly.
     *
     * @return array|string|null Returns the exported object state as a string if $blnReturn is true,
     *                     or null if outputting directly.
     */
    public function varExport(bool $blnReturn = true): array|string|null
    {
        // Remove cyclic and preserve IDs
        $this->prepareForExport();

        $vars = get_object_vars($this);
        foreach ($vars as $key => $val) {
            $this->$key = self::sleepHelper($val);
        }

        // Export
        $result = var_export($this, $blnReturn);

        // Restore state after export
        $this->restoreAfterExport();

        return $result;
    }

    /**
     * Support for jquery widgets.
     * Many jquery widgets, including those in JQuery UI, are instantiated by a JavaScript setup function that causes
     * the widget to attach itself to the control. Since any kind of control can be a jquery widget, the following
     * support code is here.
     */

    /**
     * Return the name of the JavaScript setup function that should get called on this control's HTML object. Returning
     * a value triggers the other jquery widget support.
     *
     * @return string
     */
    protected function getJqSetupFunction(): string
    {
        return '';
    }

    /**
     * Attaches the JQueryUI widget to the HTML object if a widget is specified.
     */
    protected function makeJqWidget(): void
    {
        $strFunc = $this->getJqSetupFunction();
        if ($strFunc == '') {
            return;
        }

        $jqOptions = $this->makeJqOptions();
        $strId = $this->getJqControlId();

        if ($strId !== $this->ControlId && Application::isAjax()) {
            // If events are not attached to the actual object being drawn, then the old events will not get
            // deleted during redrawing. We delete the old events here. This must happen before any other event processing code.
            Application::executeControlCommand($strId, 'off', Q\ApplicationBase::PRIORITY_HIGH);
        }

        // Attach the JavaScript widget to the HTML object
        if (empty($jqOptions)) {
            Application::executeControlCommand($strId, $strFunc, Q\ApplicationBase::PRIORITY_HIGH);
        } else {
            Application::executeControlCommand($strId, $strFunc, $jqOptions, Q\ApplicationBase::PRIORITY_HIGH);
        }
    }

    /**
     * Returns a key/value array that will be a JavaScript object of parameters passed to the jquery setup function.
     *
     * @return array
     */
    protected function makeJqOptions(): array {
        return [];
    }

    /**
     * Used by jQuery UI wrapper controls to find the element on which to apply the jQuery function
     *
     * NOTE: Some controls that use jQuery will get wrapped with extra divs by the jQuery library.
     * If such a control then gets replaced by Ajax during a redrawing, the jQuery effects will be deleted. To solve this,
     * the corresponding QCubed control should set UseWrapper to true, attach the jQuery effect to
     * the wrapper, and override this function to return the ID of the wrapper. See DialogBase.php for
     * an example.
     *
     * @return string DOM element ID to apply jQuery UI function to
     */

     public function getJQControlId(): string
    {
        return $this->ControlId;
    }

    /**
     * Returns the top level control ID, which is the wrapper ID of a wrapper is being used.
     *
     * @return string
     */
    public function getWrapperId(): string
    {
        if ($this->blnUseWrapper) {
            return $this->ControlId . '_ctl';
        } else {
            return $this->ControlId;
        }
    }

    /**
     * Watch a particular node in the database. Call this to trigger a redrawing of the control
     * whenever the database table that this node points to is changed.
     *
     * @param NodeBase $objNode
     * @throws Caller
     */
    public function watch(NodeBase $objNode): void
    {
        if (!$this->objWatcher) {
            if (defined(WatcherBase::QCUBED_WATCHER_CLASS)) {
                $class = WatcherBase::QCUBED_WATCHER_CLASS;
                $this->objWatcher = new $class();
            } else {
                $this->objWatcher = new Watcher();
            }

//            if (defined('QCUBED_WATCHER_CLASS')) {
//                $class = QCUBED_WATCHER_CLASS;
//                $this->objWatcher = new $class(); // only create a watcher object when needed, since it is stored in the form state
//            } else {
//                $this->objWatcher = new Watcher(); // only create a watcher object when needed, since it is stored in the form state
//            }
        }
        $this->objWatcher->watch($objNode);
    }

    /**
     * Make this control current as of the latest changes so that it will not refresh on the next draw.
     */
    public function makeCurrent(): void
    {
        $this->objWatcher?->makeCurrent();
    }

    /**
     * Returns true if the given control is anywhere in the parent hierarchy of this control.
     *
     * @param ControlBase $objControl
     * @return bool
     */
    public function isDescendantOf(ControlBase $objControl): bool
    {
        $objParent = $this->objParentControl;
        while ($objParent) {
            if ($objParent === $objControl) {
                return true;
            }
            $objParent = $objParent->objParentControl;
        }
        return false;
    }

    /**
     * Searches the control, and it's a hierarchy to see if a method by a given name exists.
     * This method searches only in the current control and its parents and so on.
     * It will not search for the method in any siblings at any stage in the process.
     *
     * @param string $strMethodName Name of the method
     * @param bool $blnIncludeCurrentControl Include this control as well?
     *
     * @return null|ControlBase The control found in the hierarchy to have the method
     *                       Or null if no control was found in the hierarchy with the given name
     */
    public function getControlFromHierarchyByMethodName(string $strMethodName, bool $blnIncludeCurrentControl = true): ?ControlBase
    {
        if ($blnIncludeCurrentControl) {
            $ctlDelegatorControl = $this;
        } else {
            if (!$this->ParentControl) {
                // ParentControl is null. This means the parent is a Form.
                $ctlDelegatorControl = $this->Form;
            } else {
                $ctlDelegatorControl = $this->ParentControl;
            }
        }

        do {
            if (method_exists($ctlDelegatorControl, $strMethodName)) {
                return $ctlDelegatorControl;
            } else {
                if (!$ctlDelegatorControl->ParentControl) {
                    // ParentControl is null. This means the parent is a Form.
                    $ctlDelegatorControl = $ctlDelegatorControl->Form;
                } else {
                    $ctlDelegatorControl = $ctlDelegatorControl->ParentControl;
                }
            }
        } while (!($ctlDelegatorControl instanceof FormBase));

        // If we are here, we could not find the method in the hierarchy/lineage of this control.
        return null;
    }

    /**
     * Returns the form associated with the control. Used by the DataBinder trait.
     * @return FormBase
     */
    public function getForm(): FormBase
    {
        return $this->objForm;
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * PHP __get magic method implementation
     * @param string $strName Property Name
     *
     * @return mixed
     * @throws Exception\Caller
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case "Display":
                return $this->blnDisplay;
            case "CausesValidation":
                return $this->mixCausesValidation;
            case "Required":
                return $this->blnRequired;
            case "ValidationError":
                return $this->strValidationError;
            case "Visible":
                return $this->blnVisible;
            case "PreferredRenderMethod":
                return $this->strPreferredRenderMethod;

            // LAYOUT
            case "HtmlBefore":
                return $this->strHtmlBefore;
            case "HtmlAfter":
                return $this->strHtmlAfter;
            case "Instructions":
                return $this->strInstructions;
            case "Warning":
                return $this->strWarning;
            case "Minimize":
                return $this->blnMinimize;

            case "Moveable":
                return $this->objDraggable && !$this->objDraggable->Disabled;
            case "Resizable":
                return $this->objResizable && !$this->objResizable->Disabled;
            case "Droppable":
                return $this->objDroppable && !$this->objDroppable->Disabled;
            case "DragObj":
                return $this->objDraggable;
            case "ResizeObj":
                return $this->objResizable;
            case "DropObj":
                return $this->objDroppable;

            // MISC
            case "ControlId":
                return $this->strControlId;
            case "Form":
                return $this->objForm;
            case "ParentControl":
                return $this->objParentControl;

            case "Name":
                return $this->strName;
            case "Rendered":
                return $this->blnRendered;
            case "Rendering":
                return $this->blnRendering;
            case "OnPage":
                return $this->blnOnPage;
            case "RenderMethod":
                return $this->strRenderMethod;
            case "WrapperModified":
                return $this->blnWrapperModified;
            case "ActionParameter":
                return $this->mixActionParameter;
            case "ActionsMustTerminate":
                return $this->blnActionsMustTerminate;
            case "ScriptsOnly":
                return $this->blnScriptsOnly;
            case "WrapperCssClass":
                return $this->getWrapperStyler()->CssClass;
            case "UseWrapper":
                return $this->blnUseWrapper;

            // SETTINGS
            case "JavaScripts":
                return $this->strJavaScripts;
            case "StyleSheets":
                return $this->strStyleSheets;

            case "Modified":
                return $this->isModified();
            case "LinkedNode":
                return $this->objLinkedNode;
            case "WrapperStyles":
                return $this->getWrapperStyler();
            case "WrapLabel":
                return $this->blnWrapLabel;
            case "AutoRender":
                return $this->blnAutoRender;

            default:
                try {
                    return parent::__get($strName);
                } catch (Exception\Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /////////////////////////
    // Public Properties: SET
    /////////////////////////
    /**
     * PHP __set magic method implementation
     *
     * @param string $strName Property Name
     * @param string $mixValue Property Value
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            // Shunt position settings to the wrapper. Actual drawing will get resolved at draw time.
            case "Position":
            case "Top":
            case "Left":
                try {
                    $this->getWrapperStyler()->__set($strName, $mixValue);
                    $this->markAsWrapperModified();
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Display":    // boolean to determine whether to display or not
                try {
                    $mixValue = Type::cast($mixValue, Type::BOOLEAN);
                    $this->markAsWrapperModified();
                    $this->blnDisplay = $mixValue;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "CausesValidation":
                $this->mixCausesValidation = $mixValue;
                // This would not need to cause a redrawing
                break;
            case "Required":
                try {
                    $this->blnRequired = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Visible":
                try {
                    if ($this->blnVisible !== ($mixValue = Type::cast($mixValue, Type::BOOLEAN))) {
                        $this->markAsModified();
                        $this->blnVisible = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "PreferredRenderMethod":
                try {
                    if ($this->strPreferredRenderMethod !== ($mixValue = Type::cast($mixValue, Type::STRING))) {
                        $this->markAsModified();
                        $this->strPreferredRenderMethod = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "HtmlBefore":
                try {
                    if ($this->strHtmlBefore !== ($mixValue = Type::cast($mixValue, Type::STRING))) {
                        $this->markAsModified();
                        $this->strHtmlBefore = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "HtmlAfter":
                try {
                    if ($this->strHtmlAfter !== ($mixValue = Type::cast($mixValue, Type::STRING))) {
                        $this->markAsModified();
                        $this->strHtmlAfter = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Instructions":
                try {
                    if ($this->strInstructions !== ($mixValue = Type::cast($mixValue, Type::STRING))) {
                        $this->markAsModified();
                        $this->strInstructions = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Warning":
                try {
                    if (trim($mixValue) === '') { // treat empty strings as nulls to prevent unnecessary drawing
                        $mixValue = null;
                    }
                    if ($this->strWarning !== ($mixValue = Type::cast($mixValue, Type::STRING))) {
                        $this->strWarning = $mixValue;
                        $this->markAsModified();
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "ValidationError":
                try {
                    if (trim($mixValue) === '') { // treat empty strings as nulls to prevent unnecessary drawing
                        $mixValue = null;
                    }
                    if ($this->strValidationError !== ($mixValue = Type::cast($mixValue, Type::STRING))) {
                        $this->strValidationError = $mixValue;
                        $this->markAsModified();
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "Minimize":
                try {
                    $this->blnMinimize = Type::cast($mixValue, Type::BOOLEAN);
                    $this->markAsModified();
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "Moveable":
                try {
                    $this->markAsWrapperModified();
                    if (Type::cast($mixValue, Type::BOOLEAN)) {
                        if (!$this->objDraggable) {
                            $this->objDraggable = new Draggable($this, $this->ControlId . 'draggable');
                        } else {
                            $this->objDraggable->Disabled = false;
                        }
                    } else {
                        if ($this->objDraggable) {
                            $this->objDraggable->Disabled = true;
                        }
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "Resizable":
                try {
                    $this->markAsWrapperModified();
                    if (Type::cast($mixValue, Type::BOOLEAN)) {
                        if (!$this->objResizable) {
                            $this->objResizable = new Resizable($this);
                        } else {
                            $this->objResizable->Disabled = false;
                        }
                    } else {
                        if ($this->objResizable) {
                            $this->objResizable->Disabled = true;
                        }
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "Droppable":
                try {
                    $this->markAsWrapperModified();
                    if (Type::cast($mixValue, Type::BOOLEAN)) {
                        if (!$this->objDroppable) {
                            $this->objDroppable = new Droppable($this);
                        } else {
                            $this->objDroppable->Disabled = false;
                        }
                    } else {
                        if ($this->objDroppable) {
                            $this->objDroppable->Disabled = true;
                        }
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            // MISC
            case "Name":
                try {
                    if ($this->strName !== ($mixValue = Type::cast($mixValue, Type::STRING))) {
                        $this->markAsModified();
                        $this->strName = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "ActionParameter":
                try {
                    $this->mixActionParameter = !$mixValue instanceof Q\Js\Closure ? Type::cast($mixValue,
                        Type::STRING) : $mixValue;
                    $this->markAsModified();
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "WrapperCssClass":
                try {
                    $strWrapperCssClass = Type::cast($mixValue, Type::STRING);
                    if ($this->getWrapperStyler()->setCssClass($strWrapperCssClass)) {
                        $this->markAsWrapperModified();
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "UseWrapper":
                try {
                    if ($this->blnUseWrapper != Type::cast($mixValue, Type::BOOLEAN)) {
                        $this->blnUseWrapper = !$this->blnUseWrapper;
                        //need to render the parent again (including its children)
                        if ($this->ParentControl) {
                            $this->ParentControl->markAsModified();
                        }
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "WrapLabel":
                try {
                    if ($this->blnWrapLabel != Type::cast($mixValue, Type::BOOLEAN)) {
                        $this->blnWrapLabel = !$this->blnWrapLabel;
                        //need to render the parent again (including its children)
                        $this->markAsModified();
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "SaveState":
                try {
                    $this->blnSaveState = Type::cast($mixValue, Type::BOOLEAN);
                    $this->_ReadState(); // during form creation, if we are setting this value, it means we want the state restored at form creation too, so handle both here.
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case "AutoRender":
                try {
                    $this->blnAutoRender = Type::cast($mixValue, Type::BOOLEAN);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;


            // CODEGEN
            case "LinkedNode":
                try {
                    $this->objLinkedNode = Type::cast($mixValue, '\\QCubed\\Query\\Node\\NodeBase');
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            default:
                try {
                    parent::__set($strName, $mixValue);
                    break;
                } catch (Exception\Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * Called by the form rendering code to add special attributes to the HTML form tag. If you need a special
     * attribute in your form (e.g., a multipart attribute), add it to the strFormAttributes array.
     *
     * This function is not for general consumption.
     *
     * @return array|null
     * @ignore
     */
    public function _GetFormAttributes(): ?array
    {
        if (Application::isAjax()) {
            if ($this->isModified()) {
                return $this->strFormAttributes;
            } else {
                return null;
            }
        } else {
            return $this->strFormAttributes;
        }
    }

    /**
     * Returns a description of the options available to modify by the designer for the code generator.
     *
     * @return ModelConnectorParam[]
     * @throws Caller
     */
    public static function getModelConnectorParams(): array
    {
        return array(
            new ModelConnectorParam ('Control', 'CssClass', 'CSS Class assigned to the control', Type::STRING),
            new ModelConnectorParam ('Control', 'AccessKey', 'Access Key to focus control', Type::STRING),
            new ModelConnectorParam ('Control', 'CausesValidation',
                'How and what to validate. It Can also be set to a control.', ModelConnectorParam::SELECTION_LIST,
                array(
                    '\\QCubed\\Control\\ControlBase::CAUSES_VALIDATION_NONE' => 'None',
                    '\\QCubed\\Control\\ControlBase::CAUSES_VALIDATION_ALL' => 'All Controls',
                    '\\QCubed\\Control\\ControlBase::CAUSES_VALIDATION_SIBLINGS_AND_CHILDREN' => 'Siblings And Children',
                    '\\QCubed\\Control\\ControlBase::CAUSES_VALIDATION_SIBLINGS_ONLY' => 'Siblings Only'
                )
            ),
            new ModelConnectorParam ('Control', 'Enabled', 'Will it start as enabled (default true)?',
                Type::BOOLEAN),
            new ModelConnectorParam ('Control', 'Required',
                'Will it fail validation if nothing is entered (default depends on data definition, if NULL is allowed.)?',
                Type::BOOLEAN),
            new ModelConnectorParam ('Control', 'TabIndex', '', Type::INTEGER),
            new ModelConnectorParam ('Control', 'ToolTip', '', Type::STRING, ["translate"=>true]),
            new ModelConnectorParam ('Control', 'Visible', '', Type::BOOLEAN),
            new ModelConnectorParam ('Control', 'Height',
                'Height in pixels. However, you can specify a different unit (e.g., 3.0 em).', Type::STRING),
            new ModelConnectorParam ('Control', 'Width',
                'Width in pixels. However, you can specify a different unit (e.g., 3.0 em).', Type::STRING),
            new ModelConnectorParam ('Control', 'Instructions', 'Additional help for user.', Type::STRING, ["translate"=>true]),
            new ModelConnectorParam ('Control', 'Moveable', '', Type::BOOLEAN),
            new ModelConnectorParam ('Control', 'Resizable', '', Type::BOOLEAN),
            new ModelConnectorParam ('Control', 'Droppable', '', Type::BOOLEAN),
            new ModelConnectorParam ('Control', 'UseWrapper', 'Control will be forced to be wrapped with a div',
                Type::BOOLEAN),
            new ModelConnectorParam ('Control', 'WrapperCssClass', '', Type::STRING),
            new ModelConnectorParam ('Control', 'PreferredRenderMethod', '', Type::STRING)
        );

    }


}
