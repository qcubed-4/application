<?php /** @noinspection ALL */

/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use Exception;
use QCubed as Q;
use QCubed\Context;
use QCubed\Exception\Caller;
use QCubed\Exception\DataBind;
use QCubed\Exception\InvalidCast;
use QCubed\Html;
use QCubed\ObjectBase;
use QCubed\Project\Control\FormBase as QForm;
//use QCubed\Project\Control\ControlBase;
use QCubed\Project\Watcher\Watcher;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\Action\ActionBase as QAction;
use Random\RandomException;
use ReflectionClass;
use ReflectionException;
use Throwable;


/**
 * Class FormBase
 *
 * The FormBase is central to the application framework. You can think of it as the control the prints and manage
 * the HTML FORM tag on the page. Since all other controls are contained by the form, the form is responsible for saving
 * and restoring its own state and the state of all the controls on the page. This is what allows QCubed to present all the
 * controls on the client's webpage as if they were PHP objects in your application, so that you don't have to worry about
 * JavaScript state or HTML Gets and Puts.
 *
 * Doing all of this is fairly complex and involves the use of JavaScript in the qcubed.js page, and in various control plugins.
 * It must also detect whether data is submitted via HTML Submit actions or Ajax actions (JavaScript HttpRequest actions),
 * and act accordingly.
 *
 * As of this writing, QCubed can only manage one form on a page, and all Controls must be drawn inside that form
 * (InternetExplorer in particular does not support controls drawn outside of a form).
 *
 * Typically, you will subclass this class and implement a few key overrides (formCreate() for one), and also implement a template to draw the
 * contents of the web page. The template must call renderBegin and renderEnd.
 *
 * @property-read string $FormId              Form ID of the QForm
 * @property-read WaitIcon $DefaultWaitIcon     Default Ajax wait icon control
 * @property-read integer $FormStatus          Status of form (pre-render stage, rendering stage of already rendered stage)
 * @property string $HtmlIncludeFilePath (Alternate) path to the template file to be used
 * @property string $CssClass            Form CSS class.
 * @package QCubed\Control
 */
abstract class FormBase extends ObjectBase
{
    ///////////////////////////
    // Form Status Constants
    ///////////////////////////
    /** Form has not started rendering */
    public const FORM_STATUS_UNRENDERED = 1;
    /** Form has started rendering but has not finished */
    public const FORM_STATUS_RENDER_BEGUN = 2;
    /** Form rendering has already been started and finished */
    public const FORM_STATUS_RENDER_ENDED = 3;

    // Keys for hidden fields that we use to communicate with qcubed.js
    public const POST_CALL_TYPE = 'Qform__FormCallType';   // Do not use this to detect ajax. Use Application::isAjax() instead
    public const POST_FORM_STATE = 'Qform__FormState';

    ///////////////////////////
    // Static Members
    ///////////////////////////
    /** @var bool True when CSS scripts get rendered on a page. Lets user call RenderStyles in a header. */
    protected static bool $blnStylesRendered = false;

    ///////////////////////////
    // Protected Member Variables
    ///////////////////////////
    /** @var string Form ID (usually passed as the first argument to the 'Run' method call) */
    protected string $strFormId;
    /** @var integer representational integer value of what state the form currently is in */
    protected int $intFormStatus;
    /** @var ControlBase[] Array of Controls with this form as the parent */
    protected ?array $objControlArray = null;
    /** @var bool Has the body tag already been rendered? */
    protected ?bool $blnRenderedBodyTag = false;
    protected array $checkableControlValues = array();
    // /** @var string The type of call made to the QForm (Ajax, Server or Fresh GET request) */
    //protected $strCallType; Use Application::isAjax() or Application::instance->context->requestMode() instead
    /** @var null|WaitIcon Default wait icon for the page/QForm */
    protected null|WaitIcon $objDefaultWaitIcon = null;

    /** @var array List of included JavaScript files for this QForm */
    protected array $strIncludedJavaScriptFileArray = array();
    /** @var array List of ignored JavaScript files for this QForm */
    protected array $strIgnoreJavaScriptFileArray = array();

    /** @var array List of included CSS files for this QForm */
    protected array $strIncludedStyleSheetFileArray = array();
    /** @var array List of ignored CSS files for this QForm */
    protected array $strIgnoreStyleSheetFileArray = array();

    protected ?bool $strPreviousRequestMode = false;
    /**
     * @var string The QForm's template file path.
     * When this value is not supplied, the 'Run' function will try to find and use the
     * .tpl.php file with the same filename as the QForm in the same directory as the QForm file.
     */
    protected string $strHtmlIncludeFilePath;
    /** @var string CSS class to be set for the 'form' tag when QCubed Renders the QForm */
    protected string $strCssClass = '';

    /**
     * @var array|null An optional array of custom attributes.
     *                 This can be used to define additional attributes for customization purposes.
     *                 If not set, the default value is null.
     */
    protected ?array $strCustomAttributeArray = null;

    /**
     * @var null|string The key to encrypt the formstate
     * When saving and retrieving from the chosen FormState handler
     */
    public static ?string $EncryptionKey = null;
    /**
     * @var string Chosen DefaultHandler
     *              Default is DefaultHandler as shown here,
     *              however, it is read from the configuration.inc.php (in the QForm class)
     *              In case something goes wrong with QForm; the default DefaultHandler here will
     *              try to take care of the situation.
     */
    public static string $FormStateHandler = 'DefaultHandler';

    /**
     * Generates Control ID used to keep track of those Controls whose ID was not explicitly set.
     * It uses the counter-variable to maintain uniqueness for Control IDs during the life of the page
     * Life of the page is until the time when the formstate expired and is removed by the
     * garbage collection of the formstate handler
     * @return string the Ajax Action ID
     */
    public function generateControlId(): string
    {
        $strToReturn = sprintf('c%s', $this->intNextControlId);
        $this->intNextControlId++;
        return $strToReturn;
    }

    /**
     * @var int Counter variable to contain the numerical part of the Control ID value.
     *      It is automatically incremented everytime the GenerateControlId() runs
     */
    protected int $intNextControlId = 1;

    /////////////////////////
    // Helpers for AjaxActionId Generation
    /////////////////////////
    /**
     * Generates Ajax Action ID used to keep track of Ajax Actions
     * It uses the counter-variable to maintain uniqueness for Ajax Action IDs during the life of the page
     * Life of the page is until the time when the formstate expired and is removed by the
     * garbage collection of the formstate handler
     * @return string the Ajax Action ID
     */
    public function generateAjaxActionId(): string
    {
        $strToReturn = sprintf('a%s', $this->intNextAjaxActionId);
        $this->intNextAjaxActionId++;
        return $strToReturn;
    }

    /**
     * @var int Counter variable to contain the numerical part of the AJAX ID value.
     *          It is automatically incremented everytime the GenerateAjaxActionId() runs
     */
    protected int $intNextAjaxActionId = 1;

    /////////////////////////
    // Event Handlers
    /////////////////////////
    /**
     * Custom Form Run code.
     * To contain code which should be run, 'AFTER' QCubed's QForm run has been completed
     * but 'BEFORE' the custom event handlers are called
     * (In case it is to be used, it should be overridden by a child class)
     */
    protected function formRun(): void
    {
    }

    /**
     * To contain the code which should be executed after the Form Run and
     * before the custom handlers are called (In case it is to be used, it should be overridden by a child class)
     * In this situation, we are about to process an event, or the user has reloaded the page. Do whatever you
     * need to do before any event processing.
     */
    protected function formLoad(): void
    {
    }

    /**
     * To contain the code to initialize the QForm on the first call.
     * Once the QForm is created, the state is saved and is reused by the Run method.
     * In short, this function will run only once (the first time the QForm is to be created)
     * (In case it is to be used, it should be overridden by a child class)
     */
    protected function formCreate(): void
    {
    }

    /**
     * To contain the code to be executed after formRun, formCreate, formLoad has been called,
     * and the custom-defined event handlers have been executed, but an actual rendering process has not begun.
     * This is a good place to put data into a session variable that you need to send to
     * other forms.
     */
    protected function formPreRender(): void
    {
    }

    /**
     * Override this method to set data in your form controls. Appropriate things to do would be to:
     * - Respond to options sent by _GET or _POST variables.
     * - Load data into the control from the database
     * - Initialize controls whose data depends on the state or data in other controls.
     *
     * When this is called, the controls will have been created by formCreate and will have already read their saved state.
     *
     */
    protected function formInitialize(): void
    {
    }

    /**
     * The formValidate method.
     *
     * Before we get here, all the controls will first be validated. Override this method to do
     * additional form level validation, and any form level actions needed as part of the validation process,
     * like displaying an error message.
     *
     * This is the last thing called in the validation process and will always be called if
     * validation is requested, even if prior controls caused a validation error. Return false to prevent
     * validation and cancel the current action.
     *
     * $blnValid will contain the result of control validation. If it is false, you know that validation will
     * fail, regardless of what you return from the function.
     *
     * @return bool    Return false to prevent validation.
     */
    protected function formValidate(): bool
    {
        return true;
    }

