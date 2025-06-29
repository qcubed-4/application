<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Codegen\Generator;

use Exception;
use QCubed\Codegen\ColumnInterface;
use QCubed\Codegen\DatabaseCodeGen;
use QCubed\Codegen\ManyToManyReference;
use QCubed\Codegen\ReverseReference;
use QCubed\Codegen\SqlColumn;
use QCubed\Codegen\SqlTable;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;

/**
 * Class ListControl
 *
 * @package QCubed\Codegen\Generator
 */
class ListControl extends Control
{
    public function __construct(string $strControlClassName = 'QCubed\\Control\\ListControl')
    {
        parent::__construct($strControlClassName);
    }

    /**
     * @param string $strPropName
     * @return string
     */
    public function varName(string $strPropName): string
    {
        return 'lst' . $strPropName;
    }

    public function connectorImports(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn): array
    {
        $a = parent::connectorImports($objCodeGen, $objTable, $objColumn);
        //$a[] = ['class'=>'QCubed\\Control\\ListControl'];
        $a[] = ['class'=>'QCubed\\Project\\Control\\ListBox'];
        $a[] = ['class'=>'QCubed\\Control\\ListItem'];
        $a[] = ['class'=>'QCubed\\Query\\Condition\\ConditionInterface', 'as'=>'QQCondition'];
        $a[] = ['class'=>'QCubed\\Query\\Clause\\ClauseInterface', 'as'=>'QQClause'];
        $a[] = ['class'=>'QCubed\\Exception\\InvalidCast'];
        $a[] = ['class'=>'QCubed\\Database\\Exception\\UndefinedPrimaryKey'];

        return $a;
    }

