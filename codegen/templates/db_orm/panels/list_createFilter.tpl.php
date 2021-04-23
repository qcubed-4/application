	/**
	 *
	 **/
	protected function createFilterPanel() {
		$this->pnlFilter = new Panel($this);	// div wrapper for filter objects
		$this->pnlFilter->AutoRenderChildren = true;

		$this->txtFilter = new TextBox($this->pnlFilter);
		$this->txtFilter->Placeholder = t('Search...');
		$this->txtFilter->TextMode = \QCubed\Control\TextBoxBase::SEARCH;
		$this->addFilterActions();
	}

	protected function addFilterActions() {
		$this->txtFilter->addAction(new \QCubed\Event\Input(300), new \QCubed\Action\AjaxControl ($this, 'filterChanged'));
		$this->txtFilter->addActionArray(new \QCubed\Event\EnterKey(),
			[
				new Q\Action\AjaxControl($this, 'FilterChanged'),
				new Q\Action\Terminate()
			]
		);
	}

	protected function filterChanged() {
<?= $listCodegenerator->dataListRefresh($objCodeGen, $objTable); ?>
	}

