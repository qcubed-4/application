<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Codegen\Generator;

use QCubed\Codegen\SqlTable;
use QCubed\Codegen\DatabaseCodeGen;
use QCubed as Q;

/**
 * Class QHtmlTable_CodeGenerator
 *
 * This is a base class to support classes that are derived from QHtmlTable. The methods here support the use
 * of QHtmlTable derived classes as a list connector, something that displays a list of records from a database,
 * and optionally allows the user to do CRUD operations on individual records.
 *
 * @package QCubed\Codegen\Generator
 * @was QHtmlTable_CodeGenerator
 */
class Table extends Control implements DataListInterface
{

    /**
     * dtg stands for "DataGrid", a QCubed historical name for tables displaying data. Override if you want something else.
     * @param string $strPropName
     * @return string
     */
    public function varName($strPropName)
    {
        return 'dtg' . $strPropName;
    }

    
    /****
     * CONNECTOR GEN
     * The following functions generate the ListGen code that will go into the generated/connector_base directory
     *******/

    public function dataListImports($objCodeGen, $objTable) {
        $strCode = <<<TMPL
use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Query\Clause\ClauseInterface as QQClause;
use QCubed\Table\NodeColumn;
use QCubed\Project\Control\ControlBase as QControl;
use QCubed\Project\Control\FormBase as QForm;
use QCubed\Project\Control\Paginator;
use QCubed\Type;
use QCubed\Exception\Caller;
use QCubed\Query\QQ;


TMPL;
        return $strCode;
    }

    /**
     * Generate the text to insert into the "ConnectorGen" class comments. This would typically be "property" PHPDoc
     * declarations for __get and __set properties declared in the class.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    public function dataListConnectorComments(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strCode = <<<TMPL
 * @property QQCondition 	\$Condition Any condition to use during binding
 * @property QQClause 		\$Clauses Any clauses to use during binding

TMPL;
        return $strCode;
    }


    /**
     * The main entry point for generating all the "ConnectorGen" code that defines the generated list connector
     * in the generated/connector_base directory.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    public function dataListConnector(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strCode = $this->dataListMembers($objCodeGen, $objTable);
        $strCode .= $this->dataListConstructor($objCodeGen, $objTable);
        $strCode .= $this->dataListCreatePaginator($objCodeGen, $objTable);
        $strCode .= $this->dataListCreateColumns($objCodeGen, $objTable);
        $strCode .= $this->dataListDataBinder($objCodeGen, $objTable);
        $strCode .= $this->dataListGet($objCodeGen, $objTable);
        $strCode .= $this->dataListSet($objCodeGen, $objTable);

        return $strCode;
    }

    /**
     * Generate the member variables for the "ConnectorGen" class.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    protected function dataListMembers(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strCode = <<<TMPL
	/**
	 * @var null|QQCondition	Condition to use to filter the list.
	 * @access protected
	 */
	protected \$objCondition;

	/**
	 * @var null|QQClause[]		Clauses to attach to the query.
	 * @access protected
	 */
	protected \$objClauses;


TMPL;
        $strCode .= $this->dataListColumnDeclarations($objCodeGen, $objTable);
        return $strCode;
    }

    /**
     * Generate member variables for the columns that will be created later. This implementation makes the columns
     * public so that classes can easily manipulate the columns further after construction.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     * @throws Exception
     */
    protected function dataListColumnDeclarations(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strCode = <<<TMPL
	// Publicly accessible columns that allow parent controls to directly manipulate them after creation.

TMPL;
        foreach ($objTable->ColumnArray as $objColumn) {
            if (isset($objColumn->Options['FormGen']) && ($objColumn->Options['FormGen'] == Q\ModelConnector\Options::FORMGEN_NONE)) {
                continue;
            }
            if (isset($objColumn->Options['NoColumn']) && $objColumn->Options['NoColumn']) {
                continue;
            }
            $strColVarName = 'col' . $objCodeGen->modelConnectorPropertyName($objColumn);
            $strCode .= <<<TMPL
	/** @var NodeColumn */
	public \${$strColVarName};

TMPL;
        }

        foreach ($objTable->ReverseReferenceArray as $objReverseReference) {
            $strColVarName = 'col' . $objReverseReference->ObjectDescription;

            if ($objReverseReference->Unique) {
                $strCode .= <<<TMPL
	/** @var NodeColumn */
	public \${$strColVarName};

TMPL;
            }
        }
        $strCode .= "\n";
        return $strCode;
    }

    /**
     * Generate a constructor for a subclass of itself.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    protected function dataListConstructor(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strClassName = $this->getControlClass();

        $strCode = <<<TMPL

	/**
	 * {$strClassName} constructor. The default creates a paginator, sets a default data binder, and sets the grid up
	 * watch the data. Columns are set up by the parent control. Feel free to override the constructor to do things differently.
	 *
	 * @param QControl|QForm \$objParent
	 * @param null|string \$strControlId
	 */
	public function __construct(\$objParent, \$strControlId = null) 
	{
		parent::__construct(\$objParent, \$strControlId);
		\$this->createPaginator();
		\$this->setDataBinder('bindData', \$this);
		\$this->watch(QQN::{$objTable->ClassName}());
	}


