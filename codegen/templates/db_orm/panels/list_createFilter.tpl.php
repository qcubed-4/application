    /**
    * Creates and initializes the filter panel for the control.
    *
    * @return void
    * @throws Caller
    */
    protected function createFilterPanel(): void
    {
        $this->pnlFilter = new Panel($this);    // div wrapper for filter objects
        $this->pnlFilter->AutoRenderChildren = true;

        $this->txtFilter = new TextBox($this->pnlFilter);
        $this->txtFilter->Placeholder = t('Search...');
        $this->txtFilter->TextMode = TextBoxBase::SEARCH;
        $this->addFilterActions();
    }

    /**
    * Adds actions to the filter control for handling input and key events.
    *
    * @return void
    * @throws Caller
    */
    protected function addFilterActions(): void
    {
        $this->txtFilter->addAction(new Input(300), new AjaxControl ($this, 'filterChanged'));
        $this->txtFilter->addActionArray(new EnterKey(),
            [
                new AjaxControl($this, 'FilterChanged'),
                new Terminate()
            ]
        );
    }

    /**
    * Handles the event when a filter is changed and refreshes the data grid.
    *
    * @return void
    */
    protected function filterChanged(): void
    {
<?= $listCodegenerator->dataListRefresh($objCodeGen, $objTable); ?>
    }