    /**
     * If you want to respond in some way to an invalid form that you have not already been able to handle,
     * override this function. For example, you could display a message that an error occurred with some of the
     * controls.
     */
    protected function formInvalid(): void
    {
    }

    /**
     * This function is intended to be overridden by a subclass and is called when the form exits.
     * (After the form render is complete, and just before the Run function completes execution)
     */
    protected function formExit(): void
    {
    }

    /**
     * Exports the object in a parsable string representation
     * @param bool $blnReturn Whether to return the exported string or output it directly
     *
     * @return array|string|null The exported string if $blnReturn is true, otherwise null
     */
    public function varExport(bool $blnReturn = true): array|string|null
    {
        if ($this->objControlArray) {
            foreach ($this->objControlArray as $objControl) {
                $objControl->varExport();  // force the controls to be prepared to serialize
            }
        }
         return var_export($this, $blnReturn);
    }

    /**
     * Retrieves the value of a specified checkable control by its identifier. If the control ID exists
     * in the internal checkable control values, the corresponding value is returned. Otherwise, null is returned.
     *
     * @param string $strControlId The identifier of the checkable control.
     * @return mixed The value of the checkable control if it exists, or null if not.
     */
    public function checkableControlValue(string $strControlId): mixed
    {
        if (array_key_exists($strControlId, $this->checkableControlValues)) {
            return $this->checkableControlValues[$strControlId];
        }
        return null;
    }

    /**
     * Helper function for below GetModifiedControls
     * @param ControlBase $objControl
     * @return boolean
     */
    protected static function isControlModified(ControlBase $objControl): bool
    {
        return $objControl->isModified();
    }

    /**
     * Return only the controls that have been modified
     */
    public function getModifiedControls(): array
    {
        return array_filter($this->objControlArray, 'QForm::IsControlModified');
    }

    /**
     * This method initializes the actual layout of the form
     * It runs in all cases including the initial form (the time when formCreate is run) as well as on
     * trigger actions (ServerAction, AjaxAction, ServerControlAction and AjaxControlAction)
     *
     * It is responsible for implementing the logic and sequence in which page wide checks are done,
     * such as running formValidate and Control validations for every control of the page and their
     * child controls. Checking for an existing FormState and loading them before triggering any action
     * is also a responsibility of this method.
     * @param string $strFormClass The class of the form to create when creating a new form.
     * @param string|null $strAlternateHtmlFile location of the alternate HTML template file.
     * @param string|null $strFormId The HTML id to use for the form. If null, $strFormClass will be used.
     *
     * @throws Caller
     * @throws Throwable Exception
     */
    public static function run(string $strFormClass, ?string $strAlternateHtmlFile = null, ?string $strFormId = null): void
    {
        // See if we can get a Form Class out of PostData

        /** @var QForm $objClass */
        $objClass = null;
        if ($strFormId === null) {
            $strFormId = $strFormClass;
        }

        if (array_key_exists('Qform__FormId',
                $_POST) && ($_POST['Qform__FormId'] == $strFormId) && array_key_exists('Qform__FormState', $_POST)
        ) {
            $strPostDataState = $_POST['Qform__FormState'];

            if ($strPostDataState) // We might have a valid form state -- let's see by un serializing this object
            {
                $objClass = QForm::unserialize($strPostDataState);
            }

            // If there is no QForm Class, then we have an Invalid Form State
            if (!$objClass) {
                self::invalidFormState();
            }
        }

        if ($objClass) {
            // Globalize
            global $_FORM;
            $_FORM = $objClass;

            $objClass->intFormStatus = self::FORM_STATUS_UNRENDERED;

            // Clean up ajax post-data if the encoding does not match, since ajax data is always utf-8
            if (Application::isAjax() && Application::encodingType() != 'UTF-8') {
                foreach ($_POST as $key => $val) {
                    if (!str_starts_with($key, 'Qform_')) {
                        $_POST[$key] = iconv('UTF-8', Application::encodingType(), $val);
                    }
                }
            }

            if (!empty($_POST['Qform__FormParameter'])) {
                $_POST['Qform__FormParameter'] = self::unpackPostVar($_POST['Qform__FormParameter']);
            }

            // Decode custom post variables from server calls
            if (!empty($_POST['Qform__AdditionalPostVars'])) {
                $val = self::unpackPostVar($_POST['Qform__AdditionalPostVars']);
                $_POST = array_merge($_POST, $val);
            }

            // Iterate through all the control modifications
            if (!empty($_POST['Qform__FormUpdates'])) {
                $controlUpdates = $_POST['Qform__FormUpdates'];
                if (is_string($controlUpdates)) {    // Server post is encoded, ajax not encoded
                    $controlUpdates = self::unpackPostVar($controlUpdates);
                }
                if (!empty($controlUpdates)) {
                    foreach ($controlUpdates as $strControlId => $params) {
                        foreach ($params as $strProperty => $strValue) {
                            switch ($strProperty) {
                                case 'Parent':
                                    if ($strValue) {
                                        if ($strValue == $objClass->FormId) {
                                            $objClass->objControlArray[$strControlId]->setParentControl(null);
                                        } else {
                                            $objClass->objControlArray[$strControlId]->setParentControl($objClass->objControlArray[$strValue]);
                                        }
                                    } else {
                                        // Remove all parents
                                        $objClass->objControlArray[$strControlId]->setParentControl(null);
                                        $objClass->objControlArray[$strControlId]->setForm(null);
                                        $objClass->objControlArray[$strControlId] = null;
                                        unset($objClass->objControlArray[$strControlId]);
                                    }
                                    break;
                                default:
                                    if (array_key_exists($strControlId, $objClass->objControlArray)) {
                                        $objClass->objControlArray[$strControlId]->__set($strProperty, $strValue);
                                    }
                                    break;
                            }
                        }
                    }
                }
            }

            // Set the RenderedCheckableControlArray
            if (!empty($_POST['Qform__FormCheckableControls'])) {
                $vals = $_POST['Qform__FormCheckableControls'];
                if (is_string($vals)) { // Server post is encoded, ajax not encoded
                    $vals = self::unpackPostVar($vals);
                }
                $objClass->checkableControlValues = $vals;
            } else {
                $objClass->checkableControlValues = [];
            }

            // This is the original code. In an effort to minimize changes,
            // we aren't going to touch the server calls for now
            if (!Application::isAjax()) {
                foreach ($objClass->objControlArray as $objControl) {
                    // If they were rendered last time and are visible
                    // (And if ServerAction, enabled), then Parse its post-data
                    if (($objControl->Visible) &&
                        ($objControl->Enabled) &&
                        ($objControl->RenderMethod)
                    ) {
                        // Call each control's ParsePostData()
                        $objControl->parsePostData();
                    }

                    // Reset the modified/rendered flags and the validation
                    // in ALL controls
                    $objControl->resetFlags();
                }
            } else {
                // Ajax post. Only send data to controls specified in the post to save time.

                $previouslyFoundArray = array();
                $controls = $_POST;
                $controls = array_merge($controls, $objClass->checkableControlValues);
                foreach ($controls as $key => $val) {
                    if ($key == 'Qform__FormControl') {
                        $strControlId = $val;
                    } elseif (str_starts_with($key, 'Qform_')) {
                        continue;    // ignore this form data
                    } else {
                        $strControlId = $key;
                    }
                    if (($intOffset = strpos($strControlId, '_')) !== false) {    // the first break is the control id
                        $strControlId = substr($strControlId, 0, $intOffset);
                    }

                    if (($objControl = $objClass->getControl($strControlId)) &&
                        !isset($previouslyFoundArray[$strControlId])
                    ) {
                        if (($objControl->Visible) &&
                            ($objControl->RenderMethod)
                        ) {
                            // Call each control's ParsePostData()
                            $objControl->parsePostData();
                        }

                        $previouslyFoundArray[$strControlId] = true;
                    }
                }
            }

            // Only if our action is validating, we are going to reset the validation state of all the controls
            if (isset($_POST['Qform__FormControl']) && isset($objClass->objControlArray[$_POST['Qform__FormControl']])) {
                $objControl = $objClass->objControlArray[$_POST['Qform__FormControl']];
                if ($objControl->CausesValidation) {
                    $objClass->resetValidationStates();
                }
            }

            // Trigger Run Event (if applicable)
            $objClass->formRun();

            // Trigger Load Event (if applicable)
            $objClass->formLoad();

            // Trigger a triggered control's Server- or Ajax-action (e.g., PHP method) here (if applicable)
            $objClass->triggerActions();

        } else {
            // We have no form state -- Create Brand New One
            $objClass = new $strFormClass();

            // Globalize
            global $_FORM;
            $_FORM = $objClass;

            // Setup HTML Include File Path, based on passed-in strAlternateHtmlFile (if any)
            try {
                $objClass->HtmlIncludeFilePath = $strAlternateHtmlFile;
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }

            $objClass->strFormId = $strFormId;
            $objClass->intFormStatus = self::FORM_STATUS_UNRENDERED;
            $objClass->objControlArray = array();

            // Trigger Run Event (if applicable)
            $objClass->formRun();

            // Trigger Create Event (if applicable)
            $objClass->formCreate();

            $objClass->formInitialize();

            if (defined('QCUBED_DESIGN_MODE') && QCUBED_DESIGN_MODE == 1) {
                // Attach a custom event to dialog to handle right click menu items sent by form

                $dlg = new Q\ModelConnector\EditDlg ($objClass, 'qconnectoreditdlg');

                $dlg->addAction(
                    new Q\Event\On('qdesignerclick'),
                    new Q\Action\Ajax ('ctlDesigner_Click', null, null, 'ui')
                );
            }

        }

        // Trigger PreRender Event (if applicable)
        $objClass->formPreRender();

        // Render the Page
        $requestMode = Application::instance()->context()->requestMode();
        if ($requestMode == Context::REQUEST_MODE_QCUBED_AJAX) {
            $objClass->renderAjax();
        } elseif ($requestMode == Context::REQUEST_MODE_QCUBED_SERVER || $requestMode == Context::REQUEST_MODE_HTTP) {
            // Server/Postback or New Page
            // Make sure all controls are marked as not being on the page yet

            foreach ($objClass->objControlArray as $objControl) {
                $objControl->resetOnPageStatus();
            }

            // Use Standard Rendering
            $objClass->render();

            // Ensure that RenderEnd() was called during the Render process
            switch ($objClass->intFormStatus) {
                case self::FORM_STATUS_UNRENDERED:
                    throw new Caller('$this->renderBegin() is never called in the HTML Include a file');
                case self::FORM_STATUS_RENDER_BEGUN:
                    throw new Caller('$this->renderEnd() is never called in the HTML Include a file');
                case self::FORM_STATUS_RENDER_ENDED:
                    break;
                default:
                    throw new Caller('FormStatus is in an unknown status');
            }
        } else {
            throw new Exception('Cannot process request mode: ' . $requestMode);
        }

        // Once all the controls have been set up and initialized, remember them.
        $objClass->saveControlState();

        // Trigger Exit Event (if applicable)
        $objClass->formExit();
    }