TMPL;
        return $strCode;
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    public function dataListCreatePaginator(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strCode = <<<TMPL
	/**
	 * Creates the paginator. Override to add an additional paginator, or to remove it.
	 */
	protected function createPaginator() 
	{
		\$this->Paginator = new Paginator(\$this);
		\$this->ItemsPerPage = QCUBED_ITEMS_PER_PAGE;
	}

TMPL;
        return $strCode;
    }

    /**
     * Creates the columns as part of the datagrid subclass.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     * @throws Exception
     */
    public function dataListCreateColumns(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strVarName = $objCodeGen->dataListVarName($objTable);

        $strCode = <<<TMPL
	/**
	 * Creates the columns for the table. Override to customize, or use the ModelConnectorEditor to turn on and off 
	 * individual columns. This is a public function and called by the parent control.
	 */
	public function createColumns() 
	{

TMPL;

        foreach ($objTable->ColumnArray as $objColumn) {
            if (isset($objColumn->Options['FormGen']) && ($objColumn->Options['FormGen'] == Q\ModelConnector\Options::FORMGEN_NONE)) {
                continue;
            }
            if (isset($objColumn->Options['NoColumn']) && $objColumn->Options['NoColumn']) {
                continue;
            }

            $strCode .= <<<TMPL
		\$this->col{$objCodeGen->modelConnectorPropertyName($objColumn)} = \$this->createNodeColumn("{$objCodeGen->modelConnectorControlName($objColumn)}", QQN::{$objTable->ClassName}()->{$objCodeGen->modelConnectorPropertyName($objColumn)});

TMPL;
        }

        foreach ($objTable->ReverseReferenceArray as $objReverseReference) {
            if ($objReverseReference->Unique) {
                $strCode .= <<<TMPL
		\$this->col{$objReverseReference->ObjectDescription} = \$this->createNodeColumn("{$objCodeGen->modelConnectorControlName($objReverseReference)}", QQN::{$objTable->ClassName}()->{$objReverseReference->ObjectDescription});

TMPL;
            }
        }

        $strCode .= <<<TMPL
	}


TMPL;

        return $strCode;
    }


    /**
     * Generates a data binder that can be called from the parent control, or called directly by this control.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    protected function dataListDataBinder(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strObjectType = $objTable->ClassName;
        $strCode = <<<TMPL
   /**
	* Called by the framework to access the data for the control and load it into the table. By default, this function will be
	* the data binder for the control, with no additional conditions or clauses. To change what data is displayed in the list,
	* you have many options:
	* - Override this method in the Connector.
	* - Set ->Condition and ->Clauses properties for semi-permanent conditions and clauses
	* - Override the GetCondition and GetClauses methods in the Connector.
	* - For situations where the data might change every time you draw, like if the data is filtered by other controls,
	*   you should call SetDataBinder after the parent creates this control, and in your custom data binder, call this function,
	*   passing in the conditions and clauses you want this data binder to use.
	*
	*	This binder will automatically add the orderby and limit clauses from the paginator, if present.
	*
	* @param QQCondition|null \$objAdditionalCondition
    * @param null|array \$objAdditionalClauses
	*/
	public function bindData(QQCondition \$objAdditionalCondition = null, \$objAdditionalClauses = null) 
	{
		\$objCondition = \$this->getCondition(\$objAdditionalCondition);
		\$objClauses = \$this->getClauses(\$objAdditionalClauses);

		if (\$this->Paginator) {
			\$this->TotalItemCount = {$strObjectType}::queryCount(\$objCondition, \$objClauses);
		}

		// If a column is selected to be sorted, and if that column has a OrderByClause set on it, then let's add
		// the OrderByClause to the \$objClauses array
		if (\$objClause = \$this->OrderByClause) {
			\$objClauses[] = \$objClause;
		}

		// Add the LimitClause information, as well
		if (\$objClause = \$this->LimitClause) {
			\$objClauses[] = \$objClause;
		}

		\$this->DataSource = {$strObjectType}::queryArray(\$objCondition, \$objClauses);
	}


