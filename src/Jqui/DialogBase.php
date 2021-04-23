<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Jqui;

use QCubed as Q;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Jqui;
use QCubed\Project\Control\Dialog;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class DialogBase
 *
 * The DialogBase class defined here provides an interface between the generated
 * DialogGen class and QCubed. This file is part of the core and will be overwritten
 * when you update QCubed. To override, make your changes to the QDialog.class.php file instead.
 *
 *
 * A QDialog is a QPanel that pops up on the screen and implements an "in window" dialog.
 *
 * There are a couple of ways to use the dialog. The simplest is as follows:
 *
 * In your Form_Create():
 * <code>
 * $dlg = new Dialog($this);
 * $this->dlg->AutoOpen = false;
 * $this->dlg->Modal = true;
 * $this->dlg->Text = 'Show this on the dialog.'
 * $this->dlg->addButton('OK', 'ok');
 * $this->dlg->addAction(new QDialog_ButtonEvent(), new QHideDialog());
 * </code>
 *
 * When you want to show the dialog:
 * <code>
 * $this->dlg->open();
 * </code>
 *
 * And, also remember to draw the dialog in your form template:
 *
 * <code>
 * $this->dlg->render();
 * </code>
 *
 *
 * Since Dialog is a descendant of Panel, you can do anything you can to a normal Panel,
 * including add QControls and use a template. When you want to hide the dialog, call <code>Close()</code>
 *
 * @property boolean $HasCloseButton Disables (false) or enables (true) the close X in the upper right corner of the title. Can be set when initializing the dialog.
 *    Can be set when initializing the dialog. Also enables or disables the ability to close the box by pressing the ESC key.
 * @property-read integer $ClickedButton Returns the id of the button most recently clicked. (read-only)
 * @property-write string $DialogState Set whether this dialog is in an error or highlight (info) state. Choose on of Dialog::STATE_NONE, QDialogState::STATE_ERROR, QDialogState::stateHighlight(write-only)
 *
 * @link http://jqueryui.com/dialog/
 * @was QDialogBase
 */
class DialogBase extends DialogGen implements Q\Control\DialogInterface
{
    // enumerations

    /** Default dialog state */
    const STATE_NONE = '';
    /** Display using the Themeroller error state */
    const STATE_ERROR = 'ui-state-error';
    /** Display using the Themeroller highlight state */
    const STATE_HIGHLIGHT = 'ui-state-highlight';

    /** The control id to use for the reusable global alert dialog. */
    const MESSAGE_DIALOG_ID = 'qAlertDialog';

    /** @var bool default to auto open being false, since this would be a rare need, and dialogs are auto-rendered. */
    protected $blnAutoOpen = false;
    /** @var  string Id of last button clicked. */
    protected $strClickedButtonId;
    /** @var bool Should we draw a close button on the top? */
    protected $blnHasCloseButton = true;
    /** @var bool records whether dialog is open */
    protected $blnIsOpen = false;
    /** @var array whether a button causes validation */
    protected $blnValidationArray = array();
    /** @var bool */
    protected $blnUseWrapper = true;
    /** @var  string state of the dialog for special display */
    protected $strDialogState;
    /** @var bool */
    protected $blnAutoRender = true;
    /** @var bool Whether to show the dialog as a modal dialog. Most dialogs are modal, so this defaults to true. */
    protected $blnModal = true;


    public function __construct($objParentObject = null, $strControlId = null)
    {
        // Detect which mode we are going to display in, whether to show right away, or wait for later.
        if ($objParentObject === null) {
            // The dialog will be shown right away, and then when closed, removed from the form.
            global $_FORM;
            $objParentObject = $_FORM;    // The parent object should be the form. Prevents spurious redrawing.
            $this->blnDisplay = true;
            $this->blnAutoOpen = true;
            $blnAutoRemove = true;
        } else {
            $this->blnDisplay = false;
            $blnAutoRemove = false;
        }
        parent::__construct($objParentObject, $strControlId);
        $this->mixCausesValidation = $this;
        if ($blnAutoRemove) {
            // We need to immediately detect a close so we can remove it from the form
            // Delay in an attempt to make sure this is the very last thing processed for the dialog.
            // If you want to do something just before closing, trap the QDialog_BeforeCloseEvent
            $this->addAction(new Jqui\Event\DialogClose(10), new Q\Action\AjaxControl($this, 'dialog_Close'));
        }
    }

