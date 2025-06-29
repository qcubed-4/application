    /**
    * Binds data to a data table widget based on a given condition.
    *
    * @return void
    * @throws Caller
    * @throws InvalidCast
    */
    public function bindData(): void
    {
        $objCondition = $this->getCondition();
        $this-><?= $strListVarName ?>->bindData($objCondition);
    }

    /**
    * Retrieves a condition based on the current filter input.
    *
    * This method generates a query condition to filter records. If no filter is provided,
    * it returns a condition that matches all records. Otherwise, it creates a condition
    * to filter records by ID or by matching the search value within the first or last name.
    *
    * @return QQCondition The generated query condition
    * @throws Caller
    * @throws InvalidCast
    */
    protected function getCondition(): QQCondition
    {
<?php if (isset($objTable->Options['CreateFilter']) && $objTable->Options['CreateFilter'] === false) { ?>
        return QQ::all();
<?php } else { ?>
        $strSearchValue = $this->txtFilter->Text;

        if ($strSearchValue === null) {
            $strSearchValue = '';
        }

        $strSearchValue = trim($strSearchValue);

        if ($strSearchValue === '') {
             return QQ::all();
        } else {
<?php
        $cond = array();
        foreach ($objTable->ColumnArray as $objColumn) {
            switch ($objColumn->VariableTypeAsConstant) {
                case 'QCubed\\Type::INTEGER':
                    $cond[] = 'QQ::equal(QQN::' . $objTable->ClassName . '()->' . $objColumn->PropertyName . ', $strSearchValue)';
                    break;
                case 'QCubed\\Type::STRING':
                    $cond[] = 'QQ::like(QQN::' . $objTable->ClassName . '()->' . $objColumn->PropertyName. ', "%" . $strSearchValue . "%")';
                    break;
            }
        }

        $strCondition = implode (",\n                ", $cond);
        if ($strCondition) {
            $strCondition = "QQ::orCondition(
                $strCondition
            )";
} else {
            $strCondition = 'QQ::all()';
        }
?>
            return <?= $strCondition ?>;
<?php } ?>
        }
    }