TMPL;

        $strCode .= $this->dataListGetCondition($objCodeGen, $objTable);
        $strCode .= $this->dataListGetClauses($objCodeGen, $objTable);

        return $strCode;
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    protected function dataListGetCondition(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strCode = <<<TMPL
	/**
	 * Returns the condition to use when querying the data. Default is to return the condition put in the local
	 * objCondition member variable. You can also override this to return a condition. 
	 *
	 * @param QQCondition|null \$objAdditionalCondition
	 * @return QQCondition
	 */
	protected function getCondition(QQCondition \$objAdditionalCondition = null) 
	{
		// Get passed in condition, possibly coming from subclass or enclosing control or form
		\$objCondition = \$objAdditionalCondition;
		if (!\$objCondition) {
			\$objCondition = QQ::all();
		}
		// Get condition more permanently bound
		if (\$this->objCondition) {
			\$objCondition = QQ::andCondition(\$objCondition, \$this->objCondition);
		}

		return \$objCondition;
	}


TMPL;
        return $strCode;
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    protected function dataListGetClauses(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strCode = <<<TMPL
	/**
	 * Returns the clauses to use when querying the data. Default is to return the clauses put in the local
	 * objClauses member variable. You can also override this to return clauses.
	 *
	 * @param array|null \$objAdditionalClauses
	 * @return QQClause[]
	 */
	protected function getClauses(\$objAdditionalClauses = null) 
	{
		\$objClauses = \$objAdditionalClauses;
		if (!\$objClauses) {
			\$objClauses = [];
		}
		if (\$this->objClauses) {
			\$objClauses = array_merge(\$objClauses, \$this->objClauses);
		}

		return \$objClauses;
	}


TMPL;
        return $strCode;
    }


    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    protected function dataListGet(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strCode = <<<TMPL
	/**
	 * This will get the value of \$strName
	 *
	 * @param string \$strName Name of the property to get
	 * @return mixed
	 * @throws Caller
	 */
	public function __get(\$strName) 
	{
		switch (\$strName) {
			case 'Condition':
				return \$this->objCondition;
			case 'Clauses':
				return \$this->objClauses;
			default:
				try {
					return parent::__get(\$strName);
				} catch (Caller \$objExc) {
					\$objExc->incrementOffset();
					throw \$objExc;
				}
		}
	}


TMPL;
        return $strCode;
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    protected function dataListSet(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strCode = <<<TMPL
	/**
	 * This will set the property \$strName to be \$mixValue
	 *
	 * @param string \$strName Name of the property to set
	 * @param string \$mixValue New value of the property
	 * @return void
	 * @throws Caller
	 */
	public function __set(\$strName, \$mixValue) 
	{
		switch (\$strName) {
			case 'Condition':
				try {
					\$this->objCondition = Type::cast(\$mixValue, '\\QCubed\\Query\\Condition\\ConditionInterface');
					\$this->markAsModified();
				} catch (Caller \$objExc) {
					\$objExc->incrementOffset();
					throw \$objExc;
				}
				break;
			case 'Clauses':
				try {
					\$this->objClauses = Type::cast(\$mixValue, Type::ARRAY_TYPE);
					\$this->markAsModified();
				} catch (Caller \$objExc) {
					\$objExc->incrementOffset();
					throw \$objExc;
				}
				break;
			default:
				try {
					parent::__set(\$strName, \$mixValue);
					break;
				} catch (Caller \$objExc) {
					\$objExc->incrementOffset();
					throw \$objExc;
				}
		}
	}


TMPL;
        return $strCode;
    }



    /****
     * Parent Gen
     * The following functions generate code that is to be used by the parent object to instantiate and initialize this object.
     *****/

    /**
     * Return true if the data list has its own build-in filter. False will mean that a filter field will be created
     * by default. This is still controllable by the model connector.
     *
     * @return bool
     */
    public function dataListHasFilter()
    {
        return false;
    }

    /**
     * Returns the code that creates the list object. This would be embedded in the pane
     * or form that is using the list object.
     *
     * @param SqlTable $objTable
     * @return mixed
     */
    public function dataListInstantiate(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strVarName = $objCodeGen->dataListVarName($objTable);

        $strCode = <<<TMPL
		\$this->{$strVarName}_Create();

TMPL;
        return $strCode;
    }

    /**
     * Generate the code that refreshes the control after a change in the filter. The default redraws the entire control.
     * If your control can refresh just a part of itself, insert that code here.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    public function dataListRefresh(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strVarName = $objCodeGen->dataListVarName($objTable);
        $strCode = <<<TMPL
		\$this->{$strVarName}->refresh();

TMPL;
        return $strCode;
    }

    /**
     * Generate additional methods for the enclosing control to interact with this generated control.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    public function dataListHelperMethods(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strCode = $this->dataListParentCreate($objCodeGen, $objTable);
        $strCode .= $this->dataListParentCreateColumns($objCodeGen, $objTable);
        $strCode .= $this->dataListParentMakeEditable($objCodeGen, $objTable);
        $strCode .= $this->dataListGetRowParams($objCodeGen, $objTable);

        return $strCode;
    }


    /**
     * Generates code for the enclosing control to create this control.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    protected function dataListParentCreate(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strPropertyName = $objCodeGen->dataListPropertyName($objTable);
        $strVarName = $objCodeGen->dataListVarName($objTable);

        $strCode = <<<TMPL
   /**
	* Creates the data grid and prepares it to be row clickable. Override for additional creation operations.
	**/
	protected function {$strVarName}_Create() 
	{
		\$this->{$strVarName} = new {$strPropertyName}List(\$this);
		\$this->{$strVarName}_CreateColumns();
		\$this->{$strVarName}_MakeEditable();
		\$this->{$strVarName}->RowParamsCallback = [\$this, "{$strVarName}_GetRowParams"];

TMPL;

        if (($o = $objTable->Options) && isset($o['Name'])) { // Did developer default?
            $strCode .= <<<TMPL
		\$this->{$strVarName}->Name = "{$o['Name']}";

TMPL;
        }

        // Add options coming from the config file, including the LinkedNode
        $strCode .= $this->connectorCreateOptions($objCodeGen, $objTable, null, $strVarName);

        $strCode .= <<<TMPL
	}

TMPL;
        return $strCode;
    }

    /**
     * Generates a function to add columns to the list.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    protected function dataListParentCreateColumns(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strVarName = $objCodeGen->dataListVarName($objTable);

        $strCode = <<<TMPL

   /**
	* Calls the list connector to add the columns. Override to customize column creation.
	**/
	protected function {$strVarName}_CreateColumns() 
	{
		\$this->{$strVarName}->createColumns();
	}

TMPL;

        return $strCode;
    }

    /**
     * Generates a typical action to respond to row clicks.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    protected function dataListParentMakeEditable(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strVarName = $objCodeGen->dataListVarName($objTable);

        $strCode = <<<TMPL
    /**
     * Make the datagrid editable
     */
	protected function {$strVarName}_MakeEditable() 
	{
		\$this->{$strVarName}->addAction(new Q\\Event\\CellClick(0, null, null, true), new Q\\Action\\AjaxControl(\$this, '{$strVarName}_CellClick', null, null, Q\\Event\\CellClick::ROW_VALUE));
		\$this->{$strVarName}->addCssClass('clickable-rows');
	}

    /**
     * Respond to a cell click
     * @param string \$strFormId The form id
     * @param string \$strControlId The control id of the control clicked on.
     * @param mixed \$param Params coming from the cell click. In this situations, it is a string containing the id of row clicked.
     */
	protected function {$strVarName}_CellClick(\$strFormId, \$strControlId, \$param) 
	{
		if (\$param) {
			\$this->editItem(\$param);
		}
	}