    /**
     * Decodes a JSON-encoded POST variable, handling the application's encoding type.
     * This function ensures proper encoding conversion for non-UTF-8 data and decodes the input accordingly.
     *
     * @param string $val The JSON-encoded POST variable to be unpacked.
     * @return mixed The decoded value, which could be a string, array, or other data type, depending on the input.
     * @throws Throwable Exception If the data type cannot be handled or converted properly.
     */
    protected static function unpackPostVar(string $val): mixed
    {
        $encoding = Application::encodingType();
        if ($encoding != 'UTF-8' && Application::instance()->context()->requestMode() != Context::REQUEST_MODE_AJAX) {
            // json_decode only accepts utf-8 encoded text. Ajax calls are already UTF-8 encoded.
            $val = iconv($encoding, 'UTF-8', $val);
        }
        $val = json_decode($val, true);
        if ($encoding != 'UTF-8') {
            // Must convert back from utf-8 to whatever our application encoding is
            if (is_string($val)) {
                $val = iconv('UTF-8', $encoding, $val);
            } elseif (is_array($val)) {
                array_walk_recursive($val, function (&$v, $key) use ($encoding) {
                    if (is_string($v)) {
                        $v = iconv('UTF-8', $encoding, $v);
                    }
                });
            } else {
                throw new Exception ('Unknown Post-Var Type');
            }
        }
        return $val;
    }

    /**
     * Reset all validation states.
     */
    public function resetValidationStates(): void
    {
        foreach ($this->objControlArray as $objControl) {
            $objControl->validationReset();
        }
    }

    /**
     * Handles the click event for the designer control. This method determines the relevant control
     * based on input parameters, retrieves the control, and invokes the edit dialog for further interaction.
     *
     * @param string $strFormId The form ID associated with the event.
     * @param string $strControlId The control ID associated with the event.
     * @param mixed $mixParam An array or object containing additional parameters such as the control ID or associated target.
     * @return void
     */
    private function ctlDesigner_Click(string $strFormId, string $strControlId, mixed $mixParam): void
    {
        if (isset($mixParam['id'])) {
            $controlId = $mixParam['id'];
            if (strpos($controlId, '_')) {    // extra the real control id from a sub id
                $controlId = substr($controlId, 0, strpos($controlId, '_'));
            }
        } elseif (isset($mixParam['for'])) {
            $controlId = $mixParam['for'];
        }
        if (!empty($controlId)) {
            $objControl = $this->getControl($controlId);
            if ($objControl) {
                /** @var Q\ModelConnector\EditDlg $dlg */
                $dlg = $this->getControl('qconnectoreditdlg');
                $dlg->editControl($objControl);
            }
        }
    }

    /**
     * An invalid form state was found.
     * We were handed a formstate, but the formstate could not be interpreted. This could be for
     * a variety of reasons and is dependent on the formstate handler. Most likely, the user hit
     * the back button past the back button limit of what we remember, or the user lost the session.
     * Or, you simply have not set up the form state handler correctly.
     * In the past, we threw an exception, but that was not a very user-friendly response.
     * The response below resubmits the url without a formstate so that a new one will be created.
     * Override if you want a different response.
     * @throws Throwable
     */
    public static function invalidFormState(): void
    {
        //ob_clean();
        if (Application::isAjax()) {
            Application::setProcessOutput(false);
            Application::sendAjaxResponse(['loc' => 'reload']);
        } else {
            header('Location: ' . Application::instance()->context()->requestUri());
            Application::setProcessOutput(false);
        }

        // End the Response Script
        exit();
    }

    /**
     * Binds a data source to a paginated control by invoking the provided callable.
     * If an exception occurs during the binding process, it rethrows the exception
     * wrapped in a DataBind exception.
     *
     * @param callable $callable A callable function or method that performs the data binding.
     * @param mixed $objPaginatedControl The paginated control to which the data will be bound.
     * @return void
     * @throws DataBind
     */
    public function callDataBinder(callable $callable, mixed $objPaginatedControl): void
    {
        try {
            call_user_func($callable, $objPaginatedControl);
        } catch (Caller $objExc) {
            throw new DataBind($objExc);
        }
    }

    /**
     * Renders the AjaxHelper for the QForm
     * @param ControlBase|null $objControl
     *
     * @return string|array The Ajax helper string (should be JS commands)
     */
    protected function renderAjaxHelper(?ControlBase $objControl): string|array
    {
        $controls = [];

        if ($objControl) {
            $controls = array_merge($controls,
                $objControl->renderAjax());    // will return an array of controls to be merged with current controls
            foreach ($objControl->getChildControls() as $objChildControl) {
                $controls = array_merge($controls, $this->renderAjaxHelper($objChildControl));
            }
        }
        return $controls;
    }

