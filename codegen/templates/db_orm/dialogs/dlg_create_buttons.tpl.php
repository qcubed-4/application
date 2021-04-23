	/**
	 * Create the buttons at the bottom of the dialog.
	 */
	protected function createButtons()
    {
		// Create Buttons
		$this->addButton(t('Save'), 'save', true, true, null, array('class'=>'ui-priority-primary'));
		$this->addButton(t('Cancel'), 'cancel');
		$this->addButton(t('Delete'), 'delete', false, false,
			sprintf(t('Are you SURE you want to DELETE this %s?'),  t('<?= $strPropertyName ?>')),
			array('class'=>'ui-button-left')
		);
		$this->addAction(new Q\Event\DialogButton(0, null, null, true), new Q\Action\AjaxControl($this, 'buttonClick'));
	}