TMPL;

        return $strCode;
    }

    /**
     * Generates the row param callback that will enable row clicks to know what row was clicked on.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    protected function dataListGetRowParams(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strVarName = $objCodeGen->dataListVarName($objTable);

        $strCode = <<<TMPL
    /**
     * Get row parameters for the row tag
     * 
     * @param mixed \$objRowObject   A database object
     * @param int \$intRowIndex      The row index
     * @return array
     */
	public function {$strVarName}_GetRowParams(\$objRowObject, \$intRowIndex) 
	{
		\$strKey = \$objRowObject->primaryKey();
		\$params['data-value'] = \$strKey;
		return \$params;
	}
TMPL;

        return $strCode;
    }


    /***
     * Parent SUBCLASS
     * Generator code for the parent subclass. The subclass is a first-time generation only.
     ****/

    
    /**
     * Generates an alternate create columns function that could be used by the list panel to create the columns directly.
     * This is designed to be added as commented out code in the list panel override class that the user can choose to use.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    public function dataListSubclassOverrides(DatabaseCodeGen $objCodeGen, SqlTable $objTable)
    {
        $strVarName = $objCodeGen->dataListVarName($objTable);
        $strPropertyName = DatabaseCodeGen::dataListPropertyName($objTable);

        $strCode = <<<TMPL
/*
	 Uncomment this block to directly create the columns here, rather than creating them in the {$strPropertyName}List connector.
	 You can then modify the column creation process by editing the function below. Or, you can instead call the parent function 
	 and modify the columns after the {$strPropertyName}List creates the default columns.

	protected function {$strVarName}_CreateColumns() 
	{

TMPL;

        foreach ($objTable->ColumnArray as $objColumn) {
            if (isset($objColumn->Options['FormGen']) && ($objColumn->Options['FormGen'] == Q\ModelConnector\Options::FORMGEN_NONE)) {
                continue;
            }
            if (isset($objColumn->Options['NoColumn']) && $objColumn->Options['NoColumn']) {
                continue;
            }

            $strCode .= <<<TMPL
		\$col = \$this->{$strVarName}->createNodeColumn("{$objCodeGen->modelConnectorControlName($objColumn)}", QQN::{$objTable->ClassName}()->{$objCodeGen->modelConnectorPropertyName($objColumn)});

TMPL;
        }

        foreach ($objTable->ReverseReferenceArray as $objReverseReference) {
            if ($objReverseReference->Unique) {
                $strCode .= <<<TMPL
		\$col = \$this->{$strVarName}->createNodeColumn("{$objCodeGen->modelConnectorControlName($objReverseReference)}", QQN::{$objTable->ClassName}()->{$objReverseReference->ObjectDescription});

TMPL;
            }
        }

        $strCode .= <<<TMPL
	}

*/	

TMPL;

        $strCode .= <<<TMPL
		
/*
	 Uncomment this block to use an Edit column instead of clicking on a highlighted row in order to edit an item.

		protected \$pxyEditRow;

		protected function {$strVarName}_MakeEditable () 
		{
			\$this->>pxyEditRow = new \\QCubed\\Control\\Proxy(\$this);
			\$this->>pxyEditRow->addAction(new \\QCubed\\Event\\Click(), new \\QCubed\\Action\\AjaxControl(\$this, '{$strVarName}_EditClick'));
			\$this->{$strVarName}->createLinkColumn(t('Edit'), t('Edit'), \$this->>pxyEditRow, QQN::{$objTable->ClassName}()->Id, null, false, 0);
			\$this->{$strVarName}->removeCssClass('clickable-rows');
		}

		protected function {$strVarName}_EditClick(\$strFormId, \$strControlId, \$param) 
		{
			\$this->editItem(\$param);
		}
*/	

TMPL;

        return $strCode;
    }
}
