   /**
    * Process a click on the Save button.
    */
    protected function save()
    {
        try {
            $this->pnl<?= $strPropertyName ?>->save();
        }
        catch (OptimisticLocking $e) {
            $dlg = Dialog::alert(
                t("Another user has changed the information while you were editing it. Would you like to overwrite their changes, or refresh the page and try editing again?"),
                [t("Refresh"), t("Overwrite")]);
            $dlg->addAction(new Q\Event\DialogButton(0, null, null, true), new Q\Action\AjaxControl($this, "dlgOptimisticLocking_ButtonEvent"));
            return;
        }
        $this->close();
    }

   /**
    * An optimistic lock exception has fired and we have put a dialog on the screen asking the user what they want to do.
    * The user can either overwrite the data, or refresh and start the edit process over.
    *
    * @param string $strFormId      The form id
    * @param string $strControlId   The control id of the dialog
    * @param string $btn            The text on the button
    */
    protected function dlgOptimisticLocking_ButtonEvent($strFormId, $strControlId, $btn)
    {
        if ($btn == "Overwrite") {
            $this->pnl<?= $strPropertyName ?>->save(true);
            $this->Form->getControl($strControlId)->close();
            $this->close();
        } else { // Refresh
            $this->Form->getControl($strControlId)->close();
            $this->pnl<?= $strPropertyName ?>->refresh(true);
        }
    }
