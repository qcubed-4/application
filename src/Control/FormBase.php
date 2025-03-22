<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed as Q;
use QCubed\Context;
use QCubed\Exception\Caller;
use QCubed\Exception\DataBind;
use QCubed\Html;
use QCubed\ObjectBase;
use QCubed\Project\Control\FormBase as QForm;
use QCubed\Project\Watcher\Watcher;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\Action\ActionBase as QAction;


/**
 * Class FormBase
 *
 * The FormBase is central to the application framework. You can think of it as the control the prints and manages
 * the html FORM tag on the page. Sine all other controls are contained by the form, the form is responsible for saving
 * and restoring its own state and the state of all the controls on the page. This is what allows QCubed to present all the
 * controls on the clients webpage as if they were PHP objects in your application, so that you don't have to worry about
 * JavaScript state, or HTML Gets and Puts.
 *
 * Doing all of this is fairly complex, and involves the use of JavaScript in the qcubed.js page, and in various control plugins.
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
 * @was QFormBase
 */
abstract class FormBase extends ObjectBase
{
    ///////////////////////////
    // Form Status Constants
    ///////////////////////////
    /** Form has not started rendering */
    const FORM_STATUS_UNRENDERED = 1;
    /** Form has started rendering but has not finished */
    const FORM_STATUS_RENDER_BEGUN = 2;
    /** Form rendering has already been started and finished */
    const FORM_STATUS_RENDER_ENDED = 3;

    // Keys for hidden fields that we use to communicate with qcubed.js
    const POST_CALL_TYPE = 'Qform__FormCallType';   // Do not use this to detect ajax. Use Application::isAjax() instead
    const POST_FORM_STATE = 'Qform__FormState';

    ///////////////////////////
    // Static Members
    ///////////////////////////
    /** @var bool True when css scripts get rendered on page. Lets user call RenderStyles in header. */
    protected static $blnStylesRendered = false;

    ///////////////////////////
    // Protected Member Variables
    ///////////////////////////
    /** @var string Form ID (usually passed as the first argument to the 'Run' method call) */
    protected $strFormId;
    /** @var integer representational integer value of what state the form currently is in */
    protected $intFormStatus;
    /** @var ControlBase[] Array of Controls with this form as the parent */
    protected $objControlArray;
    /**
     * @var QControlGrouping List of Groupings in the form (for old drag and drop)
     * Use of this is deprecated in favor of jQueryUI drag and drop, but code remains in case we need it again
     * since we might pull out JQueryUi, in which case we might implement HTML5 drag and drop.
     * @deprecated
     */
    protected $objGroupingArray;
    /** @var bool Has the body tag already been rendered? */
    protected $blnRenderedBodyTag = false;
    protected $checkableControlValues = array();
    /** @var string The type of call made to the QForm (Ajax, Server or Fresh GET request) */
    //protected $strCallType;   Use Application::isAjax() or Application::instance->context->requestMode() instead
    /** @var null|WaitIcon Default wait icon for the page/QForm */
    protected $objDefaultWaitIcon = null;

    /** @var array List of included JavaScript files for this QForm */
    protected $strIncludedJavaScriptFileArray = array();
    /** @var array List of ignored JavaScript files for this QForm */
    protected $strIgnoreJavaScriptFileArray = array();

    /** @var array List of included CSS files for this QForm */
    protected $strIncludedStyleSheetFileArray = array();
    /** @var array List of ignored CSS files for this QForm */
    protected $strIgnoreStyleSheetFileArray = array();

    protected $strPreviousRequestMode = false;
    /**
     * @var string The QForm's template file path.
     * When this value is not supplied, the 'Run' function will try to find and use the
     * .tpl.php file with the same filename as the QForm in the same same directory as the QForm file.
     */
    protected $strHtmlIncludeFilePath;
    /** @var string CSS class to be set for the 'form' tag when QCubed Renders the QForm */
    protected $strCssClass;

    protected $strCustomAttributeArray = null;


    /**
     * @var null|string The key to encrypt the formstate
     *              when saving and retrieving from the chosen FormState handler
     */
    public static $EncryptionKey = null;
    /**
     * @var string Chosen FormStateHandler
     *              default is QFormStateHandler as shown here,
     *              however it is read from the configuration.inc.php (in the QForm class)
     *              In case something goes wrong with QForm, the default FormStateHandler here will
     *              try to take care of the situation.
     */
    public static $FormStateHandler = 'QFormStateHandler';


    /**
     * Generates Control ID used to keep track of those Controls whose ID was not explicitly set.
     * It uses the counter variable to maintain uniqueness for Control IDs during the life of the page
     * Life of the page is untill the time when the formstate expired and is removed by the
     * garbage collection of the formstate handler
     * @return string the Ajax Action ID
     */
    public function generateControlId()
    {
//			$strToReturn = sprintf('control%s', $this->intNextControlId);
        $strToReturn = sprintf('c%s', $this->intNextControlId);
        $this->intNextControlId++;
        return $strToReturn;
    }

