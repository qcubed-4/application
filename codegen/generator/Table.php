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
use Exception;
use QCubed as Q;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;

/**
 * Class Table CodeGenerator
 *
 * This is a base class to support classes that are derived from Table. The methods here support the use
 * of Table-derived classes as a list connector, something that displays a list of records from a database
 * and optionally allows the user to do CRUD operations on individual records.
 *
 * @package QCubed\Codegen\Generator
 */
class Table extends Control implements DataListInterface
{
    /**
     * dtg stands for "DataGrid", a QCubed historical name for tables displaying data. Override if you want something else.
     * @param string $strPropName
     * @return string
     */
    public function varName(string $strPropName): string
    {
        return 'dtg' . $strPropName;
    }

    /**
     * Generates a string containing import statements for the datalist code.
     *
     * @param mixed $objCodeGen The code generator instance being used.
     * @param mixed $objTable The table object for which the datalist is being generated.
     * @return string A string containing the PHP import statements.
     */

    public function dataListImports(mixed $objCodeGen, mixed $objTable): string
    {
        return <<<TMPL
use QCubed\Project\Control\DataGrid;
use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Query\Clause\ClauseInterface as QQClause;
use QCubed\Table\NodeColumn;
use QCubed\Project\Control\ControlBase as QControl;
use QCubed\Project\Control\FormBase as QForm;
use QCubed\Project\Control\Paginator;
use QCubed\Type;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Query\QQ;

TMPL;
    }

