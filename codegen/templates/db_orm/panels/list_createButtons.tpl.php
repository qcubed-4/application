    /**
    * Create a panel containing button controls.
    *
    * @return void
    * @throws Caller
    */
    protected function createButtonPanel(): void
    {
        $this->pnlButtons = new Panel ($this);
        $this->pnlButtons->AutoRenderChildren = true;

        $this->btnNew = new Button($this->pnlButtons);
        $this->btnNew->Text = t('New');
        $this->btnNew->addAction(new Click(), new AjaxControl ($this, 'btnNew_Click'));
    }

    /**
    * Handles the click event of the "New" button and initiates the editing process for a new item.
    *
    * @return void
    * @throws Throwable
    */
    protected function btnNew_Click(): void
    {
        $this->editItem();
    }
