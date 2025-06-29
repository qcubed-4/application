    /**
    * Refreshes the current state of the object, optionally reloading the data.
    *
    * @param bool $blnReload Determines whether to reload the data or not.
    * @return void
    * @throws Caller
    * @throws DateMalformedStringException
    * @throws InvalidCast
    */
    public function refresh(bool $blnReload = false): void
    {
        $this->mct<?= $strPropertyName  ?>->refresh($blnReload);
    }
