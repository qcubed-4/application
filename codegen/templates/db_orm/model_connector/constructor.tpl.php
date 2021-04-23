/**
     * Main constructor.  Constructor OR static create methods are designed to be called in either
     * a parent Panel or the main Form when wanting to create a
     * <?= $objTable->ClassName ?>Connector to edit a single <?= $objTable->ClassName ?> object within the
     * Panel or Form.
     *
     * This constructor takes in a single <?= $objTable->ClassName ?> object, while any of the static
     * create methods below can be used to construct based off of individual PK ID(s).
     *
     * @param FormBase|ControlBase $objParentObject Form or Panel which will be using this <?= $objTable->ClassName ?>Connector
     * @param <?= $objTable->ClassName ?> $<?= $objCodeGen->modelVariableName($objTable->Name); ?> new or existing <?= $objTable->ClassName ?> object
     */
     public function __construct($objParentObject, <?= $objTable->ClassName ?> $<?= $objCodeGen->modelVariableName($objTable->Name); ?>)
     {
        // Setup Parent Object (e.g. Form or Panel which will be using this <?= $objTable->ClassName ?>Connector)
        $this->objParentObject = $objParentObject;

        // Setup linked <?= $objTable->ClassName ?> object
        $this-><?= $objCodeGen->modelVariableName($objTable->Name); ?> = $<?= $objCodeGen->modelVariableName($objTable->Name); ?>;

        // Figure out if we're Editing or Creating New
        if ($this-><?= $objCodeGen->modelVariableName($objTable->Name); ?>->__Restored) {
            $this->strTitleVerb = t('Edit');
            $this->blnEditMode = true;
        } else {
            $this->strTitleVerb = t('Create');
            $this->blnEditMode = false;
        }
     }

    /**
     * Static Helper Method to Create using PK arguments
     * You must pass in the PK arguments on an object to load, or leave it blank to create a new one.
     * If you want to load via QueryString or PathInfo, use the CreateFromQueryString or CreateFromPathInfo
     * static helper methods.  Finally, specify a CreateType to define whether or not we are only allowed to
     * edit, or if we are also allowed to create a new one, etc.
     *
     * @param FormBase|ControlBase $objParentObject Form or Panel which will be using this <?= $objTable->ClassName ?>Connector
<?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>
     * @param null|<?= $objColumn->VariableType ?> $<?= $objColumn->VariableName ?> primary key value
<?php } ?>
     * @param integer $intCreateType rules governing <?= $objTable->ClassName ?> object creation - defaults to CreateOrEdit
     * @return <?= $objTable->ClassName ?>Connector
     * @throws Caller
     */
    public static function create($objParentObject, <?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>$<?= $objColumn->VariableName ?> = null, <?php } ?>$intCreateType = Q\ModelConnector\Options::CREATE_OR_EDIT)
    {
        // Attempt to Load from PK Arguments
        if (<?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>strlen($<?= $objColumn->VariableName ?>) && <?php } ?><?php GO_BACK(4); ?>) {
            $<?= $objCodeGen->modelVariableName($objTable->Name); ?> = <?= $objTable->ClassName ?>::load(<?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>$<?= $objColumn->VariableName ?>, <?php } ?><?php GO_BACK(2); ?>);

            // <?= $objTable->ClassName ?> was found -- return it!
            if ($<?= $objCodeGen->modelVariableName($objTable->Name); ?>)
                return new <?= $objTable->ClassName ?>Connector($objParentObject, $<?= $objCodeGen->modelVariableName($objTable->Name); ?>);

            // If CreateOnRecordNotFound not specified, throw an exception
            else if ($intCreateType != Q\ModelConnector\Options::CREATE_ON_RECORD_NOT_FOUND)
                throw new Caller('Could not find a <?= $objTable->ClassName ?> object with PK arguments: ' . <?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>$<?= $objColumn->VariableName ?> . ', ' . <?php } ?><?php GO_BACK(10); ?>);

        // If EditOnly is specified, throw an exception
        } else if ($intCreateType == Q\ModelConnector\Options::EDIT_ONLY)
            throw new Caller('No PK arguments specified');

        // If we are here, then we need to create a new record
        return new <?= $objTable->ClassName ?>Connector($objParentObject, new <?= $objTable->ClassName ?>());
    }

    /**
     * Static Helper Method to Create using PathInfo arguments
     *
     * @param FormBase|ControlBase $objParentObject Form or Panel which will be using this <?= $objTable->ClassName ?>Connector
     * @param integer $intCreateType rules governing <?= $objTable->ClassName ?> object creation - defaults to CreateOrEdit
     * @return <?= $objTable->ClassName ?>Connector
     */
    public static function createFromPathInfo($objParentObject, $intCreateType = Q\ModelConnector\Options::CREATE_OR_EDIT)
    {
<?php $_INDEX = 0; foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>
        $<?= $objColumn->VariableName ?> = Application::instance()->context()->pathInfo(<?= $_INDEX ?>);
<?php $_INDEX++; } ?>
        return <?= $objTable->ClassName ?>Connector::create($objParentObject, <?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>$<?= $objColumn->VariableName ?>, <?php } ?>$intCreateType);
    }

    /**
     * Static Helper Method to Create using QueryString arguments
     *
     * @param FormBase|ControlBase $objParentObject Form or Panel which will be using this <?= $objTable->ClassName ?>Connector
     * @param integer $intCreateType rules governing <?= $objTable->ClassName ?> object creation - defaults to CreateOrEdit
     * @return <?= $objTable->ClassName ?>Connector
     */
    public static function createFromQueryString($objParentObject, $intCreateType = Q\ModelConnector\Options::CREATE_OR_EDIT)
    {
<?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>
        $<?= $objColumn->VariableName ?> = Application::instance()->context()->queryStringItem('<?= $objColumn->VariableName ?>');
<?php } ?>
        return <?= $objTable->ClassName ?>Connector::create($objParentObject, <?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) { ?>$<?= $objColumn->VariableName ?>, <?php } ?>$intCreateType);
    }