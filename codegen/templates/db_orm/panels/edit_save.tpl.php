    /**
    * Saves the current record data, with an option to force an update.
    * @param null|bool $blnForceUpdate Whether to force an update even if no changes are detected. Defaults to false.
    * @return void
    * @throws Caller
    * @throws InvalidCast
    */
    public function save(?bool $blnForceUpdate = false): void
    {
        $this->mct<?= $strPropertyName ?>->save<?= $strPropertyName ?>($blnForceUpdate);
    }
