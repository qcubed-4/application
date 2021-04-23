<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Codegen\Generator;

use QCubed\Codegen\ColumnInterface;
use QCubed\Codegen\DatabaseCodeGen;
use QCubed\Codegen\ManyToManyReference;
use QCubed\Codegen\ReverseReference;
use QCubed\Codegen\SqlColumn;
use QCubed\Codegen\SqlTable;

/**
 * Class Autocomplete
 *
 * @package QCubed\Codegen\Generator
 * @was QAutocompleteBase_CodeGenerator
 */
class Autocomplete extends TextBox
{
    public function __construct($strControlClassName = 'QCubed\\Project\\Jqui\\Autocomplete')
    {
        parent::__construct($strControlClassName);
    }

    /**
     * @param string $strPropName
     * @return string
     */
    public function varName($strPropName)
    {
        return 'lst' . $strPropName;
    }

    public function connectorImports(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        $a = parent::connectorImports($objCodeGen, $objTable, $objColumn);
        $a[] = ['class'=>'QCubed\\Query\\Condition\\ConditionInterface', 'as'=>'QQCondition'];
        $a[] = ['class'=>'QCubed\\Query\\Clause\\ClauseInterface', 'as'=>'QQClause'];
        $a[] = ['class'=>'QCubed\\Control\\ListItem'];
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
     * @throws \Exception
     * @return string
     */
    public function connectorCreate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        if ($objColumn instanceof ManyToManyReference) {
            throw new Exception("Autocomplete does not support many-to-many references.");
        }

        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strControlVarName = $objCodeGen->modelConnectorVariableName($objColumn);
        $strLabelName = addslashes(DatabaseCodeGen::modelConnectorControlName($objColumn));
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);

        // Read the control type in case we are generating code for a similar class
        $strControlType = $objCodeGen->getControlCodeGenerator($objColumn)->getControlClass();

        // Create a control designed just for selecting from a type table
        if ($objColumn instanceof SqlColumn && $objColumn->Reference->IsType) {
            $strRet = <<<TMPL
    /**
     * Create and setup {$strControlType} {$strControlVarName}
     * @param string \$strControlId optional ControlId to use
     * @return {$strControlType}
     */

    public function {$strControlVarName}_Create(\$strControlId = null) {

TMPL;
        } else {    // Create a control that presents a list taken from the database

            $strRet = <<<TMPL
		/**
		 * Create and setup {$strControlType} {$strControlVarName}
		 * @param null|string \$strControlId optional ControlId to use
		 * @param null|QQCondition \$objConditions override the default condition of QQ::all() to the query, itself
		 * @param null|QQClause[] \$objClauses additional QQClause object or array of QQClause objects for the query
		 * @return {$strControlType}
		 */

		public function {$strControlVarName}_Create(\$strControlId = null, QQCondition \$objCondition = null, \$objClauses = null) {
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
			\$this->{$strControlVarName} = new \\{$strControlType}(\$this->objParentObject, \$strControlId);
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

        if ($objColumn instanceof SqlColumn && $objColumn->Reference->IsType) {
            if ($objColumn instanceof SqlColumn) {
                $strVarType = $objColumn->Reference->VariableType;
            } else {
                $strVarType = $objColumn->VariableType;
            }
            $strRet .= <<<TMPL

		/**
		 *	Create item list for use by {$strControlVarName}
		 */
		public function {$strControlVarName}_GetItems() {
			return {$strVarType}::nameArray();
		}


TMPL;
        } else {
            if ($objColumn instanceof SqlColumn) {
                $strRefVarType = $objColumn->Reference->VariableType;
                $strRefVarName = $objColumn->Reference->VariableName;
                $strRefTable = $objColumn->Reference->Table;
            } elseif ($objColumn instanceof ReverseReference) {
                $strRefVarType = $objColumn->VariableType;
                $strRefVarName = $objColumn->VariableName;
                $strRefTable = $objColumn->Table;
            } else {
                throw new \Exception("Unprepared to handle this column type.");
            }

            $strRet .= <<<TMPL

		/**
		 *	Create item list for use by {$strControlVarName}
		 */
		 public function {$strControlVarName}_GetItems() {
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
     */
    public function connectorVariableDeclaration(DatabaseCodeGen $objCodeGen, ColumnInterface $objColumn)
    {
        $strClassName = $objCodeGen->getControlCodeGenerator($objColumn)->getControlClass();
        $strPropName = $objCodeGen->modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);

        $strRet = <<<TMPL
    /**
     * @var {$strClassName}
     * @access protected
     */
    protected \${$strControlVarName};

    /**
    * @var QQCondition
    * @access protected
    */
    protected \$obj{$strPropName}Condition;

    /**
    * @var QQClause[]
    * @access protected
    */
    protected \$obj{$strPropName}Clauses;

TMPL;

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
     */
    public function connectorRefresh(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn, $blnInit = false)
    {
        $strPrimaryKey = $objCodeGen->getTable($objColumn->Reference->Table)->PrimaryKeyColumnArray[0]->PropertyName;
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strRet = '';

        if (!$blnInit) {
            $t = "\t";    // inserts an extra tab below
            $strRet = <<<TMPL
			if (\$this->{$strControlVarName}) {

TMPL;
        } else {
            $t = '';
        }

        $options = $objColumn->Options;
        if (!$options || !isset($options['NoAutoLoad'])) {
            $strRet .= <<<TMPL
$t			\$this->{$strControlVarName}->Source = \$this->{$strControlVarName}_GetItems();

TMPL;
        }
        $strRet .= <<<TMPL
$t			if (\$this->{$strObjectName}->{$strPropName}) {
$t				\$this->{$strControlVarName}->Text = \$this->{$strObjectName}->{$strPropName}->__toString();
$t				\$this->{$strControlVarName}->SelectedValue = \$this->{$strObjectName}->{$strPropName}->{$strPrimaryKey};
$t			}
$t			else {
$t				\$this->{$strControlVarName}->Text = '';
$t				\$this->{$strControlVarName}->SelectedValue = null;
$t			}

TMPL;

        if (!$blnInit) {
            $strRet .= <<<TMPL
			}

TMPL;
        }
        return $strRet;
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @return string
     */
    public function connectorUpdate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
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
}
