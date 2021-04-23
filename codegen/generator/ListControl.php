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
 * Class ListControl
 *
 * @package QCubed\Codegen\Generator
 * @was QListControlBase_CodeGenerator
 */
class ListControl extends Control
{
    public function __construct($strControlClassName = 'QCubed\\Control\\ListControl')
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
        $a[] = ['class'=>'QCubed\\Control\\ListControl'];
        $a[] = ['class'=>'QCubed\\Project\\Control\\ListBox'];
        $a[] = ['class'=>'QCubed\\Control\\ListItem'];
        $a[] = ['class'=>'QCubed\\Query\\Condition\\ConditionInterface', 'as'=>'QQCondition'];
        $a[] = ['class'=>'QCubed\\Query\\Clause\\ClauseInterface', 'as'=>'QQClause'];
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
     */
    public function connectorCreate(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strControlVarName = $objCodeGen->modelConnectorVariableName($objColumn);
        $strLabelName = addslashes(DatabaseCodeGen::modelConnectorControlName($objColumn));
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);

        // Read the control type in case we are generating code for a similar class
        $strControlType = $objCodeGen->getControlCodeGenerator($objColumn)->getControlClass();

        // Create a control designed just for selecting from a type table
        if (($objColumn instanceof SqlColumn && $objColumn->Reference->IsType) ||
            ($objColumn instanceof ManyToManyReference && $objColumn->IsTypeAssociation)
        ) {
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
		 * @param string \$strControlId optional ControlId to use
		 * @param QQCondition \$objCondition override the default condition of QQ::all() to the query, itself
		 * @param QQClause[] \$objClauses additional QQClause object or array of QQClause objects for the query
		 * @return ListBox
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

        if ($objColumn instanceof SqlColumn && $objColumn->Reference->IsType ||
            $objColumn instanceof ManyToManyReference && $objColumn->IsTypeAssociation
        ) {
            if ($objColumn instanceof SqlColumn) {
                $strVarType = $objColumn->Reference->VariableType;
            } else {
                $strVarType = $objColumn->VariableType;
            }
            $strRefVarName = null;
            $strRet .= <<<TMPL

		/**
		 *	Create item list for use by {$strControlVarName}
		 */
		public function {$strControlVarName}_GetItems() {
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
		 *	Create item list for use by {$strControlVarName}
		 */
		public function {$strControlVarName}_GetItems() {
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
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);

        $strRet = <<<TMPL
    /**
     * @var {$strClassName}
     * @access protected
     */
    protected \${$strControlVarName};

    /**
     * @var string 
     * @access protected
     */
    protected \$str{$strPropName}NullLabel;


TMPL;

        if (($objColumn instanceof SqlColumn && !$objColumn->Reference->IsType) ||
            ($objColumn instanceof ManyToManyReference && !$objColumn->IsTypeAssociation) ||
            ($objColumn instanceof ReverseReference)
        ) {
            $strRet .= <<<TMPL
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
     */
    public function connectorRefresh(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn, $blnInit = false)
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
            $strRet .= "\$this->{$strControlVarName}->addItems(\$this->{$strControlVarName}_GetItems());\n";
        }

        if ($objColumn instanceof SqlColumn) {
            $strRet .= "\$this->{$strControlVarName}->SelectedValue = \$this->{$strObjectName}->{$objColumn->PropertyName};\n";
        } elseif ($objColumn instanceof ReverseReference && $objColumn->Unique) {
            $strRet .= "if (\$this->{$strObjectName}->{$objColumn->ObjectPropertyName})\n";
            $strRet .= _indent("\$this->{$strControlVarName}->SelectedValue = \$this->{$strObjectName}->{$objColumn->ObjectPropertyName}->{$objCodeGen->getTable($objColumn->Table)->PrimaryKeyColumnArray[0]->PropertyName};\n");
        } elseif ($objColumn instanceof ManyToManyReference) {
            if ($objColumn->IsTypeAssociation) {
                $strRet .= "\$this->{$strControlVarName}->SelectedValues = array_keys(\$this->{$strObjectName}->Get{$objColumn->ObjectDescription}Array());\n";
            } else {
                //$strRet .= $strTabs . "\$this->{$strControlVarName}->SelectedValues = \$this->{$strObjectName}->Get{$objColumn->ObjectDescription}Keys();\n";
            }
        }
        if (!$blnInit) {    // wrap it with a test as to whether the control has been created.
            $strRet = _indent($strRet);
            $strRet = <<<TMPL
if (\$this->{$strControlVarName}) {
$strRet
}

TMPL;
        }
        $strRet = _indent($strRet, 3);
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

    /**
     * Generate helper functions for the update process.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     *
     * @return string
     */
    public function connectorUpdateMethod(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);
        $strRet = <<<TMPL
		protected function {$strControlVarName}_Update() {
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
     */
    public function connectorSet(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);
        $strRet = <<<TMPL
                case '{$strPropName}NullLabel':
                    \$this->str{$strPropName}NullLabel = \$mixValue;
                    break;

TMPL;
        return $strRet;
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @param ColumnInterface $objColumn
     * @return string
     */
    public function connectorGet(DatabaseCodeGen $objCodeGen, SqlTable $objTable, ColumnInterface $objColumn)
    {
        $strObjectName = $objCodeGen->modelVariableName($objTable->Name);
        $strPropName = DatabaseCodeGen::modelConnectorPropertyName($objColumn);
        $strControlVarName = $this->varName($strPropName);
        $strRet = <<<TMPL
            case '{$strPropName}NullLabel':
                return \$this->str{$strPropName}NullLabel;

TMPL;
        return $strRet;
    }
}