    /**
     * Renders the actual ajax return value as a JSON object. Since JSON must be UTF-8 encoded, it will convert to
     * UTF-8 if needed. Response is parsed in the "success" function in qcubed.js and handled there.
     * @throws Throwable Exception
     */
    protected function renderAjax(): void
    {
        $aResponse = array();

        if (Application::instance()->jsResponse()->hasExclusiveCommand()) {
            /**
             * Processing of the actions has resulted in a very high-priority exclusive response. This would typically
             * happen when a JavaScript widget is requesting data from us. We want to respond as quickly as possible
             * and also prevent possibly redrawing the widget while it is already in the middle of its own drawing.
             * We short-circuit the drawing process here.
             */

            $aResponse = Application::getJavascriptCommandArray();
            $strFormState = QForm::serialize($this);
            $aResponse[Q\JsResponse::CONTROLS][] = [
                Q\JsResponse::ID => self::POST_FORM_STATE,
                Q\JsResponse::VALUE => $strFormState
            ];    // bring it back next time
            ob_clean();
            Application::sendAjaxResponse($aResponse);
            return;
        }

        // Update the Status
        $this->intFormStatus = self::FORM_STATUS_RENDER_BEGUN;

        // Broadcast the watcher change to other windows listening
        if (Watcher::watchersChanged()) {
            $aResponse[Q\JsResponse::WATCHER] = true;
        }

        // Recursively render changed controls, starting with all top-level controls
        $controls = array();
        foreach ($this->getAllControls() as $objControl) {
            if (!$objControl->ParentControl) {
                $controls = array_merge($controls, $this->renderAjaxHelper($objControl));
            }
        }
        $aResponse[Q\JsResponse::CONTROLS] = $controls;

        // Go through all controls and gather up any JS or CSS to run or Form Attributes to modify
        foreach ($this->getAllControls() as $objControl) {
            // Include any JavaScript files that were added by the control
            // Note: current implementation does not handle removal of JavaScript files
            if ($strScriptArray = $this->processJavaScriptList($objControl->JavaScripts)) {
                Application::addJavaScriptFiles($strScriptArray);
            }

            // Include any new stylesheets
            if ($strScriptArray = $this->processStyleSheetList($objControl->StyleSheets)) {
                Application::addStyleSheets(array_keys($strScriptArray));
            }

            // Form Attributes
            $attributes = $objControl->_GetFormAttributes();
            if ($attributes) {
                // Make sure the form has attributes that the control requires.
                // If such a control gets removed from a form during an ajax call, but that is a very unlikely scenario.
                Application::executeControlCommand($this->strFormId, 'attr', $attributes);
            }
        }

        $strControlIdToRegister = array();
        foreach ($this->getAllControls() as $objControl) {
            $strScript = '';
            if ($objControl->Rendered) { // whole control was rendered during this event
                $strScript = trim($objControl->getEndScript());
                $strControlIdToRegister[] = $objControl->ControlId;
            } else {
                $objControl->renderAttributeScripts(); // render one-time attribute commands only
            }
            if ($strScript) {
                Application::executeJavaScript($strScript,
                    Q\ApplicationBase::PRIORITY_HIGH);    // put these lasts in the high-priority queue, just before getting the commands below
            }
            $objControl->resetFlags();
        }

        if ($strControlIdToRegister) {
            $aResponse[Q\JsResponse::REG_C] = $strControlIdToRegister;
        }

        $aResponse = array_merge($aResponse, Application::getJavascriptCommandArray());
        // Add in the form state
        $strFormState = QForm::serialize($this);
        $aResponse[Q\JsResponse::CONTROLS][] = [
            Q\JsResponse::ID => self::POST_FORM_STATE,
            Q\JsResponse::VALUE => $strFormState
        ];

        $strContents = trim(ob_get_contents());

        if (strtolower(substr($strContents, 0, 5)) == 'debug') {
            // TODO: Output debugging information.
        } else {
            ob_end_clean();

            Application::sendAjaxResponse($aResponse);
        }

        // Update Render State
        $this->intFormStatus = self::FORM_STATUS_RENDER_ENDED;
    }

    /**
     * Serializes a given FormBase object and saves its state using the defined form state handler.
     *
     * @param FormBase $objForm The form object to be serialized.
     *
     * @return string Serialized form state saved by the form state handler.
     */
    public static function serialize(FormBase $objForm): string
    {
        // Get and then Update PreviousRequestMode
        $strPreviousRequestMode = $objForm->strPreviousRequestMode;
        $objForm->strPreviousRequestMode = Application::instance()->context()->requestMode();

        // Figure Out if we need to store state for back-button purposes
        $blnBackButtonFlag = true;
        if ($strPreviousRequestMode == Context::REQUEST_MODE_QCUBED_AJAX) {
            $blnBackButtonFlag = false;
        }

        // Create a Clone of the Form to Serialize
        $objForm = clone($objForm);

        // Cleanup internal links between controls and the form
        if ($objForm->objControlArray) {
            foreach ($objForm->objControlArray as $objControl) {
                $objControl->sleep();
            }
        }

        // Use PHP "serialize" to serialize the form
        $strSerializedForm = serialize($objForm);

        // Setup and Call the FormStateHandler to retrieve the PostDataState to return
        $strFormStateHandler = QForm::$FormStateHandler;

        // Return the PostDataState
        return $strFormStateHandler::save($strSerializedForm, $blnBackButtonFlag);
    }

    /**
     * Un serializes (extracts) the FormState using the 'Load' method of FormStateHandler set in configuration.inc.php
     * @param string $strPostDataState The string identifying the FormState to the loaded for Serialization
     *
     * @return QForm|null the Form object
     * @throws Caller
     * @throws InvalidCast
     * @internal param string $strSerializedForm
     */
    public static function unserialize(string $strPostDataState): ?QForm
    {
        // Setup and Call the FormStateHandler to retrieve the Serialized Form
        $strFormStateHandler = QForm::$FormStateHandler;
        $strSerializedForm = $strFormStateHandler::load($strPostDataState);

        if ($strSerializedForm) {
            // Unserialize and Cast the Form
            // For the QSessionFormStateHandler the __PHP_Incomplete_Class sometimes occurs
            // for the result of the unserialize call.
            /** @var QForm $objForm */
            $objForm = unserialize($strSerializedForm);
            $objForm = Type::cast($objForm, '\QCubed\Project\Control\FormBase');

            // Reset the links from Control->Form
            if ($objForm->objControlArray) {
                foreach ($objForm->objControlArray as $objControl) {
                    // If you are having trouble with a __PHP_Incomplete_Class here, it means you are not including the definitions
                    // of your own controls in the form.
                    $objControl->wakeup($objForm);
                }
            }
            // Return the Form
            return $objForm;
        } else {
            return null;
        }
    }

    /**
     * Add a Control to the current QForm.
     * @param  ControlBase $objControl
     *
     * @throws Caller
     */
    public function addControl(ControlBase $objControl): void
    {
        $strControlId = $objControl->ControlId;
        $objControl->markAsModified(); // make sure new controls get drawn
        if (array_key_exists($strControlId, $this->objControlArray)) {
            throw new Caller(sprintf('A control already exists in the form with the ID: %s', $strControlId));
        }

        $this->objControlArray[$strControlId] = $objControl;
    }

    /**
     * Returns a control from the current QForm
     * @param string $strControlId The Control ID of the control which is needed to be fetched
     *               from the current QForm (should be the child of the current QForm).
     *
     * @return null|ControlBase
     */
    public function getControl(string $strControlId): ?ControlBase
    {
        return $this->objControlArray[$strControlId] ?? null;
    }

    /**
     * Removes a Control (and its children) from the current QForm
     * @param string $strControlId
     * @throws Caller
     */
    public function removeControl(string $strControlId): void
    {
        if (isset($this->objControlArray[$strControlId])) {
            // Get the Control in Question
            $objControl = $this->objControlArray[$strControlId];

            // Remove all Child Controls as well
            $objControl->removeChildControls(true);

            // Remove this control from the parent
            if ($objControl->ParentControl) {
                $objControl->ParentControl->removeChildControl($strControlId,
                    false);    // will redraw the ParentControl
            } else {
                // if the parent is the form, then remove it from the dom through JavaScript, since the form won't be redrawn
                Application::executeSelectorFunction('#' . $objControl->getWrapperId(), 'remove');
            }

            // Remove this control
            unset($this->objControlArray[$strControlId]);

        }
    }

    /**
     * Retrieves all controls in the current control array.
     *
     * @return array The array of all controls.
     */
    public function getAllControls(): array
    {
        return $this->objControlArray;
    }

    /**
     * Tell all the controls to save their state.
     */
    public function saveControlState(): void
    {
        // Tell the controls to save their state
        $a = $this->getAllControls();
        foreach ($a as $control) {
            $control->_WriteState();
        }
    }

    /**
     * Tell all the controls to read their state.
     */
    protected function restoreControlState(): void
    {
        // Tell the controls to restore their state
        $a = $this->getAllControls();
        foreach ($a as $control) {
            $control->_ReadState();
        }
    }

    /**
     * Custom Attributes are other HTML name-value pairs that can be rendered within the form using this method.
     * For example, you can now render the autocomplete tag on the QForm
     * additional JavaScript actions, etc.
     *        $this->setCustomAttribute("autocomplete", "off");
     * Will render:
     *        [form … autocomplete="off"] (replace square brackets with angle brackets)
     * @param string $strName Name of the attribute
     * @param string $strValue Value of the attribute
     *
     * @throws Caller
     */
    public function setCustomAttribute(string $strName, string $strValue): void
    {
        if ($strName == "method" || $strName == "action") {
            throw new Caller(sprintf("Custom Attribute is not supported through SetCustomAttribute(): %s",
                $strName));
        }

        if (!is_null($strValue)) {
            $this->strCustomAttributeArray[$strName] = $strValue;
        } else {
            $this->strCustomAttributeArray[$strName] = null;
            unset($this->strCustomAttributeArray[$strName]);
        }
    }

    /**
     * Returns the requested custom attribute from the form.
     * This attribute must have already been set.
     * @param string $strName Name of the Custom Attribute
     *
     * @return mixed
     * @throws Caller
     */
    public function getCustomAttribute(string $strName): mixed
    {
        if ((is_array($this->strCustomAttributeArray)) && (array_key_exists($strName,
                $this->strCustomAttributeArray))
        ) {
            return $this->strCustomAttributeArray[$strName];
        } else {
            throw new Caller(sprintf("Custom Attribute does not exist in Form: %s", $strName));
        }
    }

