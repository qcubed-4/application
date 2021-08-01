<?php
use QCubed\Project\Codegen\CodegenBase as QCodegen;
use QCubed\QString;
?>

   /**
    * Process a click on the Save button.
    *
    * @param $strFormId
    * @param $strControlId
    * @param $strParameter
    */
    protected function btnSave_Click($strFormId, $strControlId, $strParameter) {
        try {
		    $this->pnl<?= $strPropertyName ?>->Save();
        }
        catch (Q\Exception\OptimisticLocking $e) {
            $dlg = Dialog::alert (
                t("Another user has changed the information while you were editing it. Would you like to overwrite their changes, or refresh the page and try editing again?"),
                [t("Refresh"), t("Overwrite")]);
            $dlg->addAction(new Q\Event\DialogButton(0, null, null, true), new Q\Action\Ajax("dlgOptimisticLocking_ButtonEvent"));
            return;
        }
		$this->redirectToListPage();
	}

   /**
    * An optimistic lock exception has fired and we have put a dialog on the screen asking the user what they want to do.
    * The user can either overwrite the data, or refresh and start the edit process over.
    *
    * @param string $strFormId      The form id
    * @param string $strControlId   The control id of the dialog
    * @param string $btn            The text on the button
    */
    protected function dlgOptimisticLocking_ButtonEvent($strFormId, $strControlId, $btn) {
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
    * Process a click of the delete button.
    *
    * @param string $strFormId      The form id
    * @param string $strControlId   The control id of the dialog
    * @param string $strParameter   The control parameter, not used
    */
	protected function btnDelete_Click($strFormId, $strControlId, $strParameter) {
		$this->pnl<?= $strPropertyName ?>->delete();
		$this->redirectToListPage();
	}

   /**
    * Process a click of the cancel button.
    *
    * @param string $strFormId      The form id
    * @param string $strControlId   The control id of the dialog
    * @param string $strParameter   The control parameter, not used
    */
	protected function btnCancel_Click($strFormId, $strControlId, $strParameter) {
		$this->redirectToListPage();
	}

   /**
    * The user has pressed one of the buttons, and now wants to go back to the list page.
    * Override this if you have another way of going to the list page.
    *
    * @param string $strFormId      The form id
    * @param string $strControlId   The control id of the dialog
    * @param string $strParameter   The control parameter, not used
    */
	protected function redirectToListPage() {
		Application::redirect(QCUBED_FORMS_URL . '/<?= QString::underscoreFromCamelCase($objTable->ClassName) ?>_list.php',
            false); // Putting false here is important to preventing an optimistic locking exception as a result of the user pressing the back button on the browser
	}