    /**
     * Validate the child items if the dialog is visible and the clicked button requires validation.
     * This piece of magic makes validation specific to the dialog if an action is coming from the dialog,
     * and prevents the controls in the dialog from being validated if the action is coming from outside
     * the dialog.
     *
     * @return bool
     */
    public function validateControlAndChildren()
    {
        if ($this->blnIsOpen) {    // don't validate a closed dialog
            if (!empty($this->mixButtons)) {    // using built-in dialog buttons
                if (!empty ($this->blnValidationArray[$this->strClickedButtonId])) {
                    return parent::validateControlAndChildren();
                }
            } else {    // using QButtons placed in the control
                return parent::validateControlAndChildren();
            }
        }
        return true;
    }

    /**
     * Returns the control id for purposes of jQuery UI.
     * @return string
     */
    public function getJqControlId()
    {
        return $this->getWrapperId();
    }

    /**
     * Overrides the parent to add code to cause the default button to be fired if an enter key is pressed
     * on a control. This purposefully does not include textarea controls, which should get the enter key to
     * insert a newline.
     */

    protected function makeJqWidget()
    {
        parent::makeJqWidget();
        Application::executeJsFunction('qc.dialog', $this->getJqControlId(), Application::PRIORITY_HIGH);
    }

    /**
     * Add additional javascript to the dialog creation to further format the dialog.
     * This will set the class of the title bar to the strDialogState value and add an
     * icon to implement a dialog state. Override and restyle for a different look.
     * @return string
     */
    protected function stylingJs()
    {
        $strJs = '';
        if ($this->strDialogState) {
            $strIcon = '';
            
            // Move the dialog class to the header of dialog to improve the appearance over the default.
            // Also add an appropriate icon.
            // Override this if you want your dialogs to look different.
            switch ($this->strDialogState) {
                case Dialog::STATE_ERROR:
                    $strIcon = 'alert';
                    break;

                case Dialog::STATE_HIGHLIGHT:
                    $strIcon = 'info';
                    break;
            }
            $strIconJs = sprintf('<span class="ui-icon ui-icon-%s" ></span>', $strIcon);

            $strJs .= sprintf(
                '$j("#%s").prev().addClass("%s").prepend(\'%s\');
                ',
                $this->getJqControlId(), $this->strDialogState, $strIconJs);
        }
        return $strJs;
    }