    /**
     * Generate code that will be inserted into the ModelConnector to connect a database object with this control.
     * This is called during the codegen process. This is very similar to the QListControl code, but there are
     * some differences. In particular, this control does not support ManyToMany references.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @return string
     * @throws InvalidCast
     * @throws Caller
     */
    public function connectorCreate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn): string
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strControlVarName = $objCodeGen->modelConnectorVariableName($objColumn);
        $strLabelName = addslashes(DatabaseCodeGen::modelConnectorControlName($objColumn));
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);

        // Read the control type in case we are generating code for a similar class
        $strControlType = $objCodeGen->getControlCodeGenerator($objColumn)->getControlClass();

        $displayType = $strControlType;
        if ($displayType === 'QCubed\Project\Control\ListBox') {
            $displayType = 'ListBox';
        }
        if ($displayType === 'QCubed\Control\CheckboxList') {
            $displayType = 'CheckboxList';
        }
        // add more else-ifs if necessary!

        // Create a control designed just for selecting from a type table
        if (($objColumn instanceof SqlColumn && $objColumn->Reference->IsType) ||
            ($objColumn instanceof ManyToManyReference && $objColumn->IsTypeAssociation)
        ) {
            $strRet = <<<TMPL
    /**
     * Create and set up a {$displayType} control for selecting {$objTable->ClassName} Types
     *
     * @param string|null \$strControlId Optional control ID for the {$displayType}
     * @return {$displayType}|null The created {$displayType} control or null if unsuccessful
     * @throws Caller
     * @throws InvalidCast
     */
    public function {$strControlVarName}_Create(?string \$strControlId = null): ?{$displayType} 
    {

TMPL;
        } else {    // Create a control that presents a list taken from the database

            $strRet = <<<TMPL
     /**
     * Creates and initializes a {$displayType} control for selecting a {$strPropName} entity.
     *
     * @param string|null \$strControlId Optional control ID for the ListBox.
     * @param QQCondition|null \$objCondition Optional condition to filter the items in the {$displayType}.
     * @param array|null \$objClauses Optional clauses to modify the query for retrieving items.
     * @return {$displayType}|null The created {$displayType} control, or null if unsuccessful.
     * @throws Caller
     * @throws DateMalformedStringException
     * @throws InvalidCast
     */
    public function {$strControlVarName}_Create(?string \$strControlId = null, ?QQCondition \$objCondition = null, ?array \$objClauses = null): ?{$displayType}  
    {
        \$this->obj{$strPropName}Condition = \$objCondition;
        \$this->obj{$strPropName}Clauses = \$objClauses;

TMPL;
        }
        // Allow the codegen process to either create custom ids based on the field/table names, or to be
        // Specified by the developer.
        $strControlIdOverride = $objCodeGen->generateControlId($objTable, $objColumn);

        if ($strControlIdOverride) {
            $strRet .= <<<TMPL
        if (!\$strControlId) {
            \$strControlId = '$strControlIdOverride';
        }

TMPL;
        }

        $strRet .= <<<TMPL
        \$this->{$strControlVarName} = new {$displayType}(\$this->objParentObject, \$strControlId);
        \$this->{$strControlVarName}->Name = t('{$strLabelName}');

TMPL;

        if ($objColumn instanceof SqlColumn && $objColumn->NotNull) {
            $strRet .= <<<TMPL
        \$this->{$strControlVarName}->Required = true;

TMPL;
        }

        if ($strMethod = DatabaseCodeGen::$PreferredRenderMethod) {
            $strRet .= <<<TMPL
        \$this->{$strControlVarName}->PreferredRenderMethod = '$strMethod';

TMPL;
        }

        $strRet .= $this->connectorCreateOptions($objCodeGen, $objTable, $objColumn, $strControlVarName);
        $strRet .= $this->connectorRefresh($objCodeGen, $objTable, $objColumn, true);

        $strRet .= <<<TMPL
        return \$this->{$strControlVarName};
    }

TMPL;

        if ($objColumn instanceof SqlColumn && $objColumn->Reference->IsType ||
            $objColumn instanceof ManyToManyReference && $objColumn->IsTypeAssociation
        ) {
            if ($objColumn instanceof SqlColumn) {
                $strVarType = $objColumn->Reference->VariableType;
            } else {
                $strVarType = $objColumn->VariableType;
            }
            $strRefVarName = null;

            $displayLowercase = strtolower($objTable->ClassName);
            $strRet .= <<<TMPL

     /**
     * Retrieves an array of {$displayLowercase} types.
     *
     * @return array An associative array of {$displayLowercase} types where keys are type identifiers and values are type names.
     */
    public function {$strControlVarName}_GetItems(): array 
    {
        return {$strVarType}::nameArray();
    }


TMPL;
        } elseif ($objColumn instanceof ManyToManyReference) {
            $strRefVarName = $objColumn->VariableName;
            $strVarType = $objColumn->VariableType;
            $strRefTable = $objColumn->AssociatedTable;
            $strRefPropName = $objColumn->OppositeObjectDescription;
            $strRefPK = $objCodeGen->getTable($strRefTable)->PrimaryKeyColumnArray[0]->PropertyName;
            //$strPK = $objTable->PrimaryKeyColumnArray[0]->PropertyName;

            $strRet .= <<<TMPL

     /**
     * Retrieves a list of {$strPropName} items based on specified conditions and clauses///////
     * This method generates an array of ListItem objects representing {$strPropName} entries,
     * with an appropriate selection state determined by the associated {$objTable->ClassName} object.
     *
     * @return ListItem[] An array of ListItem objects, each representing a {$strPropName} entity.
     * @throws Caller
     * @throws DateMalformedStringException
     * @throws InvalidCast
     */
    public function {$strControlVarName}_GetItems(): array 
    {
        \$a = array();
        \$objCondition = \$this->obj{$strPropName}Condition;
        if (is_null(\$objCondition)) \$objCondition = QQ::all();
        \$objClauses = \$this->obj{$strPropName}Clauses;

        \$objClauses[] =
            QQ::expand(QQN::{$strVarType}()->{$strRefPropName}->{$objTable->ClassName}, QQ::equal(QQN::{$strVarType}()->{$strRefPropName}->{$objColumn->PropertyName}, \$this->{$strObjectName}->{$strRefPK}));

        \$obj{$strVarType}Cursor = {$strVarType}::queryCursor(\$objCondition, \$objClauses);

        // Iterate through the Cursor
        while (\${$strRefVarName} = {$strVarType}::instantiateCursor(\$obj{$strVarType}Cursor)) {
            \$objListItem = new ListItem(\${$strRefVarName}->__toString(), \${$strRefVarName}->{$strRefPK}, \${$strRefVarName}->_{$strRefPropName} !== null);
            \$a[] = \$objListItem;
        }
        
        return \$a;
    }


TMPL;
        } else {
            if ($objColumn instanceof SqlColumn) {
                $strRefVarType = $objColumn->Reference->VariableType;
                $strRefVarName = $objColumn->Reference->VariableName;
                //$strRefPropName = $objColumn->Reference->PropertyName;
                $strRefTable = $objColumn->Reference->Table;
            } elseif ($objColumn instanceof ReverseReference) {
                $strRefVarType = $objColumn->VariableType;
                $strRefVarName = $objColumn->VariableName;
                //$strRefPropName = $objColumn->PropertyName;
                $strRefTable = $objColumn->Table;
            }
            $strRet .= <<<TMPL

    /**
     * Retrieves a list of {$strPropName} items based on specified conditions and clauses.
     * This method generates an array of ListItem objects representing {$strPropName} entries,
     * with an appropriate selection state determined by the associated {$objTable->ClassName} object.
     *
     * @return ListItem[] An array of ListItem objects, each representing a {$strPropName} entity.
     * @throws Caller
     * @throws DateMalformedStringException
     * @throws InvalidCast
     */
     public function {$strControlVarName}_GetItems(): array
      {
        \$a = array();
        \$objCondition = \$this->obj{$strPropName}Condition;
        if (is_null(\$objCondition)) \$objCondition = QQ::all();
        \${$strRefVarName}Cursor = {$strRefVarType}::queryCursor(\$objCondition, \$this->obj{$strPropName}Clauses);

        // Iterate through the Cursor
        while (\${$strRefVarName} = {$strRefVarType}::instantiateCursor(\${$strRefVarName}Cursor)) {
            \$objListItem = new ListItem(\${$strRefVarName}->__toString(), \${$strRefVarName}->{$objCodeGen->getTable($strRefTable)->PrimaryKeyColumnArray[0]->PropertyName});
            if ((\$this->{$strObjectName}->{$strPropName}) && (\$this->{$strObjectName}->{$strPropName}->{$objCodeGen->getTable($strRefTable)->PrimaryKeyColumnArray[0]->PropertyName} == \${$strRefVarName}->{$objCodeGen->getTable($strRefTable)->PrimaryKeyColumnArray[0]->PropertyName}))
                \$objListItem->Selected = true;
            \$a[] = \$objListItem;
        }
        
        return \$a;
     }


TMPL;
        }

        return $strRet;
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param ColumnInterface $objColumn
     * @return string
     * @throws InvalidCast
     */
    public function connectorVariableDeclaration(DatabaseCodeGen $objCodeGen, ColumnInterface $objColumn): string
    {
        $strClassName = $objCodeGen->getControlCodeGenerator($objColumn)->getControlClass();
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);

        $strSelectedOut = substr($strClassName, strrpos($strClassName, '\\') + 1);

        $strRet = <<<TMPL
    /**
     * @var {$strSelectedOut}|null
     * @access protected
     */
    protected ?{$strSelectedOut} \${$strControlVarName} = null;

    /**
     * @var string|null 
     * @access protected
     */
    protected ?string \$str{$strPropName}NullLabel = null;


TMPL;

        if (($objColumn instanceof SqlColumn && !$objColumn->Reference->IsType) ||
            ($objColumn instanceof ManyToManyReference && !$objColumn->IsTypeAssociation) ||
            ($objColumn instanceof ReverseReference)
        ) {
            $strRet .= <<<TMPL
    /**
    * @var QQCondition|null
    * @access protected
    */
    protected ?QQCondition \$obj{$strPropName}Condition = null;

    /**
    * @var QQClause|null
    * @access protected
    */
    protected ?QQClause \$obj{$strPropName}Clauses = null;

TMPL;
        }
        return $strRet;
    }

    /**
     * Returns code to refresh the control from the saved object.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @param bool $blnInit
     * @return string
     * @throws Caller
     * @throws InvalidCast
     */
    public function connectorRefresh(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn, ?bool $blnInit = false): string
    {
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);

        $strRet = '';

        if ($blnInit) {
            $strRet .= <<<TMPL

    if (!\$this->str{$strPropName}NullLabel) {
        if (!\$this->{$strControlVarName}->Required) {
            \$this->str{$strPropName}NullLabel = t('- None -');
        }
        elseif (!\$this->blnEditMode) {
            \$this->str{$strPropName}NullLabel = t('- Select One -');
        }
    }

TMPL;
        } else {
        $strRet .= "\$this->{$strControlVarName}->removeAllItems();\n";
        }
        $strRet .= <<<TMPL

    \$this->{$strControlVarName}->addItem(\$this->str{$strPropName}NullLabel, null);