    /**
     * Generate the text to insert into the "ConnectorGen" class comments. This would typically be "property" PHPDoc
     * declarations for __get and __set properties declared in the class.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    public function dataListConnectorComments(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        return <<<TMPL
 * @property QQCondition \$Condition Any condition to use during binding
 * @property QQClause \$Clauses Any clauses to use during binding

TMPL;
    }

    /**
     * The main entry point for generating all the "ConnectorGen" code that defines the generated list connector
     * in the generated/connector_base directory.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     * @throws Exception
     */
    public function dataListConnector(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
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
     * @throws Exception
     */
    protected function dataListMembers(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        $strCode = <<<TMPL
    /**
     * @var null|QQCondition Condition to use to filter the list.
     * @access protected
     */
    protected ?QQCondition \$objCondition = null;
    
    /**
     * @var null|QQClause[] Clauses to attach to the query.
     * @access protected
     */
    protected ?array \$objClauses = null;

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
    protected function dataListColumnDeclarations(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
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
    public NodeColumn \${$strColVarName};
    

TMPL;
        }

        foreach ($objTable->ReverseReferenceArray as $objReverseReference) {
            $strColVarName = 'col' . $objReverseReference->ObjectDescription;

            if ($objReverseReference->Unique) {
                $strCode .= <<<TMPL
    /** @var NodeColumn */
    public NodeColumn  \${$strColVarName};

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
    protected function dataListConstructor(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        $strClassName = $this->getControlClass();

        return <<<TMPL
    /**
     * {$strClassName} constructor. The default creates a paginator, sets a default data binder, and sets the grid up
     * watch the data. Columns are set up by the parent control. Feel free to override the constructor to do things differently.
     *
     * @param QControl|QForm \$objParent
     * @param null|string \$strControlId
     * @throws Caller
     */
    public function __construct(QControl|QForm \$objParent, ?string \$strControlId = null) 
    {
        parent::__construct(\$objParent, \$strControlId);
        \$this->createPaginator();
        \$this->setDataBinder('bindData', \$this);
        \$this->watch(QQN::{$objTable->ClassName}());
    }


TMPL;
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    public function dataListCreatePaginator(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        return <<<TMPL
     /**
     * Initializes and sets up the paginator for the control.
     * Configures the paginator instance and assigns the default number of items per a page.
     *
     * @return void
     * @throws Caller
     */
    protected function createPaginator(): void 
    {
        \$this->Paginator = new Paginator(\$this);
        \$this->ItemsPerPage = QCUBED_ITEMS_PER_PAGE;
    }

TMPL;
    }

    /**
     * Creates the columns as part of the datagrid subclass.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     * @throws Exception
     */
    public function dataListCreateColumns(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        $strVarName = $objCodeGen->dataListVarName($objTable);

        $strCode = <<<TMPL
    /**
     * Creates and initializes the columns for the data table,
     * setting up their respective names and associated data nodes.
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function createColumns(): void 
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
    protected function dataListDataBinder(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
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
    *   This binder will automatically add the order and limit clauses from the paginator, if present.
    *
    * @param QQCondition|null \$objAdditionalCondition
    * @param null|array \$objAdditionalClauses
    * @return void
    * @throws Caller
    * @throws InvalidCast
    */
    public function bindData(?QQCondition \$objAdditionalCondition = null, ?array \$objAdditionalClauses = null): void 
    {
        \$objCondition = \$this->getCondition(\$objAdditionalCondition);
        \$objClauses = \$this->getClauses(\$objAdditionalClauses);
    
        if (\$this->Paginator) {
            \$this->TotalItemCount = {$strObjectType}::queryCount(\$objCondition, \$objClauses);
        }
    
        // If a column is selected to be sorted, and if that column has an OrderByClause set on it, then let's add
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
    protected function dataListGetCondition(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        return <<<TMPL
    /**
     * Returns the condition to use when querying the data. The default is to return the condition put in the local
     * objCondition member variable. You can also override this to return a condition. 
     *
     * @param QQCondition|null \$objAdditionalCondition
     * @return QQCondition|null
     * @throws Caller
     */
    protected function getCondition(?QQCondition \$objAdditionalCondition = null): ?QQCondition  
    {
        // Get passed in condition, possibly coming from a subclass or enclosing control or form
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
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    protected function dataListGetClauses(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        return <<<TMPL
    /**
     * Returns the clauses to use when querying the data. The default is to return the clauses put in the local
     * objClauses member variable. You can also override this to return clauses.
     *
     * @param array|null \$objAdditionalClauses
     * @return array|null
     */
    protected function getClauses(?array \$objAdditionalClauses = null): ?array 
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
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    protected function dataListGet(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        return <<<TMPL
    /**
     * This will get the value of \$strName
     *
     * @param string \$strName Name of the property to get
     * @return mixed
     * @throws Caller
     */
    public function __get(string \$strName): mixed 
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
    }

    /**
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     */
    protected function dataListSet(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        return <<<TMPL
    /**
     * This will set the property \$strName to be \$mixValue
     *
     * @param string \$strName Name of the property to set
     * @param mixed \$mixValue New value of the property
     * @throws Caller
     * @throws InvalidCast
     * @throws Throwable
     */
    public function __set(string \$strName, mixed \$mixValue): void 
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
    }

    /**
     * Determines if the data list has filters applied.
     *
     * @return string Returns false indicating no filters are applied.
     */

    public function dataListHasFilter(): string
    {
        return false;
    }

    /**
     * Returns the code that creates the list object. This would be embedded in the pane
     * or form that is using the list object.
     *
     * @param \QCubed\Codegen\DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     * @throws \QCubed\Exception\Caller
     */
    public function dataListInstantiate(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        $strVarName = $objCodeGen->dataListVarName($objTable);

        return <<<TMPL
        \$this->{$strVarName}_Create();

TMPL;
    }

    /**
     * Generate the code that refreshes the control after a change in the filter. The default redraws the entire control.
     * If your control can refresh just a part of itself, insert that code here.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     * @throws \QCubed\Exception\Caller
     */
    public function dataListRefresh(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        $strVarName = $objCodeGen->dataListVarName($objTable);
        return <<<TMPL
        \$this->{$strVarName}->refresh();

TMPL;
    }

    /**
     * Generate additional methods for the enclosing control to interact with this generated control.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     * @throws \QCubed\Exception\Caller
     */
    public function dataListHelperMethods(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
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
     * @throws \QCubed\Exception\Caller
     */
    protected function dataListParentCreate(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        $strPropertyName = $objCodeGen->dataListPropertyName($objTable);
        $strVarName = $objCodeGen->dataListVarName($objTable);

        $strCode = <<<TMPL
   /**
    * Creates the data grid and prepares it to be row-clickable. Override for additional creation operations.
    *
    * @return void
    * @throws Caller
    **/
    protected function {$strVarName}_Create(): void 
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
     * @throws \QCubed\Exception\Caller
     */
    protected function dataListParentCreateColumns(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        $strVarName = $objCodeGen->dataListVarName($objTable);

        return <<<TMPL

   /**
    * Calls the list connector to add the columns. Override to customize column creation.
    *
    * @return void
    * @throws Caller
    */
    protected function {$strVarName}_CreateColumns(): void 
    {
        \$this->{$strVarName}->createColumns();
    }

TMPL;
    }

    /**
     * Generates a typical action to respond to row clicks.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     * @throws \QCubed\Exception\Caller
     */
    protected function dataListParentMakeEditable(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        $strVarName = $objCodeGen->dataListVarName($objTable);

        return <<<TMPL

    /**
     * Make the datagrid editable
     *
     * @return void
     * @throws Caller
     */
    protected function {$strVarName}_MakeEditable(): void 
    {
        \$this->{$strVarName}->addAction(new CellClick(0, null, null, true), new AjaxControl(\$this, '{$strVarName}_CellClick', null, null, CellClick::ROW_VALUE));
        \$this->{$strVarName}->addCssClass('clickable-rows');
    }

    /**
     * Respond to a cell click
     * @param string \$strFormId The form id
     * @param string \$strControlId The control id of the control clicked on.
     * @param string \$param Params coming from the cell click. In this situation, it is a string containing the id of row clicked.
     *
     * @return void
     * @throws Throwable
     */
    protected function {$strVarName}_CellClick(string \$strFormId, string \$strControlId, string \$param): void 
    {
        if (\$param) {
            \$this->editItem(\$param);
        }
    }

TMPL;
    }

    /**
     * Generates the row param callback that will enable row clicks to know what row was clicked on.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     * @throws \QCubed\Exception\Caller
     */
    protected function dataListGetRowParams(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        $strVarName = $objCodeGen->dataListVarName($objTable);

        return <<<TMPL

    /**
     * Get row parameters for the row tag
     * 
     * @param mixed \$objRowObject   A database object
     * @param int \$intRowIndex      The row index
     * @return array
     */
    public function {$strVarName}_GetRowParams(mixed \$objRowObject, int \$intRowIndex): array 
    {
        \$strKey = \$objRowObject->primaryKey();
        \$params['data-value'] = \$strKey;
        return \$params;
    }
TMPL;
    }

    /***
     * Parent SUBCLASS
     * Generator code for the parent subclass. The subclass is a first-time generation only.
     ****/

    /**
     * Generates an alternate create columns function that could be used by the list panel to create the columns directly.
     * This is designed to be added as commented our code in the list panel override class that the user can choose to use.
     *
     * @param DatabaseCodeGen $objCodeGen
     * @param SqlTable $objTable
     * @return string
     * @throws \QCubed\Exception\Caller
     * @throws \QCubed\Exception\InvalidCast
     */
    public function dataListSubclassOverrides(DatabaseCodeGen $objCodeGen, SqlTable $objTable): string
    {
        $strVarName = $objCodeGen->dataListVarName($objTable);
        $strPropertyName = DatabaseCodeGen::dataListPropertyName($objTable);

        $strCode = <<<TMPL
/*
     Uncomment this block to directly create the columns here, rather than creating them in the {$strPropertyName}List connector.
     You can then modify the column creation process by editing the function below. Or, you can instead call the parent function 
     and modify the columns after the {$strPropertyName}List creates the default columns.
    
    protected function {$strVarName}_CreateColumns(): void 
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
    
        protected function {$strVarName}_MakeEditable(): void 
        {
            \$this->>pxyEditRow = new Proxy(\$this);
            \$this->>pxyEditRow->addAction(new Click(), new AjaxControl(\$this, '{$strVarName}_EditClick'));
            \$this->{$strVarName}->createLinkColumn(t('Edit'), t('Edit'), \$this->>pxyEditRow, QQN::{$objTable->ClassName}()->Id, null, false, 0);
            \$this->{$strVarName}->removeCssClass('clickable-rows');
        }
    
        protected function {$strVarName}_EditClick(string \$strFormId, string \$strControlId, string \$param): void 
        {
            \$this->editItem(\$param);
        }
*/

TMPL;

        return $strCode;
    }
}