    /**
     * Implements QCubed specific dialog functions. Makes sure dialog is put at the end of the form
     * to fix an overlay problem with jQuery UI.
     *
     * @return string
     */
    protected function makeJqOptions()
    {
        $jqOptions = parent::makeJqOptions();

        $controlId = $this->ControlId;
        $strFormId = $this->Form->FormId;

        if (!$this->blnHasCloseButton) {
            $strHideCloseButtonScript = '$j(this).prev().find(".ui-dialog-titlebar-close").hide();';
        } else {
            $strHideCloseButtonScript = '';
        }

        $jqOptions['open'] = new Q\Js\Closure (
            sprintf('qcubed.recordControlModification("%s", "_IsOpen", true);
            %s', $controlId, $strHideCloseButtonScript)
            , ['event', 'ui']);
        $jqOptions['close'] = new Q\Js\Closure (sprintf(
            'qcubed.recordControlModification("%s", "_IsOpen", false);
            ', $controlId), ['event', 'ui']);
        $jqOptions['appendTo'] = "#{$strFormId}";

        // By doing the styling at creation time, we ensure that it gets done only once.
        if ($strCreateJs = $this->stylingJs()) {
            $jqOptions['create'] = new Q\Js\Closure($strCreateJs);
        }
        return $jqOptions;
    }


    /**
     * Adds a button to the dialog. Use this to add buttons BEFORE bringing up the dialog.
     *
     * @param string $strButtonName
     * @param string $strButtonId Id associated with the button for detecting clicks. Note that this is not the id on the form.
     *                                    Different dialogs can have the same button id.
     *                                    To specify a control id for the button (for styling purposes for example), set the id in options.
     * @param bool $blnCausesValidation If the button causes the dialog to be validated before the action is executed
     * @param bool $blnIsPrimary Whether this button will be automatically clicked if user presses an enter key.
     * @param string $strConfirmation If set, will confirm with the given string before the click is sent
     * @param array $options Additional attributes to add to the button. Useful things to do are:
     *                                    array('class'=>'ui-button-left') to create a button on the left side.
     *                                    array('class'=>'ui-priority-primary') to style a button as important or primary.
     */
    public function addButton(
        $strButtonName,
        $strButtonId = null,
        $blnCausesValidation = false,
        $blnIsPrimary = false,
        $strConfirmation = null,
        $options = null
    ) {
        if (!$this->mixButtons) {
            $this->mixButtons = array();
        }
        $strJS = '';
        if ($strConfirmation) {
            $strJS .= sprintf('if (confirm("%s"))', $strConfirmation);
        }

        $controlId = $this->ControlId;

        if (!$strButtonId) {
            $strButtonId = $strButtonName;
        }

        // Brackets are for possible "confirm" above
        $strJS .= sprintf('
            {
                qcubed.recordControlModification("%s", "_ClickedButton", "%s");
                $j("#%s").trigger("%s", $j(event.currentTarget).data("btnid"));
            }
            event.preventDefault();
            ', $controlId, $strButtonId, $controlId, addslashes(Q\Event\DialogButton::EVENT_NAME));

        $btnOptions = array(
            'text' => $strButtonName,
            'click' => new Q\Js\NoQuoteKey(new Q\Js\Closure($strJS, array('event'))),
            'data-btnid' => $strButtonId
        );

        if ($options) {
            $btnOptions = array_merge($options, $btnOptions);
        }

        if ($blnIsPrimary) {
            $btnOptions['type'] = 'submit';
        }

        $this->mixButtons[] = $btnOptions;

        $this->blnValidationArray[$strButtonId] = $blnCausesValidation;

        $this->blnModified = true;
    }

    /**
     * Remove the given button from the dialog.
     *
     * @param $strButtonId
     */
    public function removeButton($strButtonId)
    {
        if (!empty($this->mixButtons)) {
            $this->mixButtons = array_filter($this->mixButtons, function ($a) use ($strButtonId) {
                return $a['id'] == $strButtonId;
            });
        }

        unset ($this->blnValidationArray[$strButtonId]);

        $this->blnModified = true;
    }

    /**
     * Remove all the buttons from the dialog.
     */
    public function removeAllButtons()
    {
        $this->mixButtons = array();
        $this->blnValidationArray = array();
        $this->blnModified = true;
    }

    /**
     * Show or hide the given button. Changes the display attribute, so the buttons will reflow.
     *
     * @param $strButtonId
     * @param $blnVisible
     */
    public function showHideButton($strButtonId, $blnVisible)
    {
        if ($blnVisible) {
            Application::executeJavaScript(
                sprintf('$j("#%s").next().find("button[data-btnid=\'%s\']").show();',
                    $this->getJqControlId(), $strButtonId)
            );
        } else {
            Application::executeJavaScript(
                sprintf('$j("#%s").next().find("button[data-btnid=\'%s\']").hide();',
                    $this->getJqControlId(), $strButtonId)
            );
        }
    }

    /**
     * Applies CSS styles to a button that is already in the dialog.
     *
     * @param string $strButtonId Id of button to set the style on
     * @param array $styles Array of key/value style specifications
     */
    public function setButtonStyle($strButtonId, $styles)
    {
        Application::executeJavaScript(
            sprintf('$j("#%s").next().find("button[data-btnid=\'%s\']").css(%s)', $this->getJqControlId(), $strButtonId,
                Q\Js\Helper::toJsObject($styles))
        );
    }

    /**
     * Adds a close button that just closes the dialog without firing the QDialogButton event. You can
     * detect this by adding an action to the QDialog_BeforeCloseEvent.
     *
     * @param $strButtonName
     */
    public function addCloseButton($strButtonName)
    {
        // This is an alternate button format supported by jQuery UI.
        $this->mixButtons[$strButtonName] = new Q\Js\Closure('$j(this).dialog("close")');
    }

    /**
     * Create a message dialog. Automatically adds an OK button that closes the dialog. To detect the close,
     * add an action on the Q\JqUi\Event\DialogClose. To change the message, use the return value and set ->Text.
     * To detect a button click, add a Q\Event\DialogButton.
     *
     * If you specify no buttons, a close box in the corner will be created that will just close the dialog. If you
     * specify just a string in $strButtons, or just one string in the button array, one button will be shown that will just close the message.
     *
     * If you specify more than one button, the first button will be the default button (the one pressed if the user presses the return key). In
     * this case, you will need to detect the button by adding a Q\Event\DialogButton. You will also be responsible for calling "Close()" on
     * the dialog after detecting a button.
     *
     * @param string $strMessage // The message
     * @param string|string[]|null $strButtons
     * @param string|null $strControlId
     * @return Dialog
     */
    public static function alert($strMessage, $strButtons = null, $strControlId = null)
    {
        $dlg = new Dialog(null, $strControlId);
        $dlg->Modal = true;
        $dlg->Resizable = false;
        $dlg->Text = $strMessage;
        if ($strButtons) {
            $dlg->blnHasCloseButton = false;
            if (is_string($strButtons)) {
                $dlg->addCloseButton($strButtons);
            } elseif (count($strButtons) == 1) {
                $dlg->addCloseButton($strButtons[0]);
            } else {
                $strButton = array_shift($strButtons);
                $dlg->addButton($strButton, null, false, true);    // primary button

                foreach ($strButtons as $strButton) {
                    $dlg->addButton($strButton);
                }
            }
        } else {
            $dlg->blnHasCloseButton = true;
            $dlg->Height = 100; // fix problem with jquery ui dialog making space for buttons that don't exist
        }
        $dlg->open();
        return $dlg;
    }

    /**
     * A dialog is closing that is autoRemoved, so we remove the dialog from the form and the dom.
     *
     * @param $strFormId
     * @param $strControlId
     * @param $strParameter
     */
    public function dialog_Close($strFormId, $strControlId, $strParameter)
    {
        $this->Form->removeControl($this->ControlId);
    }

    /**
     * Show the dialog.
     * @deprecated
     */
    public function showDialogBox()
    {
        $this->open();
    }

    /**
     * Hide the dialog
     */
    public function hideDialogBox()
    {
        $this->close();
    }

    public function open()
    {
        $this->Visible = true;
        $this->Display = true;
        parent::open();
    }

    /**
     * Closes the dialog. To detect the close, use the DialogBeforeClose Event.
     *
     */
    public function close()
    {
        Application::instance()->executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "close",
            Application::PRIORITY_LAST);
    }

    /**
     * PHP magic method
     *
     * @param string $strName
     * @param string $mixValue
     * @throws Caller
     * @throws InvalidCast
     * @return void
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case '_ClickedButton': // Internal only. Do not use. Used by JS above to keep track of clicked button.
                try {
                    $this->strClickedButtonId = Type::cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case '_IsOpen': // Internal only, to detect when dialog has been opened or closed.
                try {
                    $this->blnIsOpen = Type::cast($mixValue, Type::BOOLEAN);
                    $this->blnAutoOpen = $this->blnIsOpen;  // in case it gets redrawn
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            // set to false to remove the close x in upper right corner and disable the
            // escape key as well
            case 'HasCloseButton':
                try {
                    $this->blnHasCloseButton = Type::cast($mixValue, Type::BOOLEAN);
                    $this->blnCloseOnEscape = $this->blnHasCloseButton;
                    $this->blnModified = true;    // redraw
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Height':
                try {
                    if ($mixValue == 'auto') {
                        $this->mixHeight = 'auto';
                        if ($this->Rendered) {
                            $this->option2($strName, $mixValue);
                        }
                    } else {
                        parent::__set($strName, $mixValue);
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Width':
                try {
                    if ($mixValue == 'auto') {
                        $this->intWidth = 'auto';
                        if ($this->Rendered) {
                            $this->option2($strName, $mixValue);
                        }
                    } else {
                        parent::__set($strName, $mixValue);
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'DialogState':
                try {
                    $this->strDialogState = Type::cast($mixValue, Type::STRING);
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

    /**
     * PHP magic method
     *
     * @param string $strName
     *
     * @return mixed
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case 'ClickedButton':
                return $this->strClickedButtonId;

            case 'HasCloseButton' :
                return $this->blnHasCloseButton;

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