    /**
    * @param FormBase|ControlBase|null $objParentObject
    * @param null|string $strControlId
    * @throws Caller
    * @throws InvalidCast
    */
    public function __construct(FormBase|ControlBase $objParentObject = null, ?string $strControlId = null)
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