TMPL;

        $options = $objColumn->Options;
        if (!$options || !isset($options['NoAutoLoad'])) {
        $strRet .= "    \$this->{$strControlVarName}->addItems(\$this->{$strControlVarName}_GetItems());\n";
        }

        if ($objColumn instanceof SqlColumn) {
    $strRet .= "    \$this->{$strControlVarName}->SelectedValue = \$this->{$strObjectName}->{$objColumn->PropertyName};\n";
        } elseif ($objColumn instanceof ReverseReference && $objColumn->Unique) {
            $strRet .= "     if (\$this->{$strObjectName}->{$objColumn->ObjectPropertyName})\n";
            $strRet .= _indent("       \$this->{$strControlVarName}->SelectedValue = \$this->{$strObjectName}->{$objColumn->ObjectPropertyName}->{$objCodeGen->getTable($objColumn->Table)->PrimaryKeyColumnArray[0]->PropertyName};\n");
        } elseif ($objColumn instanceof ManyToManyReference) {
            if ($objColumn->IsTypeAssociation) {
                $strRet .= "    \$this->{$strControlVarName}->SelectedValues = array_keys(\$this->{$strObjectName}->get{$objColumn->ObjectDescription}Array());\n";
            }
        }
        if (!$blnInit) {    // wrap it with a test as to whether the control has been created.
            $strRet = _indent($strRet);
            $strRet = <<<TMPL
    if (\$this->{$strControlVarName}) {
     $strRet    }
     

TMPL;
        }
        return _indent($strRet, 2);
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @return string
     * @throws Exception
     */
    public function connectorUpdate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn): string
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);
        $strRet = '';
        if ($objColumn instanceof SqlColumn) {
            $strRet = <<<TMPL
            if (\$this->{$strControlVarName}) \$this->{$strObjectName}->{$objColumn->PropertyName} = \$this->{$strControlVarName}->SelectedValue;

TMPL;
        } elseif ($objColumn instanceof ReverseReference) {
            $strRet = <<<TMPL
            if (\$this->{$strControlVarName}) \$this->{$strObjectName}->{$objColumn->ObjectPropertyName} = {$objColumn->VariableType}::load(\$this->{$strControlVarName}->SelectedValue);

TMPL;
        }
        return $strRet;
    }

    /**
     * Generate helper functions for the update process.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     *
     * @return string
     * @throws Exception
     */
    public function connectorUpdateMethod(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn): string
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);
        $strRet = <<<TMPL

    /**
     * Updates the associations for the current object using selected values from the related control.
     *
     * This method first clears all existing associations, then establishes new associations
     * based on the current selected values in the related control.
     *
     * @return void
     * @throws Caller If the associations cannot be updated due to an invalid call.
     * @throws UndefinedPrimaryKey If the primary key is not defined for the associated object.
     */
    protected function {$strControlVarName}_Update(): void
     {
        if (\$this->{$strControlVarName}) {

TMPL;

        if ($objColumn instanceof ManyToManyReference) {
            if ($objColumn->IsTypeAssociation) {
                $strRet .= <<<TMPL
            \$this->{$strObjectName}->UnassociateAll{$objColumn->ObjectDescriptionPlural}();
            \$this->{$strObjectName}->Associate{$objColumn->ObjectDescription}(\$this->{$strControlVarName}->SelectedValues);

TMPL;
            } else {
                $strRet .= <<<TMPL
            \$this->{$strObjectName}->UnassociateAll{$objColumn->ObjectDescriptionPlural}();
            foreach(\$this->{$strControlVarName}->SelectedValues as \$id) {
                \$this->{$strObjectName}->Associate{$objColumn->ObjectDescription}ByKey(\$id);
            }

TMPL;
            }
        }

        $strRet .= <<<TMPL
        }
    }

TMPL;

        return $strRet;
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @return string
     * @throws InvalidCast
     */
    public function connectorSet(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn): string
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);
        return <<<TMPL
                case '{$strPropName}NullLabel':
                    \$this->str{$strPropName}NullLabel = \$mixValue;
                    break;

TMPL;
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @return string
     * @throws InvalidCast
     */
    public function connectorGet(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn): string
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);
        return <<<TMPL
            case '{$strPropName}NullLabel':
                return \$this->str{$strPropName}NullLabel;

TMPL;
    }
}