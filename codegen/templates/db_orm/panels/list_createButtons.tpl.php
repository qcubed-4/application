	/**
	 *
	 **/
	protected function createButtonPanel() {
		$this->pnlButtons = new \QCubed\Control\Panel ($this);
		$this->pnlButtons->AutoRenderChildren = true;

		$this->btnNew = new <?= \QCubed\Project\Codegen\CodegenBase::$DefaultButtonClass ?> ($this->pnlButtons);
		$this->btnNew->Text = t('New');
		$this->btnNew->addAction(new Q\Event\Click(), new Q\Action\AjaxControl ($this, 'btnNew_Click'));
	}

	protected function btnNew_Click() {
		$this->editItem();
	}