    /**
     * @var int Counter variable to contain the numerical part of the Control ID value.
     *      it is automatically incremented everytime the GenerateControlId() runs
     */
    protected $intNextControlId = 1;

    /////////////////////////
    // Helpers for AjaxActionId Generation
    /////////////////////////
    /**
     * Generates Ajax Action ID used to keep track of Ajax Actions
     * It uses the counter variable to maintain uniqueness for Ajax Action IDs during the life of the page
     * Life of the page is untill the time when the formstate expired and is removed by the
     * garbage collection of the formstate handler
     * @return string the Ajax Action ID
     */
    public function generateAjaxActionId()
    {
        $strToReturn = sprintf('a%s', $this->intNextAjaxActionId);
        $this->intNextAjaxActionId++;
        return $strToReturn;
    }

    /**
     * @var int Counter variable to contain the numerical part of the AJAX ID value.
     *      it is automatically incremented everytime the GenerateAjaxActionId() runs
     */
    protected $intNextAjaxActionId = 1;

    /////////////////////////
    // Event Handlers
    /////////////////////////
    /**
     * Custom Form Run code.
     * To contain code which should be run 'AFTER' QCubed's QForm run has been completed
     * but 'BEFORE' the custom event handlers are called
     * (In case it is to be used, it should be overriden by a child class)
     */
    protected function formRun()
    {
    }

    /**
     * To contain the code which should be executed after the Form Run and
     * before the custom handlers are called (In case it is to be used, it should be overridden by a child class)
     * In this situation, we are about to process an event, or the user has reloaded the page. Do whatever you
     * need to do before any event processing.
     */
    protected function formLoad()
    {
    }

    /**
     * To contain the code to initialize the QForm on the first call.
     * Once the QForm is created, the state is saved and is reused by the Run method.
     * In short - this function will run only once (the first time the QForm is to be created)
     * (In case it is to be used, it should be overriden by a child class)
     */
    protected function formCreate()
    {
    }

    /**
     * To contain the code to be executed after formRun, formCreate, formLoad has been called
     * and the custom defined event handlers have been executed but actual rendering process has not begun.
     * This is a good place to put data into a session variable that you need to send to
     * other forms.
     */
    protected function formPreRender()
    {
    }

    /**
     * Override this method to set data in your form controls. Appropriate things to do would be to:
     * - Respond to options sent by _GET or _POST variables.
     * - Load data into the control from the database
     * - Initialize controls whose data depends on the state or data in other controls.
     *
     * When this is called, the controls will have been created by formCreate, and will have already read their saved state.
     *
     */
    protected function formInitialize()
    {
    }

    /**
     * The formValidate method.
     *
     * Before we get here, all the controls will first be validated. Override this method to do
     * additional form level validation, and any form level actions needed as part of the validation process,
     * like displaying an error message.
     *
     * This is the last thing called in the validation process, and will always be called if
     * validation is requested, even if prior controls caused a validation error. Return false to prevent
     * validation and cancel the current action.
     *
     * $blnValid will contain the result of control validation. If it is false, you know that validation will
     * fail, regardless of what you return from the function.
     *
     * @return bool    Return false to prevent validation.
     */
    protected function formValidate()
    {
        return true;
    }

    /**
     * If you want to respond in some way to an invalid form that you have not already been able to handle,
     * override this function. For example, you could display a message that an error occurred with some of the
     * controls.
     */
    protected function formInvalid()
    {
    }

    /**
     * This function is meant to be overriden by child class and is called when the Form exits
     * (After the form render is complete and just before the Run function completes execution)
     */
    protected function formExit()
    {
    }


    /**
     * VarExport the Controls or var_export the current QForm
     * (well, be ready for huge amount of text)
     * @param bool $blnReturn
     *
     * @return mixed
     */
    public function varExport($blnReturn = true)
    {
        if ($this->objControlArray) {
            foreach ($this->objControlArray as $objControl) {
                $objControl->varExport(true);  // force the controls to be prepared to serialize
            }
        }
         return var_export($this, $blnReturn);
    }

    /**
     * Returns the value of a checkable control. Checkable controls are special, in that the browser only tells us
     * when a control is checked, not when it is unchecked. So, unless we keep track of them specially, we will
     * not know if they are unchecked, or just not there.
     * @param $strControlId
     * @return mixed|null
     */
    public function checkableControlValue($strControlId)
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
    protected static function isControlModified(ControlBase $objControl)
    {
        return $objControl->isModified();
    }