    /**
     * Removes a custom attribute from the custom attribute array.
     *
     * If the specified attribute exists in the custom attribute array, it will be removed.
     * Throw an exception if the attribute does not exist.
     *
     * @param string $strName The name of the custom attribute to remove.
     * @return void
     * @throws Caller If the specified custom attribute does not exist.
     */
    public function removeCustomAttribute(string $strName): void
    {
        if ((is_array($this->strCustomAttributeArray)) && (array_key_exists($strName,
                $this->strCustomAttributeArray))
        ) {
            $this->strCustomAttributeArray[$strName] = null;
            unset($this->strCustomAttributeArray[$strName]);
        } else {
            throw new Caller(sprintf("Custom Attribute does not exist in Form: %s", $strName));
        }
    }

    /**
     * Returns the child controls of the current Form or a Control object
     *
     * @param ControlBase|FormBase $objParentObject The object whose child controls are to be searched for
     *
     * @return ControlBase[]
     *@throws Caller
     */
    public function getChildControls(FormBase|ControlBase $objParentObject): array
    {
        $objControlArrayToReturn = array();

        if ($objParentObject instanceof QForm) {
            // They want all the ChildControls for this Form
            // Basically, return all objControlArray Controls where the Control's parent is NULL
            foreach ($this->objControlArray as $objChildControl) {
                if (!($objChildControl->ParentControl)) {
                    $objControlArrayToReturn[] = $objChildControl;
                }
            }
            return $objControlArrayToReturn;

        } else {
            if ($objParentObject instanceof ControlBase) {
                return $objParentObject->getChildControls();
            } else {
                throw new Caller('ParentObject must be either a Form or Control object');
            }
        }
    }

