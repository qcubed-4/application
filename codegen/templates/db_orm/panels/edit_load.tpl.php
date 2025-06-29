    /**
    * Loads a record/s based on the given data.
    <?php foreach ($objTable->PrimaryKeyColumnArray as $objColumn) {
        $displayType = $objColumn->VariableType;
        if ($displayType === 'integer') {
            $displayType = 'int';
        }
    ?>
* @param null|<?= $displayType ?? $objColumn->VariableType ?> $<?= $objColumn->VariableName ?> The <?= $objColumn->Name ?> to load. If null, defaults will be used.
    <?php } ?>
* @return void
    * @throws Caller
    * @throws DateMalformedStringException
    * @throws InvalidCast
    */
    public function load(<?php
            foreach ($objTable->PrimaryKeyColumnArray as $objColumn) {
                $substitute = $displayType ?? $objColumn->VariableType;
                echo '?' . $substitute  . ' $'. $objColumn->VariableName . ' = null, ';
            } GO_BACK(2);?>): void
    {
        if (!$this->mct<?php echo $objTable->ClassName  ?>->load (<?php
            foreach ($objTable->PrimaryKeyColumnArray as $objColumn) {
                echo '$'. $objColumn->VariableName . ', ';
            } GO_BACK(2);?>)) {
            Application::displayAlert(t('Could not load the record. Perhaps it was deleted? Try again.'));
        }
    }