    /**
     * Return only the controls that have been modified
     */
    public function getModifiedControls()
    {
        $ret = array_filter($this->objControlArray, 'QForm::IsControlModified');
        return $ret;
    }

    /**
     * This method initializes the actual layout of the form
     * It runs in all cases including initial form (the time when formCreate is run) as well as on
     * trigger actions (QServerAction, QAjaxAction, QServerControlAction and QAjaxControlAction)
     *
     * It is responsible for implementing the logic and sequence in which page wide checks are done
     * such as running formValidate and Control validations for every control of the page and their
     * child controls. Checking for an existing FormState and loading them before trigerring any action
     * is also a responsibility of this method.
     * @param string $strFormClass The class of the form to create when creating a new form.
     * @param string|null $strAlternateHtmlFile location of the alternate HTML template file.
     * @param string|null $strFormId The html id to use for the form. If null, $strFormClass will be used.
     *
     * @throws Caller
     * @throws \Exception
     */
    public static function run($strFormClass, $strAlternateHtmlFile = null, $strFormId = null)
    {
        // See if we can get a Form Class out of PostData

        /** @var \QCubed\Project\Control\FormBase $objClass */
        $objClass = null;
        if ($strFormId === null) {
            $strFormId = $strFormClass;
        }
        if (array_key_exists('Qform__FormId',
                $_POST) && ($_POST['Qform__FormId'] == $strFormId) && array_key_exists('Qform__FormState', $_POST)
        ) {
            $strPostDataState = $_POST['Qform__FormState'];

            if ($strPostDataState) // We might have a valid form state -- let's see by unserializing this object
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

            // Cleanup ajax post data if the encoding does not match, since ajax data is always utf-8
            if (Application::isAjax() && Application::encodingType() != 'UTF-8') {
                foreach ($_POST as $key => $val) {
                    if (substr($key, 0, 6) != 'Qform_') {
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

            // This is original code. In an effort to minimize changes,
            // we aren't going to touch the server calls for now
            if (!Application::isAjax()) {
                foreach ($objClass->objControlArray as $objControl) {
                    // If they were rendered last time and are visible
                    // (and if ServerAction, enabled), then Parse its post data
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
                    } elseif (substr($key, 0, 6) == 'Qform_') {
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

            // Trigger a triggered control's Server- or Ajax- action (e.g. PHP method) here (if applicable)
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
            $objClass->objGroupingArray = array();

            // Trigger Run Event (if applicable)
            $objClass->formRun();

            // Trigger Create Event (if applicable)
            $objClass->formCreate();

            $objClass->formInitialize();

            if (defined('QCUBED_DESIGN_MODE') && QCUBED_DESIGN_MODE == 1) {
                // Attach custom event to dialog to handle right click menu items sent by form

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
                    throw new Caller('$this->renderBegin() is never called in the HTML Include file');
                case self::FORM_STATUS_RENDER_BEGUN:
                    throw new Caller('$this->renderEnd() is never called in the HTML Include file');
                case self::FORM_STATUS_RENDER_ENDED:
                    break;
                default:
                    throw new Caller('FormStatus is in an unknown status');
            }
        } else {
            throw new \Exception('Cannot process request mode: ' . $requestMode);
        }

        // Once all the controls have been set up, and initialized, remember them.
        $objClass->saveControlState();

        // Trigger Exit Event (if applicable)
        $objClass->formExit();
    }

    /**
     * Unpacks a post variable that has been encoded with JSON.stringify.
     *
     * @param $val
     * @return mixed|string
     */
    protected static function unpackPostVar($val)
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
                throw new \Exception ('Unknown Post Var Type');
            }
        }
        return $val;
    }

    /**
     * Reset all validation states.
     */
    public function resetValidationStates()
    {
        foreach ($this->objControlArray as $objControl) {
            $objControl->validationReset();
        }
    }

    /**
     * Private function to respond to a designer click.
     *
     * @param $strFormId
     * @param $strControlId
     * @param $mixParam
     */
    private function ctlDesigner_Click($strFormId, $strControlId, $mixParam)
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
     * a variety of reasons, and is dependent on the formstate handler. Most likely, the user hit
     * the back button past the back button limit of what we remember, or the user lost the session.
     * Or, you simply have not set up the form state handler correctly.
     * In the past, we threw an exception, but that was not a very user friendly response.
     * The response below resubmits the url without a formstate so that a new one will be created.
     * Override if you want a different response.
     */
    public static function invalidFormState()
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
     * Calls a data binder associated with the form. Does this so data binder can be protected. Mostly for legacy code.
     * @param callable $callable
     * @param  ControlBase $objPaginatedControl
     * @throws DataBind
     */
    public function callDataBinder($callable, $objPaginatedControl)
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
     * @return string The Ajax helper string (should be JS commands)
     */
    protected function renderAjaxHelper($objControl)
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
     * Renders the actual ajax return value as a json object. Since json must be UTF-8 encoded, will convert to
     * UTF-8 if needed. Response is parsed in the "success" function in qcubed.js, and handled there.
     */
    protected function renderAjax()
    {
        $aResponse = array();

        if (Application::instance()->jsResponse()->hasExclusiveCommand()) {
            /**
             * Processing of the actions has resulted in a very high priority exclusive response. This would typically
             * happen when a javascript widget is requesting data from us. We want to respond as quickly as possible,
             * and also prevent possibly redrawing the widget while its already in the middle of its own drawing.
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
            // Include any javascript files that were added by the control
            // Note: current implementation does not handle removal of javascript files
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
                // Note that current implementation does not handle removing attributes that are no longer needed
                // if such a control gets removed from a form during an ajax call, but that is a very unlikely scenario.
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
                    Application::PRIORITY_HIGH);    // put these last in the high priority queue, just before getting the commands below
            }
            $objControl->resetFlags();
        }

        if ($strControlIdToRegister) {
            $aResponse[Q\JsResponse::REG_C] = $strControlIdToRegister;
        }


        foreach ($this->objGroupingArray as $objGrouping) {
            $strRender = $objGrouping->render();
            if (trim($strRender) ?? '') {
                Application::executeJavaScript($strRender, Application::PRIORITY_HIGH);
            }
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
     * Saves the formstate using the 'Save' method of FormStateHandler set in configuration.inc.php
     * @param FormBase $objForm
     *
     * @return string the Serialized QForm
     */
    public static function serialize(FormBase $objForm)
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
        $strPostDataState = $strFormStateHandler::save($strSerializedForm, $blnBackButtonFlag);

        // Return the PostDataState
        return $strPostDataState;
    }

    /**
     * Unserializes (extracts) the FormState using the 'Load' method of FormStateHandler set in configuration.inc.php
     * @param string $strPostDataState The string identifying the FormState to the loaded for Unserialization
     *
     * @internal param string $strSerializedForm
     * @return QForm the Form object
     */
    public static function unserialize($strPostDataState)
    {
        // Setup and Call the FormStateHandler to retrieve the Serialized Form
        $strFormStateHandler = QForm::$FormStateHandler;
        $strSerializedForm = $strFormStateHandler::load($strPostDataState);

        if ($strSerializedForm) {
            // Unserialize and Cast the Form
            // For the QSessionFormStateHandler the __PHP_Incomplete_Class occurs sometimes
            // for the result of the unserialize call.
            /** @var \QCubed\Project\Control\FormBase $objForm */
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
    public function addControl(ControlBase $objControl)
    {
        $strControlId = $objControl->ControlId;
        $objControl->markAsModified(); // make sure new controls get drawn
        if (array_key_exists($strControlId, $this->objControlArray)) {
            throw new Caller(sprintf('A control already exists in the form with the ID: %s', $strControlId));
        }
        if (array_key_exists($strControlId, $this->objGroupingArray)) {
            throw new Caller(sprintf('A Grouping already exists in the form with the ID: %s', $strControlId));
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
    public function getControl($strControlId)
    {
        if (isset($this->objControlArray[$strControlId])) {
            return $this->objControlArray[$strControlId];
        } else {
            return null;
        }
    }

    /**
     * Removes a Control (and its children) from the current QForm
     * @param string $strControlId
     */
    public function removeControl($strControlId)
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
                // if the parent is the form, then remove it from the dom through javascript, since the form won't be redrawn
                Application::executeSelectorFunction('#' . $objControl->getWrapperId(), 'remove');
            }

            // Remove this control
            unset($this->objControlArray[$strControlId]);

            // Remove this control from any groups
            foreach ($this->objGroupingArray as $strKey => $objGrouping) {
                $this->objGroupingArray[$strKey]->removeControl($strControlId);
            }
        }
    }

    /**
     * Returns all controls belonging to the Form as an array.
     * @return mixed|ControlBase[]
     */
    public function getAllControls()
    {
        return $this->objControlArray;
    }

    /**
     * Tell all the controls to save their state.
     */
    public function saveControlState()
    {
        // tell the controls to save their state
        $a = $this->getAllControls();
        foreach ($a as $control) {
            $control->_WriteState();
        }
    }

    /**
     * Tell all the controls to read their state.
     */
    protected function restoreControlState()
    {
        // tell the controls to restore their state
        $a = $this->getAllControls();
        foreach ($a as $control) {
            $control->_ReadState();
        }
    }


    /**
     * Custom Attributes are other html name-value pairs that can be rendered within the form using this method.
     * For example, you can now render the autocomplete tag on the QForm
     * additional javascript actions, etc.
     *        $this->setCustomAttribute("autocomplete", "off");
     * Will render:
     *        [form ...... autocomplete="off"] (replace sqare brackets with angle brackets)
     * @param string $strName Name of the attribute
     * @param string $strValue Value of the attribute
     *
     * @throws Caller
     */
    public function setCustomAttribute($strName, $strValue)
    {
        if ($strName == "method" || $strName == "action") {
            throw new Caller(sprintf("Custom Attribute not supported through SetCustomAttribute(): %s",
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
    public function getCustomAttribute($strName)
    {
        if ((is_array($this->strCustomAttributeArray)) && (array_key_exists($strName,
                $this->strCustomAttributeArray))
        ) {
            return $this->strCustomAttributeArray[$strName];
        } else {
            throw new Caller(sprintf("Custom Attribute does not exist in Form: %s", $strName));
        }
    }

    public function removeCustomAttribute($strName)
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

    /*
        public function addGrouping(ControlGrouping $objGrouping)
        {
            $strGroupingId = $objGrouping->GroupingId;
            if (array_key_exists($strGroupingId, $this->objGroupingArray)) {
                throw new Caller(sprintf('A Grouping already exists in the form with the ID: %s',
                    $strGroupingId));
            }
            if (array_key_exists($strGroupingId, $this->objControlArray)) {
                throw new Caller(sprintf('A Control already exists in the form with the ID: %s', $strGroupingId));
            }
            $this->objGroupingArray[$strGroupingId] = $objGrouping;
        }
    
        public function getGrouping($strGroupingId)
        {
            if (array_key_exists($strGroupingId, $this->objGroupingArray)) {
                return $this->objGroupingArray[$strGroupingId];
            } else {
                return null;
            }
        }
    
        public function removeGrouping($strGroupingId)
        {
            if (array_key_exists($strGroupingId, $this->objGroupingArray)) {
                // Remove this Grouping
                unset($this->objGroupingArray[$strGroupingId]);
            }
        }
    */
    /**
     * Retruns the Groupings
     * @return mixed
     */
    public function getAllGroupings()
    {
        return $this->objGroupingArray;
    }

    /**
     * Returns the child controls of the current Form or a Control object
     *
     * @param FormBase|ControlBase $objParentObject The object whose child controls are to be searched for
     *
     * @throws Caller
     * @return ControlBase[]
     */
    public function getChildControls($objParentObject)
    {
        $objControlArrayToReturn = array();

        if ($objParentObject instanceof QForm) {
            // They want all the ChildControls for this Form
            // Basically, return all objControlArray Controls where the Qcontrol's parent is NULL
            foreach ($this->objControlArray as $objChildControl) {
                if (!($objChildControl->ParentControl)) {
                    array_push($objControlArrayToReturn, $objChildControl);
                }
            }
            return $objControlArrayToReturn;

        } else {
            if ($objParentObject instanceof ControlBase) {
                return $objParentObject->getChildControls();
                // THey want all the ChildControls for a specific Control
                // Basically, return all objControlArray Controls where the Qcontrol's parent is the passed in parentobject
                /*				$strControlId = $objParentObject->ControlId;
                                foreach ($this->objControlArray as $objChildControl) {
                                    $objParentControl = $objChildControl->ParentControl;
                                    if (($objParentControl) && ($objParentControl->ControlId == $strControlId)) {
                                        array_push($objControlArrayToReturn, $objChildControl);
                                    }
                                }*/

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
     * @return string The evaluated HTML string
     */
    public function evaluateTemplate($strTemplate)
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

            // Restore the output buffer and return evaluated template
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
     */
    protected function triggerMethod($strControlId, $strMethodName, QAction $objAction)
    {
        $mixParameter = $_POST['Qform__FormParameter'];
        $objMethodParam = new Q\Action\ActionParams($objAction, $this, $strControlId, $mixParameter);
        Q\Project\Control\ControlBase::_processActionParams($objMethodParam);

        if (strpos($strMethodName, '::')) {
            // Calling a static method in a class
            $f = explode('::', $strMethodName);
            if (is_callable($f)) {
                /**
                 * To transition to actions that just take a $params array and nothing else, we use reflection
                 */
                $ref = new \ReflectionClass($f[0]);
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
            Q\Project\Control\ControlBase::_callActionMethod($objDestControl, $strMethodName, $objMethodParam);
        } else {
            /**
             * To transition to actions that just take a $params array and nothing else, we use reflection
             */
            $ref = new \ReflectionClass(get_class($this));
            $argCount = $ref->getMethod($strMethodName)->getNumberOfParameters();

            if ($argCount > 1) {
                $this->$strMethodName($this->strFormId, $strControlId, $objMethodParam->Param, $objMethodParam);
            } else {
                $this->$strMethodName($objMethodParam);
            }
        }
    }

    /**
     * Calles 'Validate' method on a Control recursively
     * @param ControlBase $objControl
     *
     * @return bool
     */
    protected function validateControlAndChildren(ControlBase $objControl)
    {
        return $objControl->validateControlAndChildren();
    }

    protected function triggerActions($strControlIdOverride = null)
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

                // Does this Control which performed the action exist?
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
                            throw new \Exception('Unknown request mode: ' . Application::instance()->context()->requestMode());
                    }

                    // Validation Check
                    $blnValid = true;
                    $mixCausesValidation = null;

                    // Figure out what the CausesValidation directive is
                    // Set $mixCausesValidation to the default one (e.g. the one defined on the control)
                    $mixCausesValidation = $objActionControl->CausesValidation;

                    // Next, go through the linked ajax/server actions to see if a causesvalidation override is set on any of them
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

                    // Starting Point is a Control
                    if ($mixCausesValidation instanceof ControlBase) {
                        if (!$this->validateControlAndChildren($mixCausesValidation)) {
                            $blnValid = false;
                        }

                        // Starting Point is an Array of Controls
                    } else {
                        if (is_array($mixCausesValidation)) {
                            foreach (((array)$mixCausesValidation) as $objControlToValidate) {
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
                                        // If not ParentControl, tyhen the parent is the form itself
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
                                    } else {
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


                    // Go ahead and run the ServerActions or AjaxActions if Validation Passed and if there are Server/Ajax-Actions defined
                    if ($blnValid) {
                        if ($objActions) {
                            foreach ($objActions as $objAction) {
                                if ($strMethodName = $objAction->MethodName) {
                                    if (($strAjaxActionId == null)            //if this call was not an ajax call
                                        || ($objAction->Id == null)        // or the QAjaxAction derived action has no id set
                                        //(a possible way to add a callback that gets executed on every ajax call for this control)
                                        || ($strAjaxActionId == $objAction->Id)
                                    ) //or the ajax action id passed from client side equals the id of the current ajax action
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
                    throw new \Exception(sprintf('Control passed by Qform__FormControl does not exist: %s',
                        $strControlId));
                }
            }
            /* else {
                // TODO: Code to automatically execute any PrimaryButton's onclick action, if applicable
                // Difficult b/c of all the QCubed hidden parameters that need to be set to get the action to work properly
                // Javascript interaction of PrimaryButton works fine in Firefox... currently doens't work in IE 6.
            }*/
        }
    }

    /**
     * Begins rendering the page
     */
    protected function render()
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
     */
    protected function renderChildren($blnDisplayOutput = true)
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
     * This exists to prevent inadverant "New"
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
     * @return string|null The generated HTML string containing the `<link>` tags for stylesheets if
     *                     `$blnDisplayOutput` is false, or null if `$blnDisplayOutput` is true.
     */
    public function renderStyles($blnDisplayOutput = true)
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

            // Add newline after each file, including the last one
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
    public function renderBegin($blnDisplayOutput = true)
    {
        // Ensure that RenderBegin() has not yet been called
        switch ($this->intFormStatus) {
            case self::FORM_STATUS_UNRENDERED:
                break;
            case self::FORM_STATUS_RENDER_BEGUN:
            case self::FORM_STATUS_RENDER_ENDED:
                throw new Caller('$this->renderBegin() has already been called');
                break;
            default:
                throw new Caller('FormStatus is in an unknown status');
        }

        // Update FormStatus and Clear Included JS/CSS list
        $this->intFormStatus = self::FORM_STATUS_RENDER_BEGUN;

        // Prepare for rendering

        $blnProcessing = Application::setProcessOutput(false);
        $strOutputtedText = trim(ob_get_contents());
        if (strpos(strtolower($strOutputtedText), '<body') === false) {
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
                $strFormAttributeArray = array_merge($strFormAttributeArray, $attributes);
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
     * Given a comma-delimited list of javascript files, this will return an array of files that NEED to still
     * be included because (1) it hasn't yet been included and (2) it hasn't been specified to be "ignored".
     *
     * This WILL update the internal $strIncludedJavaScriptFileArray array.
     *
     * @param string | array $strJavaScriptFileList
     * @return string[] array of script files to include or NULL if none
     */
    protected function processJavaScriptList($strJavaScriptFileList)
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

                // Include it if we're NOT ignoring it and it has NOT already been included
                if ((array_search($strScript, $this->strIgnoreJavaScriptFileArray) === false) &&
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
     * Given a comma-delimited list of stylesheet files, this will return an array of file that NEED to still
     * be included because (1) it hasn't yet been included and (2) it hasn't been specified to be "ignored".
     *
     * This WILL update the internal $strIncludedStyleSheetFileArray array.
     *
     * @param string $strStyleSheetFileList
     * @return string[] array of stylesheet files to include or NULL if none
     */
    protected function processStyleSheetList($strStyleSheetFileList)
    {
        $strArrayToReturn = array();

        // Is there a comma-delimited list of StyleSheet files to include?
        if ($strStyleSheetFileList) {
            $strScriptArray = explode(',', $strStyleSheetFileList);

            // Iterate through the list of StyleSheetFiles to Include...
            foreach ($strScriptArray as $strScript) {
                if ($strScript = trim($strScript) ?? '') // Include it if we're NOT ignoring it and it has NOT already been included
                {
                    if ((array_search($strScript, $this->strIgnoreStyleSheetFileArray) === false) &&
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
     * Returns whether or not this Form is being run due to a PostBack event (e.g. a ServerAction or AjaxAction)
     * @return bool
     */
    public function isPostBack()
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
    public function getErrorMessages($blnErrorsOnly = false)
    {
        $strToReturn = array();
        foreach ($this->getAllControls() as $objControl) {
            if ($objControl->ValidationError) {
                array_push($strToReturn, $objControl->ValidationError);
            }
            if (!$blnErrorsOnly) {
                if ($objControl->Warning) {
                    array_push($strToReturn, $objControl->Warning);
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
    public function getErrorControls($blnErrorsOnly = false)
    {
        $objToReturn = array();
        foreach ($this->getAllControls() as $objControl) {
            if ($objControl->ValidationError) {
                array_push($objToReturn, $objControl);
            } else {
                if (!$blnErrorsOnly) {
                    if ($objControl->Warning) {
                        array_push($objToReturn, $objControl);
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
     */
    public function getJsFileUri($strFile)
    {
        return Application::getJsFileUri($strFile);
    }

    /**
     * Gets the CSS file URI, given a string input
     * @param string $strFile File name to be tested
     *
     * @return string the final CSS URI
     */
    public function getCssFileUri($strFile)
    {
        return Application::getCssFileUri($strFile);
    }

    /**
     * Get high level form javascript files to be included. Default here includes all
     * javascripts needed to run qcubed.
     * Override and add to this list and include
     * javascript and jQuery files and libraries needed for your application.
     * Javascript files included before QCUBED_JS can refer to jQuery as $.
     * After qcubed.js, $ becomes $j, so add other libraries that need
     * $ in a different context after qcubed.js, and insert jQuery libraries and  plugins that
     * refer to $ before qcubed.js file.
     *
     * @return array
     */
    protected function getFormJavaScripts()
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
    protected function getFormStyles()
    {
        return array(
            QCUBED_CSS
        );
    }

    /**
     * Renders the end of the form, including the closing form and body tags.
     * Renders the html for hidden controls.
     * @param bool $blnDisplayOutput should the output be returned or directly printed to screen.
     *
     * @return null|string
     * @throws Caller
     */
    public function renderEnd($blnDisplayOutput = true)
    {
        // Ensure that RenderEnd() has not yet been called
        switch ($this->intFormStatus) {
            case self::FORM_STATUS_UNRENDERED:
                throw new Caller('$this->renderBegin() was never called');
            case self::FORM_STATUS_RENDER_BEGUN:
                break;
            case self::FORM_STATUS_RENDER_ENDED:
                throw new Caller('$this->renderEnd() has already been called');
                break;
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

        /**** Prepare Javascripts ****/

        // Clear included javascript array since we are completely redrawing the page
        $this->strIncludedJavaScriptFileArray = array();
        $strControlIdToRegister = array();
        $strEventScripts = '';

        // Add form level javascripts and libraries
        $strJavaScriptArray = $this->processJavaScriptList($this->getFormJavaScripts());
        Application::addJavaScriptFiles($strJavaScriptArray);
        $strFormJsFiles = Application::renderFiles();    // Render the form-level javascript files separately

        // Go through all controls and gather up any JS or CSS to run or Form Attributes to modify
        foreach ($this->getAllControls() as $objControl) {
            if ($objControl->Rendered || $objControl->ScriptsOnly) {
                $strControlIdToRegister[] = $objControl->ControlId;

                /* Note: GetEndScript may cause the control to register additional commands, or even add javascripts, so those should be handled after this. */
                if ($strControlScript = $objControl->getEndScript()) {
                    $strControlScript = Q\Js\Helper::terminateScript($strControlScript);

                    // Add comments for developer version of output
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

            // Include the javascripts specified by each control.
            if ($strScriptArray = $this->processJavaScriptList($objControl->JavaScripts)) {
                Application::addJavaScriptFiles($strScriptArray);
            }

            // Include any StyleSheets?  The control would have a
            // comma-delimited list of stylesheet files to include (if applicable)
            if ($strScriptArray = $this->processStyleSheetList($objControl->StyleSheets)) {
                Application::addStyleSheets(array_keys($strScriptArray));
            }
        }

        // Add grouping commands to events (Used for deprecated drag and drop, but not removed yet)
        /*
        foreach ($this->objGroupingArray as $objGrouping) {
            $strGroupingScript = $objGrouping->render();
            if (strlen($strGroupingScript) > 0) {
                $strGroupingScript = \QCubed\Js\Helper::terminateScript($strGroupingScript);
                $strEventScripts .= $strGroupingScript;
            }
        }*/

        /*** Build the javascript block ****/

        // Start with variable settings and initForm
        $strEndScript = sprintf('qc.initForm("%s"); ', $this->strFormId);

        // Register controls
        if ($strControlIdToRegister) {
            $strEndScript .= sprintf("qc.regCA(%s); \n", Q\Js\Helper::toJsObject($strControlIdToRegister));
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

        // Add any application level js commands.
        // This will include high and medimum level commands
        $strEndScript .= Application::renderJavascript(true);

        // Add the javascript coming from controls and events just after the medium level commands
        $strEndScript .= ';' . $strEventScripts;

        // Add low level commands and other things that need to execute at the end
        $strEndScript .= ';' . Application::renderJavascript(false);


        // Create Final EndScript Script
        $strEndScript = sprintf('<script type="text/javascript">jQuery(document).ready(function($j) { %s; });</script>',
            $strEndScript);


        /**** Render the HTML itself, appending the javascript we generated above ****/

        foreach ($this->getAllControls() as $objControl) {
            if ($objControl->Rendered) {
                $strHtml .= $objControl->getEndHtml();
            }
            $objControl->resetFlags(); // Make sure controls are serialized in a reset state
        }

        $strHtml .= $strFormJsFiles . _nl();    // Add form level javascript files

        // put javascript environment defines up early for use by other js files.
        $strHtml .= '<script type="text/javascript">' .
            sprintf('qc.baseDir = "%s"; ', QCUBED_BASE_URL) .
            sprintf('qc.jsVendor = "%s"; ', QCUBED_VENDOR_URL) .
            sprintf('qc.jsAssets = "%s"; ', QCUBED_JS_URL) .
            sprintf('qc.phpAssets = "%s"; ', QCUBED_PHP_URL) .
            sprintf('qc.cssAssets = "%s"; ', QCUBED_CSS_URL) .
            sprintf('qc.imageAssets = "%s"; ', QCUBED_IMAGE_URL) .
            '</script>' .
            _nl();

        $strHtml .= Application::renderFiles() . _nl();    // add plugin and control js files

        // Render hidden controls related to the form
        $strHtml .= sprintf('<input type="hidden" name="Qform__FormId" id="Qform__FormId" value="%s" />',
                $this->strFormId) . _nl();
        $strHtml .= sprintf('<input type="hidden" name="Qform__FormControl" id="Qform__FormControl" value="" />') . _nl();
        $strHtml .= sprintf('<input type="hidden" name="Qform__FormEvent" id="Qform__FormEvent" value="" />') . _nl();
        $strHtml .= sprintf('<input type="hidden" name="Qform__FormParameter" id="Qform__FormParameter" value="" />') . _nl();
        $strHtml .= Html::renderTag('input',
            ['type' => 'hidden', 'name' => self::POST_CALL_TYPE, 'id' => self::POST_CALL_TYPE, 'value' => ''], null,
            true) . _nl();
        $strHtml .= sprintf('<input type="hidden" name="Qform__FormUpdates" id="Qform__FormUpdates" value="" />') . _nl();
        $strHtml .= sprintf('<input type="hidden" name="Qform__FormCheckableControls" id="Qform__FormCheckableControls" value="" />') . _nl();

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
     * PHP magic method for getting property values of object
     *
     * @param string $strName
     * @return mixed
     * @throws Caller
     * @throws \Exception
     */
    public function __get($strName)
    {
        switch ($strName) {
            case "FormId":
                return $this->strFormId;
            case "CallType":
                throw new \Exception ('CallType is deprecated. Use Applicaton::isAjax() or Application::instance()->context()->requestMode()');
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
     * PHP magic function to set the value of properties of class object
     * @param string $strName Name of the property
     * @param string $mixValue Value of the property
     *
     * @return void
     * @throws Caller
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case "HtmlIncludeFilePath":
                // Passed-in value is null -- use the "default" path name of file".tpl.php"
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
    public static function EvaluateTemplate_ObHandler($strBuffer)
    {
        return $strBuffer;
    }

}