    /**
     * This function evaluates the QForm Template.
     * It will try to open the Template file specified in the call to 'Run' method for the QForm
     * and then execute it.
     * @param string $strTemplate Path to the HTML template file
     *
     * @return string|null The evaluated HTML string
     */
    public function evaluateTemplate(string $strTemplate): ?string
    {
        global $_ITEM;
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
            require($strTemplate);
            $strTemplateEvaluated = ob_get_contents();
            ob_end_clean();

            // Restore the output buffer and return the evaluated template
            if ($strAlreadyRendered) {
                print($strAlreadyRendered);
            }
            Application::setProcessOutput($blnProcessing);

            return $strTemplateEvaluated;
        } else {
            return null;
        }
    }

    /**
     * Triggers an event handler method for a given control ID
     * NOTE: Parameters must be already validated and are guaranteed to exist.
     *
     * @param string $strControlId Control ID triggering the method
     * @param string $strMethodName Method name which has to be fired. Includes a control id if a control action.
     * @param QAction $objAction The action object which caused the event
     * @throws ReflectionException
     */
    protected function triggerMethod(string $strControlId, string $strMethodName, QAction $objAction): void
    {
        $mixParameter = $_POST['Qform__FormParameter'];
        $objMethodParam = new Q\Action\ActionParams($objAction, $this, $strControlId, $mixParameter);
        ControlBase::_processActionParams($objMethodParam);

        if (strpos($strMethodName, '::')) {
            // Static method calling
            $f = explode('::', $strMethodName);
            if (is_callable($f)) {
                $ref = new ReflectionClass($f[0]);
                $argCount = $ref->getMethod($f[1])->getNumberOfParameters();
                if ($argCount > 1) {
                    $f($this->strFormId, $strControlId, $objMethodParam->Param);
                } else {
                    $f($objMethodParam);
                }
            }
        } elseif (($intPosition = strpos($strMethodName, ':')) !== false) {
            $strDestControlId = substr($strMethodName, 0, $intPosition);
            $strMethodName = substr($strMethodName, $intPosition + 1);
            $objDestControl = $this->getControl($strDestControlId);

            ControlBase::_callActionMethod($objDestControl, $strMethodName, $objMethodParam);
        } else {
            $ref = new ReflectionClass(get_class($this));
            $argCount = $ref->getMethod($strMethodName)->getNumberOfParameters();

            if ($argCount > 1) {
                $this->$strMethodName($this->strFormId, $strControlId, $objMethodParam->Param, $objMethodParam);
            } else {
                $this->$strMethodName($objMethodParam);
            }
        }
    }

    /**
     * Calls 'Validate' method on a Control recursively
     * @param ControlBase $objControl
     *
     * @return bool
     */
    protected function validateControlAndChildren(ControlBase $objControl): bool
    {
        return $objControl->validateControlAndChildren();
    }

    /**
     * Handles the triggering of actions for a specific control based on posted data and request mode.
     * The method determines the control and event causing the action, performs validation, and executes
     * a corresponding server or AJAX action if conditions are met.
     *
     * @param string|null $strControlIdOverride Optional control ID to override the one provided in the posted data.
     * @return void
     * @throws Throwable Exception If the request mode is unknown or the control specified in the posted data does not exist.
     */
    protected function triggerActions(?string $strControlIdOverride = null): void
    {
        if (array_key_exists('Qform__FormControl', $_POST)) {
            if ($strControlIdOverride) {
                $strControlId = $strControlIdOverride;
            } else {
                $strControlId = $_POST['Qform__FormControl'];
            }

            // Control ID determined
            if ($strControlId != '') {
                $strEvent = $_POST['Qform__FormEvent'];
                $strAjaxActionId = null;

                // Does this Control, which performed the action, exist?
                if (array_key_exists($strControlId, $this->objControlArray)) {
                    // Get the ActionControl as well as the Actions to Perform
                    $objActionControl = $this->objControlArray[$strControlId];

                    switch (Application::instance()->context()->requestMode()) {
                        case Context::REQUEST_MODE_QCUBED_AJAX:
                            // split up event class name and ajax action id: i.e.: QClickEvent#a3 => [QClickEvent, a3]
                            $arrTemp = explode('#', $strEvent);
                            $strEvent = $arrTemp[0];
                            if (count($arrTemp) == 2) {
                                $strAjaxActionId = $arrTemp[1];
                            }
                            $objActions = $objActionControl->getAllActions($strEvent, 'QCubed\\Action\\Ajax');
                            break;
                        case Context::REQUEST_MODE_QCUBED_SERVER:
                            $objActions = $objActionControl->getAllActions($strEvent, 'QCubed\\Action\\Server');
                            break;
                        default:
                            throw new Exception('Unknown request mode: ' . Application::instance()->context()->requestMode());
                    }

                    // Validation Check
                    $blnValid = true;
                    $mixCausesValidation = null;

                    // Figure out what the CausesValidation directive is
                    // Set $mixCausesValidation to the default one (e.g., the one defined on the control)
                    $mixCausesValidation = $objActionControl->CausesValidation;

                    // Next, go through the linked ajax/server actions to see if a cause validation override is set on any of them
                    if ($objActions) {
                        foreach ($objActions as $objAction) {
                            if (($objAction instanceof Q\Action\Server || $objAction instanceof Q\Action\Ajax) &&
                                !is_null($objAction->CausesValidationOverride)
                            ) {
                                $mixCausesValidation = $objAction->CausesValidationOverride;
                            }
                        }
                    }

                    // Now, Do Something with mixCauseValidation...

                    // The Starting Point is Control
                    if ($mixCausesValidation instanceof ControlBase) {
                        if (!$this->validateControlAndChildren($mixCausesValidation)) {
                            $blnValid = false;
                        }

                        // The Starting Point is an Array of Controls
                    } else {
                        if (is_array($mixCausesValidation)) {
                            foreach (($mixCausesValidation) as $objControlToValidate) {
                                if (!$this->validateControlAndChildren($objControlToValidate)) {
                                    $blnValid = false;
                                }
                            }

                            // Validate All the Controls on the Form
                        } else {
                            if ($mixCausesValidation === ControlBase::CAUSES_VALIDATION_ALL) {
                                foreach ($this->getChildControls($this) as $objControl) {
                                    // Only Enabled and Visible and Rendered controls that are children of this form should be validated
                                    if (($objControl->Visible) && ($objControl->Enabled) && ($objControl->RenderMethod) && ($objControl->OnPage)) {
                                        if (!$this->validateControlAndChildren($objControl)) {
                                            $blnValid = false;
                                        }
                                    }
                                }

                            } else {
                                if ($mixCausesValidation == ControlBase::CAUSES_VALIDATION_SIBLINGS_AND_CHILDREN) {
                                    // Get only the Siblings of the ActionControl's ParentControl
                                    // If not ParentControl, then the parent is the form itself
                                    if (!($objParentObject = $objActionControl->ParentControl)) {
                                        $objParentObject = $this;
                                    }
                                    // Get all the children of ParentObject
                                    foreach ($this->getChildControls($objParentObject) as $objControl) {
                                        // Only Enabled and Visible and Rendered controls that are children of ParentObject should be validated
                                        if (($objControl->Visible) && ($objControl->Enabled) && ($objControl->RenderMethod) && ($objControl->OnPage)) {
                                            if (!$this->validateControlAndChildren($objControl)) {
                                                $blnValid = false;
                                            }
                                        }
                                    }
                                } else {
                                    if ($mixCausesValidation == ControlBase::CAUSES_VALIDATION_SIBLINGS_ONLY) {
                                        // Get only the Siblings of the ActionControl's ParentControl
                                        // If not ParentControl, then the parent is the form itself
                                        if (!($objParentObject = $objActionControl->ParentControl)) {
                                            $objParentObject = $this;
                                        }

                                        // Get all the children of ParentObject
                                        foreach ($this->getChildControls($objParentObject) as $objControl) // Only Enabled and Visible and Rendered controls that are children of ParentObject should be validated
                                        {
                                            if (($objControl->Visible) && ($objControl->Enabled) && ($objControl->RenderMethod) && ($objControl->OnPage)) {
                                                if (!$objControl->validate()) {
                                                    $objControl->markAsModified();
                                                    $blnValid = false;
                                                }
                                            }
                                        }

                                        // No Validation Requested
                                    }
                                }
                            }
                        }
                    }


                    // Run Form-Specific Validation (if any)
                    if ($mixCausesValidation && !($mixCausesValidation instanceof DialogInterface)) {
                        if (!$this->formValidate()) {
                            $blnValid = false;
                        }
                    }

                    // Go ahead and run the ServerActions or AjaxActions if Validation Passed, and if there are Server/Ajax-Actions defined

                    if ($blnValid) {
                        if ($objActions) {
                            foreach ($objActions as $objAction) {
                                if ($strMethodName = $objAction->MethodName) {
                                    if (($strAjaxActionId == null)            //if this call was not an ajax call
                                        || ($objAction->Id == null)        // or the AjaxAction derived action has no id set
                                        //(a possible way to add a callback that gets executed on every ajax call for this control)
                                        || ($strAjaxActionId == $objAction->Id)
                                    ) //or the ajax action id passed from the client side equals the id of the current ajax action
                                    {
                                        $this->triggerMethod($strControlId, $strMethodName, $objAction);
                                    }
                                }
                            }
                        }
                    } else {
                        $this->formInvalid();    // notify form that something went wrong
                    }
                } else {
                    // Nope -- Throw an exception
                    throw new Exception(sprintf('Control passed by Qform__FormControl does not exist: %s',
                        $strControlId));
                }
            }
            /* else {
                // TODO: Code to automatically execute any PrimaryButton's onclick action, if applicable
                // Difficult b/c of all the QCubed hidden parameters that need to be set to get the action to work properly
                // Javascript interaction of PrimaryButton works fine in Firefox... currently doesn't work in IE 6.
            }*/
        }
    }

    /**
     * Begins rendering the page
     */
    protected function render(): void
    {
        if (Watcher::watchersChanged()) {
            Application::executeJsFunction('qc.broadcastChange');
        }

        require($this->HtmlIncludeFilePath);
    }

    /**
     * Render the children of this QForm
     * @param bool $blnDisplayOutput
     *
     * @return null|string Null when blnDisplayOutput is true
     * @throws Caller
     */
    protected function renderChildren(bool $blnDisplayOutput = true): ?string
    {
        $strToReturn = "";

        foreach ($this->getChildControls($this) as $objControl) {
            if (!$objControl->Rendered) {
                $strToReturn .= $objControl->render($blnDisplayOutput);
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
     * This exists to prevent inadvertent "New"
     */
    protected function __construct()
    {
    }

    /**
     * Renders the required stylesheets for the application and optionally displays the output directly.
     *
     * This method collects all necessary stylesheets from controls and form-specific styles.
     * It ensures that stylesheets are ordered correctly, including moving any jQuery UI CSS file
     * to the end of the list to allow theme overrides. It generates HTML `<link>` tags for inclusion
     * of these stylesheets, optionally handling additional attributes like CORS or integrity.
     * The output can either be printed to the browser or returned as a string depending on the parameter.
     *
     * @param bool $blnDisplayOutput Determines whether the generated styles should be directly outputted.
     *                               If true, styles are printed; otherwise, the method returns the
     *                               generated HTML string.
     * @return string|null The generated HTML string containing the `<link>` tags for stylesheets of
     *                     `$blnDisplayOutput` is false, or null if `$blnDisplayOutput` is true.
     * @throws Caller
     */
    public function renderStyles(bool $blnDisplayOutput = true): ?string
    {
        $strToReturn = '';
        $this->strIncludedStyleSheetFileArray = array();

        $strStyleSheetArray = [];

        // Collect all stylesheets from controls
        foreach ($this->getAllControls() as $objControl) {
            if ($strScriptArray = $this->processStyleSheetList($objControl->StyleSheets)) {
                $strStyleSheetArray = array_merge($strStyleSheetArray, $strScriptArray);
            }
        }

        // Move the jQuery UI CSS file to the end of the list to ensure theme overrides are applied
        foreach ($strStyleSheetArray as $strScript) {
            if (QCUBED_JQUI_CSS === $strScript) {
                unset($strStyleSheetArray[$strScript]);
                $strStyleSheetArray[$strScript] = $strScript;
                break;
            }
        }

        // Add any form-specific styles
        foreach ($this->getFormStyles() as $strScript) {
            $strStyleSheetArray[$strScript] = $strScript;
        }

        // Prepare indentation
        $indent = "    "; // 4 spaces
        $totalFiles = count($strStyleSheetArray); // Get the total count of stylesheets
        $currentIndex = 0; // Start index tracker

        // Generate <link> elements for all stylesheets
        foreach ($strStyleSheetArray as $strScript) {
            $currentIndex++; // Increment current index

            if (is_array($strScript)) { // Handle additional attributes like CORS or integrity
                $href = $strScript["file"];
                unset($strScript["file"]);
                $attributes = [
                    "href" => $this->getCssFileUri($href),
                    "rel" => "stylesheet",
                ];
                $attributes = array_merge($attributes, $strScript);
                $strToReturn .= Html::renderTag("link", $attributes);
            } else {
                $strToReturn .= '<link href="' . $this->getCssFileUri($strScript) . '" rel="stylesheet" />';
            }

            // Add a newline after each file, including the last one
            $strToReturn .= "\n";

            // Add indentation only if it is NOT the last file
            if ($currentIndex < $totalFiles) {
                $strToReturn .= $indent;
            }
        }

        self::$blnStylesRendered = true;

        // Output or return the generated styles
        if ($blnDisplayOutput) {
            if (Application::instance()->context()->requestMode() !== Context::REQUEST_MODE_CLI) {
                print($strToReturn);
            }
            return null;
        } else {
            if (Application::instance()->context()->requestMode() !== Context::REQUEST_MODE_CLI) {
                return $strToReturn;
            } else {
                return '';
            }
        }
    }

    /**
     * Initializes the QForm rendering process
     * @param bool $blnDisplayOutput Whether the output is to be printed (true) or simply returned (false)
     *
     * @return null|string
     * @throws Caller
     */
    public function renderBegin(bool $blnDisplayOutput = true): ?string
    {
        // Ensure that RenderBegin() has not yet been called
        switch ($this->intFormStatus) {
            case self::FORM_STATUS_UNRENDERED:
                break;
            case self::FORM_STATUS_RENDER_BEGUN:
            case self::FORM_STATUS_RENDER_ENDED:
                throw new Caller('$this->renderBegin() has already been called');
                //break;
            default:
                throw new Caller('FormStatus is in an unknown status');
        }

        // Update FormStatus and Clear Included JS/CSS list
        $this->intFormStatus = self::FORM_STATUS_RENDER_BEGUN;

        // Prepare for rendering

        $blnProcessing = Application::setProcessOutput(false);
        $strOutputtedText = trim(ob_get_contents());
        if (!str_contains(strtolower($strOutputtedText), '<body')) {
            $strToReturn = '<body>';
            $this->blnRenderedBodyTag = true;
        } else {
            $strToReturn = '';
        }
        Application::setProcessOutput($blnProcessing);


        // Iterate through the form's ControlArray to Define FormAttributes and additional JavaScriptIncludes
        $strFormAttributeArray = array();
        foreach ($this->getAllControls() as $objControl) {
            // Form Attributes?
            if ($attributes = $objControl->_GetFormAttributes()) {
                $strFormAttributeArray = array_merge($strFormAttributeArray, (array)$attributes);
            }
        }

        if (is_array($this->strCustomAttributeArray)) {
            $strFormAttributeArray = array_merge($strFormAttributeArray, $this->strCustomAttributeArray);
        }

        if ($this->strCssClass) {
            $strFormAttributeArray['class'] = $this->strCssClass;
        }
        $strFormAttributeArray['method'] = 'post';
        $strFormAttributeArray['id'] = $this->strFormId;
        $strFormAttributeArray['action'] = Application::instance()->context()->requestUri();
        $strToReturn .= '<form ' . Html::renderHtmlAttributes($strFormAttributeArray) . ">\n";

//        if (!self::$blnStylesRendered) {
//            $strToReturn .= $this->renderStyles(false, false);
//        }

        // Perhaps a strFormModifiers as an array to
        // allow controls to update other parts of the form, like enctype, onsubmit, etc.

        // Return or Display
        if ($blnDisplayOutput) {
            if (Application::instance()->context()->requestMode() != Context::REQUEST_MODE_CLI) {
                print($strToReturn);
            }
            return null;
        } else {
            if (Application::instance()->context()->requestMode() != Context::REQUEST_MODE_CLI) {
                return $strToReturn;
            } else {
                return '';
            }
        }
    }

    /**
     * Internal helper function used by RenderBegin and by RenderAjax
     * Given a comma-delimited list of JavaScript files, this will return an array of files that NEED to still
     * be included because (1) it hasn't yet been included and (2) it hasn't been specified to be "ignored".
     *
     * This UPDATES the internal $strIncludedJavaScriptFileArray array.
     *
     * @param array|string|null $strJavaScriptFileList
     * @return array|null array of script files to include or NULL if none
     */
    protected function processJavaScriptList(array|string|null $strJavaScriptFileList): ?array
    {
        if (empty($strJavaScriptFileList)) {
            return null;
        }

        $strArrayToReturn = array();

        if (!is_array($strJavaScriptFileList)) {
            $strJavaScriptFileList = explode(',', $strJavaScriptFileList);
        }

        // Iterate through the list of JavaScriptFiles to Include...
        foreach ($strJavaScriptFileList as $strScript) {
            if ($strScript = trim($strScript) ?? '') {

                // Include it if we're NOT ignoring it and it has NOT yet been included
                if ((!in_array($strScript, $this->strIgnoreJavaScriptFileArray)) &&
                    !array_key_exists($strScript, $this->strIncludedJavaScriptFileArray)
                ) {
                    $strArrayToReturn[$strScript] = $strScript;
                    $this->strIncludedJavaScriptFileArray[$strScript] = true;
                }
            }
        }

        if (count($strArrayToReturn)) {
            return $strArrayToReturn;
        }

        return null;
    }

    /**
     * Primarily used by RenderBegin and by RenderAjax
     * Given a comma-delimited list of stylesheet files, this will return an array of a file that NEED to still
     * be included because (1) it hasn't yet been included and (2) it hasn't been specified to be "ignored".
     *
     * This UPDATES the internal $strIncludedStyleSheetFileArray array.
     *
     * @param array|string|null $strStyleSheetFileList
     * @return array|null array of stylesheet files to include or NULL if none
     */
    protected function processStyleSheetList(array|string|null $strStyleSheetFileList): ?array
    {
        $strArrayToReturn = array();

        // Is there a comma-delimited list of StyleSheet files to include?
        if ($strStyleSheetFileList) {
            $strScriptArray = explode(',', $strStyleSheetFileList);

            // Iterate through the list of StyleSheetFiles to Include...
            foreach ($strScriptArray as $strScript) {
                if ($strScript = trim($strScript) ?? '') // Include it if we're NOT ignoring it and it has NOT yet been included
                {
                    if ((!in_array($strScript, $this->strIgnoreStyleSheetFileArray)) &&
                        !array_key_exists($strScript, $this->strIncludedStyleSheetFileArray)
                    ) {
                        $strArrayToReturn[$strScript] = $strScript;
                        $this->strIncludedStyleSheetFileArray[$strScript] = true;
                    }
                }
            }
        }

        if (count($strArrayToReturn)) {
            return $strArrayToReturn;
        }

        return null;
    }

    /**
     * Returns whether or not this Form is being run due to a PostBack event (e.g., a ServerAction or AjaxAction)
     * @return bool
     */
    public function isPostBack(): bool
    {
        $requestMode = Application::instance()->context()->requestMode();
        return ($requestMode == Context::REQUEST_MODE_QCUBED_SERVER || $requestMode == Context::REQUEST_MODE_QCUBED_AJAX);
    }

    /**
     * Will return an array of Strings which will show all the error and warning messages
     * in all the controls in the form.
     *
     * @param bool $blnErrorsOnly Show only the errors (otherwise, show both warnings and errors)
     * @return string[] an array of strings representing the (multiple) errors and warnings
     */
    public function getErrorMessages(?bool $blnErrorsOnly = false): array
    {
        $strToReturn = array();
        foreach ($this->getAllControls() as $objControl) {
            if ($objControl->ValidationError) {
                $strToReturn[] = $objControl->ValidationError;
            }
            if (!$blnErrorsOnly) {
                if ($objControl->Warning) {
                    $strToReturn[] = $objControl->Warning;
                }
            }
        }

        return $strToReturn;
    }

    /**
     * Will return an array of Controls from the form which have either an error or warning message.
     *
     * @param bool $blnErrorsOnly Return controls that have just errors (otherwise, show both warnings and errors)
     * @return ControlBase[] an array of controls representing the (multiple) errors and warnings
     */
    public function getErrorControls(?bool $blnErrorsOnly = false): array
    {
        $objToReturn = array();
        foreach ($this->getAllControls() as $objControl) {
            if ($objControl->ValidationError) {
                $objToReturn[] = $objControl;
            } else {
                if (!$blnErrorsOnly) {
                    if ($objControl->Warning) {
                        $objToReturn[] = $objControl;
                    }
                }
            }
        }

        return $objToReturn;
    }

    /**
     * Gets the JS file URI, given a string input
     * @param string $strFile File name to be tested
     *
     * @return string the final JS file URI
     * @throws Caller
     */
    public function getJsFileUri(string $strFile): string
    {
        return Application::getJsFileUri($strFile);
    }

    /**
     * Gets the CSS file URI, given a string input
     * @param string $strFile File name to be tested
     *
     * @return string the final CSS URI
     * @throws Caller
     */
    public function getCssFileUri(string $strFile): string
    {
        return Application::getCssFileUri($strFile);
    }

    /**
     * Get high-level form JavaScript files to be included. Default here includes all
     * JavaScripts needed to run qcubed.
     * Override and add to this list and include
     * JavaScript and jQuery files and libraries needed for your application.
     * JavaScript files included before QCUBED_JS can refer to jQuery as $.
     * After qcubed.js, $ becomes $j, so add other libraries that need
     * $ in a different context after qcubed.js, and insert jQuery libraries and plugins that
     * refer to $ before qcubed.js file.
     *
     * @return array
     */
    protected function getFormJavaScripts(): array
    {
        return array(
            QCUBED_JQUERY_JS,
            QCUBED_JQUI_JS,
            QCUBED_JS_URL . '/ajaxq/ajaxq.js',
            QCUBED_JS
        );
    }

    /**
     * Returns an array of URLS that point to the style sheets required by every
     * form in the application. Override and add your own style sheets if needed.
     *
     * @return array
     */
    protected function getFormStyles(): array
    {
        return array(
            QCUBED_CSS
        );
    }

    /**
     * Renders the end of the form, including the closing form and body tags.
     * Renders the HTML for hidden controls.
     * @param bool $blnDisplayOutput should the output be returned or directly printed to the screen?
     *
     * @return null|string
     * @throws Caller|RandomException
     */
    public function renderEnd(bool $blnDisplayOutput = true): ?string
    {
        // Ensure that RenderEnd() has not yet been called
        switch ($this->intFormStatus) {
            case self::FORM_STATUS_UNRENDERED:
                throw new Caller('$this->renderBegin() was never called');
            case self::FORM_STATUS_RENDER_BEGUN:
                break;
            case self::FORM_STATUS_RENDER_ENDED:
                throw new Caller('$this->renderEnd() has already been called');

            default:
                throw new Caller('FormStatus is in an unknown status');
        }

        $strHtml = '';    // This will be the final output

        /**** Render any controls that get automatically rendered ****/
        foreach ($this->getAllControls() as $objControl) {
            if ($objControl->AutoRender &&
                !$objControl->Rendered
            ) {
                $strRenderMethod = $objControl->PreferredRenderMethod;
                $strHtml .= $objControl->$strRenderMethod(false) . _nl();
            }
        }

        /**** Prepare JavaScripts ****/

        // Clearly included JavaScript array since we are completely redrawing the page
        $this->strIncludedJavaScriptFileArray = array();
        $strControlIdToRegister = array();
        $strEventScripts = '';

        // Add form level JavaScripts and libraries
        $strJavaScriptArray = $this->processJavaScriptList($this->getFormJavaScripts());
        Application::addJavaScriptFiles($strJavaScriptArray);
        $strFormJsFiles = Application::renderFiles();    // Render the form-level JavaScript files separately

        // Go through all controls and gather up any JS or CSS to run or Form Attributes to modify
        foreach ($this->getAllControls() as $objControl) {
            if ($objControl->Rendered || $objControl->ScriptsOnly) {
                $strControlIdToRegister[] = $objControl->ControlId;

                /* Note: GetEndScript may cause the control to register additional commands or even add JavaScripts, so those should be handled after this. */
                if ($strControlScript = $objControl->getEndScript()) {
                    $strControlScript = Q\Js\Helper::terminateScript($strControlScript);

                    Application::executeJavaScript($strControlScript);;

                    // Add comments for a developer version of output
                    if (!Application::instance()->minimize()) {
                        // Render a comment
                        $strControlScript = _nl() . _nl() .
                            sprintf('/*** EndScript -- Control Type: %s, Control Name: %s, Control Id: %s  ***/',
                                get_class($objControl), $objControl->Name, $objControl->ControlId) .
                            _nl() .
                            _indent($strControlScript);
                    }
                    $strEventScripts .= $strControlScript;
                }
            }

            // Include the JavaScripts specified by each control.
            if ($strScriptArray = $this->processJavaScriptList($objControl->JavaScripts)) {
                Application::addJavaScriptFiles($strScriptArray);
            }

            // Include any StyleSheets?  The control would have a
            // comma-delimited list of stylesheet files to include (if applicable)
            if ($strScriptArray = $this->processStyleSheetList($objControl->StyleSheets)) {
                Application::addStyleSheets(array_keys($strScriptArray));
            }
        }

        /*** Build the JavaScript block ****/

        // Start with variable settings and initForm
        $strEndScript = sprintf('qc.initForm("%s"); ', $this->strFormId) . _nl();

        // Register controls
        if ($strControlIdToRegister) {
            $strEndScript .= sprintf("qc.regCA(%s); \n", Q\Js\Helper::toJsObject($strControlIdToRegister)) . _nl();
        }

        // Design mode event
        if (defined('QCUBED_DESIGN_MODE') && QCUBED_DESIGN_MODE == 1) {
            // attach an event listener to the form to send context menu selections to the designer dialog for processing
            $strEndScript .= sprintf(
                '$j("#%s").on("contextmenu", "[id]", 
                    function(event) {
                        $j("#qconnectoreditdlg").trigger("qdesignerclick", 
                            [{id: event.target.id ? event.target.id : $j(event.target).parents("[id]").attr("id"), for: $j(event.target).attr("for")}]
                        );
                        return false;
                    }
                );', $this->FormId);
        }

        // Add any application level JS commands.
        // This will include high and critical level commands
        $strEndScript .= Application::renderJavascript(true);

        // Add the JavaScript coming from controls and events just after the medium level commands
        //$strEndScript .= ';' . $strEventScripts;

        // Add low-level commands and other things that need to execute at the end
        $strEndScript .= /*';' .*/ Application::renderJavascript(false);

        // Create Final EndScript Script
        $strEndScript = sprintf('<script type="text/javascript">jQuery(document).ready(function($j) { %s; });</script>', $strEndScript);

        /**** Render the HTML itself, appending the JavaScript we generated above ****/

        foreach ($this->getAllControls() as $objControl) {
            if ($objControl->Rendered) {
                $strHtml .= $objControl->getEndHtml();
            }
            $objControl->resetFlags(); // Make sure controls are serialized in a reset state
        }

        $strHtml .= $strFormJsFiles . _nl();    // Add form level JavaScript files

        // put JavaScript environment defines up early for use by other JS files.
        $strHtml .= '<script type="text/javascript">' .
            sprintf('qc.baseDir = "%s"; ', QCUBED_BASE_URL) .
            sprintf('qc.jsVendor = "%s"; ', QCUBED_VENDOR_URL) .
            sprintf('qc.jsAssets = "%s"; ', QCUBED_JS_URL) .
            sprintf('qc.phpAssets = "%s"; ', QCUBED_PHP_URL) .
            sprintf('qc.cssAssets = "%s"; ', QCUBED_CSS_URL) .
            sprintf('qc.imageAssets = "%s"; ', QCUBED_IMAGE_URL) .
            '</script>' .
            _nl();

        $strHtml .= Application::renderFiles() . _nl();    // add a plugin and control JS files

        // Render hidden controls related to the form
        $strHtml .= sprintf('<input type="hidden" name="Qform__FormId" id="Qform__FormId" value="%s" />',
                $this->strFormId) . _nl();
        $strHtml .= '<input type="hidden" name="Qform__FormControl" id="Qform__FormControl" value="" />' . _nl();
        $strHtml .= '<input type="hidden" name="Qform__FormEvent" id="Qform__FormEvent" value="" />' . _nl();
        $strHtml .= '<input type="hidden" name="Qform__FormParameter" id="Qform__FormParameter" value="" />' . _nl();
        $strHtml .= Html::renderTag('input',
            ['type' => 'hidden', 'name' => self::POST_CALL_TYPE, 'id' => self::POST_CALL_TYPE, 'value' => ''], null,
            true) . _nl();
        $strHtml .= '<input type="hidden" name="Qform__FormUpdates" id="Qform__FormUpdates" value="" />' . _nl();
        $strHtml .= '<input type="hidden" name="Qform__FormCheckableControls" id="Qform__FormCheckableControls" value="" />' . _nl();

        // Serialize and write out the formstate
        $strHtml .= sprintf('<input type="hidden" name="' . self::POST_FORM_STATE . '" id="Qform__FormState" value="%s" />',
                QForm::serialize(clone($this))) . _nl();

        $GLOBALS['_csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $GLOBALS['_csrf_token']; // Ajax's new tokens 

        if (!empty($GLOBALS['_csrf_token'])) {
            $strHtml .= sprintf('<input type="hidden" name="Qform__FormCsrfToken" id="Qform__FormCsrfToken" value="%s" />',
                    $GLOBALS['_csrf_token']) . _nl();
        }

        // close the form tag
        $strHtml .= "</form>" . _nl();

        // Add the JavaScripts rendered above
        $strHtml .= $strEndScript;

        // close the body tag
        if ($this->blnRenderedBodyTag) {
            $strHtml .= '</body>';
        }

        /**** Cleanup ****/

        // Update Form Status
        $this->intFormStatus = self::FORM_STATUS_RENDER_ENDED;

        // Display or Return
        if ($blnDisplayOutput) {

            if (Application::instance()->context()->requestMode() != Context::REQUEST_MODE_CLI) {
                print($strHtml);
            }

            return null;

        } else {
            if (Application::instance()->context()->requestMode() != Context::REQUEST_MODE_CLI) {
                return $strHtml;
            } else {
                return '';
            }
        }
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * PHP magic method for getting property values of an object
     *
     * @param string $strName
     * @return mixed
     * @throws Caller
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case "FormId":
                return $this->strFormId;
            case "CallType":
                throw new Caller('CallType is deprecated. Use Application::isAjax() or Application::instance()->context()->requestMode()');
            case "DefaultWaitIcon":
                return $this->objDefaultWaitIcon;
            case "FormStatus":
                return $this->intFormStatus;
            case "HtmlIncludeFilePath":
                return $this->strHtmlIncludeFilePath;
            case "CssClass":
                return $this->strCssClass;

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
     * PHP magic function to set the value of properties of a class object
     * @param string $strName Name of the property
     * @param mixed $mixValue Value of the property
     *
     * @return void
     * @throws Caller
     * @throws Exception
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case "HtmlIncludeFilePath":
                // Passed-in value is null -- use the "default" path name of a file".tpl.php"
                if (!$mixValue) {
                    $strPath = realpath(substr(Application::instance()->context()->scriptFileName(), 0,
                            strrpos(Application::instance()->context()->scriptFileName(), '.php')) . '.tpl.php');
                    if ($strPath === false) {
                        // Look again based on the object name
                        $strPath = realpath(get_class($this) . '.tpl.php');
                    }
                } // Use passed-in value
                else {
                    $strPath = realpath($mixValue);
                }

                // Verify File Exists, and if not, throw exception
                if (is_file($strPath)) {
                    $this->strHtmlIncludeFilePath = $strPath;
                } else {
                    throw new Caller('Accompanying HTML Include File does not exist: "' . $mixValue . '"');
                }
                break;

            case "CssClass":
                try {
                    $this->strCssClass = Type::cast($mixValue, Type::STRING);
                } catch (Caller $objExc) {
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
        }
    }

    /**
     * A helper function for buffering templates
     *
     * @param string $strBuffer
     * @return string
     */
    public static function EvaluateTemplate_ObHandler(string $strBuffer): string
    {
        return $strBuffer;
    }

}
