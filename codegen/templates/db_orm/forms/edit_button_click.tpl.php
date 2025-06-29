<?php
use QCubed\Project\Codegen\CodegenBase as QCodegen;
use QCubed\QString;
?>
    /**
    * Handles the click event for the Save button.
    *
    * This method attempts to save the current state of the person panel.
    * If an optimistic locking conflict occurs (data modified by another user),
    * the user is prompted with a dialog to either overwrite the changes or refresh the page.
    * Upon successful save, the user is redirected to the list page.
    *
    * @param ActionParams $params The parameters associated with the click action.
    * @return void
    * @throws Caller
    * @throws Throwable
    */
    protected function btnSave_Click(ActionParams $params): void
    {
        try {
            $this->pnl<?= $strPropertyName ?>->save();
        }
        catch (OptimisticLocking $e) {
            $dlg = Dialog::alert(
            t("Another user has changed the information while you were editing it. Would you like to overwrite their changes or refresh the page and try editing again?"),
            [t("Refresh"), t("Overwrite")]);
            $dlg->addAction(new DialogButton(0, null, null, true), new Ajax("dlgOptimisticLocking_ButtonEvent"));
            return;
        }
        $this->redirectToListPage();
    }

    /**
    * An optimistic lock exception has fired, and we have put a dialog on the screen asking the user what they want to do.
    * The user can either overwrite the data or refresh and start the edit process over.
    *
    * @param string $strFormId The form ID
    * @param string $strControlId The control ID of the dialog
    * @param string $btn The text on the button
    * @throws Caller|Throwable
    */
    protected function dlgOptimisticLocking_ButtonEvent(string $strFormId, string $strControlId, string $btn): void
    {
        if ($btn == "Overwrite") {
            $this->pnl<?= $strPropertyName ?>->save(true);
            $this->getControl($strControlId)->close();
            $this->redirectToListPage();
        } else { // Refresh
            $this->getControl($strControlId)->close();
            $this->pnl<?= $strPropertyName ?>->refresh(true);
        }
    }

    /**
    * Handles the click event for the Delete button and displays a confirmation dialog.
    *
    * This method creates a dialog prompt asking the user to confirm the delete action.
    * The dialog contains customizable text and two buttons: "OK" and "Cancel."
    * An action is associated with the "OK" button to handle the confirmation process.
    * The dialog does not allow resizing or closing via a close button.
    *
    * @param ActionParams $params Parameters passed from the action triggering the button click.
    * @return void
    */
    protected function btnDelete_Click(ActionParams $params): void
    {
        $dlgConfirm = new Dialog();
        $dlgConfirm->Text = sprintf(t('Are you SURE you want to DELETE this %s?'), t('<?= $objTable->ClassName ?>'));
        $dlgConfirm->addButton(t('Cancel'));
        $dlgConfirm->addButton(t('OK'));
        $dlgConfirm->addAction(new DialogButton(), new Ajax('dlgConfirm_Button'));
        $dlgConfirm->Width = 400;
        $dlgConfirm->Resizable = false;
        $dlgConfirm->HasCloseButton = false;
    }

    protected function dlgConfirm_Button(ActionParams $params): void
    {
        $dlgConfirm = $params->Control;

        if ($params->ActionParameter == t('OK')) {
            $this->pnl<?= $strPropertyName ?>->delete();
            $this->redirectToListPage();
        } else {
            $dlgConfirm->close();
        }

        $dlgConfirm->close();
    }

    /**
    * Handles the click event for the Cancel button.
    *
    * This method is triggered when the Cancel button is clicked.
    * It redirects the user to the list page without saving any changes.
    *
    * @param ActionParams $params The parameters associated with the Cancel button click event.
    * @return void
    * @throws Throwable
    */
    protected function btnCancel_Click(ActionParams $params): void
    {
        $this->redirectToListPage();
    }

    /**
    * The user has pressed one of the buttons and now wants to go back to the list page.
    * Override this if you have another way of going to the list page.
    *
    * @throws Throwable
    */
    protected function redirectToListPage(): void
    {
		Application::redirect(QCUBED_FORMS_URL. '/<?= QString::underscoreFromCamelCase($objTable->ClassName) ?>_list.php',
            false); // Putting false here is important to preventing an optimistic locking exception as a result of the user pressing the back button on the browser
	}
