<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Project\Control\Dialog;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\ControlBase as QControl;
use QCubed\Project\Control\FormBase as QForm;
use QCubed as Q;

/**
 * Class FileAssetDialog
 *
 * This file contains the QFileAssetDialog class.
 *
 * @was QFileAssetDialog
 * @package QCubed\Control
 */
class FileAssetDialog extends Dialog
{
    /** @var Label  */
    public $lblMessage;
    /** @var FileControl  */
    public $flcFileAsset;
    /** @var Label  */
    public $lblError;
    /** @var Button  */
    public $btnUpload;
    /** @var Button  */
    public $btnCancel;
    /** @var WaitIcon  */
    public $objSpinner;
    /** @var  string */
    protected $strFileUploadCallback;

    /**
     * FileAssetDialog constructor.
     * @param QControl|QForm $objParentObject
     * @param string $strFileUploadCallback
     * @param null|string $strControlId
     */
    public function __construct($objParentObject, $strFileUploadCallback, $strControlId = null)
    {
        // Call parent constructor and define FileUploadCallback
        parent::__construct($objParentObject, $strControlId);
        $this->strFileUploadCallback = $strFileUploadCallback;

        // Setup the Dialog Box, itself
        // TODO: Change this default
        $this->strTemplate = __DOCROOT__ . __PHP_ASSETS__ . '/QFileAssetDialog.tpl.php';
        //$this->blnDisplay = false;
        //$this->blnMatteClickable = false;
        $this->AutoOpen = false;

        $this->strTitle = t("Upload a File");

        // Controls for Upload FileAsset Dialog Box
        $this->lblMessage = new Label($this);
        $this->lblMessage->HtmlEntities = false;

        $this->lblError = new Label($this);
        $this->lblError->HtmlEntities = false;

        $this->flcFileAsset = new FileControl($this);
        $this->btnUpload = new Button($this);
        $this->btnCancel = new Button($this);
        $this->objSpinner = new WaitIcon($this);

        // Events on the Dialog Box Controls
        $this->flcFileAsset->addAction(new Q\Event\EnterKey(), new Q\Action\Terminate());

        $this->btnUpload->addAction(new Q\Event\Click(), new Q\Action\ToggleEnable($this->btnUpload));
        $this->btnUpload->addAction(new Q\Event\Click(), new Q\Action\ToggleEnable($this->btnCancel));
        $this->btnUpload->addAction(new Q\Event\Click(), new Q\Action\ToggleDisplay($this->objSpinner));
        $this->btnUpload->addAction(new Q\Event\Click(), new Q\Action\ServerControl($this, 'btnUpload_Click'));

        $this->btnCancel->addAction(new Q\Event\Click(), new Q\Action\HideDialog($this));
    }

    public function btnUpload_Click($strFormId, $strControlId, $strParameter)
    {
        $this->btnUpload->Enabled = true;
        $this->btnCancel->Enabled = true;
        $this->objSpinner->Display = false;

        $strFileControlCallback = $this->strFileUploadCallback;

        if (isset($this->objParentControl)) {
            $parent = $this->objParentControl;
        } else {
            $parent = $this->objForm;
        }
        $parent->$strFileControlCallback($strFormId, $strControlId, $strParameter);
    }

    public function showError($strErrorMessage)
    {
        $this->lblError->Text = $strErrorMessage;
        $this->flcFileAsset->focus();
        $this->blink();
    }
}
