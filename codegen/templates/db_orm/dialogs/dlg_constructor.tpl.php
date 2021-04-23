	/**
	 * @param FormBase|ControlBase $objParentObject
	 * @param null|string $strControlId
	 * @throws Caller
	 */
	public function __construct($objParentObject = null, $strControlId = null)
    {
		try {
			parent::__construct($objParentObject, $strControlId);
		} catch (Caller $objExc) {
			$objExc->incrementOffset();
			throw $objExc;
		}

		$this->pnl<?= $strPropertyName ?> = new <?= $strPropertyName ?>EditPanel($this);
		$this->createButtons();
	}
