    /**
     * @param FormBase|ControlBase $objParentObject
     * @param null|string $strControlId
     * @throws Exception
     * @throws Caller
     */
    public function __construct(FormBase|ControlBase $objParentObject, ?string $strControlId = null) {
        // Call the Parent
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }

        // Construct the <?= $strPropertyName ?>Connector
        // MAKE SURE we specify "$this" as the Connector's (and thus all subsequent controls') parent
        $this->mct<?= $strPropertyName ?> = <?= $strPropertyName ?>Connector::create($this);

        $this->createObjects();
    }
