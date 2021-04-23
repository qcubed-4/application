<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\ModelConnector;

use QCubed\Action\AjaxControl;
use QCubed\Codegen\ControlCategoryType;
use QCubed\Table\CallableColumn;
use QCubed\Project\Application;
use QCubed\Jqui\DialogBase as QDialog;
use QCubed\Project\Control\ControlBase as QControl;
use QCubed\Event;
use QCubed\Control\Panel;
use QCubed\ModelConnector\Param as QModelConnectorParam;
use QCubed\ModelConnector\Options as QModelConnectorOptions;
use QCubed\Project\Control\Table;
use QCubed\Project\Jqui\Tabs;
use QCubed\Type;
use QCubed\Project\Codegen\CodegenBase as QCodeGen;

/**
 * Class EditDlg
 *
 * A dialog that lets you specify code generation options for a control. These options control how a control
 * is generated, and includes additional parameters that can be specified for a control.
 *
 * This dialog pops up when designer mode is turned on and the user right clicks on a control.
 *
 * The code below will set up the dialog and display options that are generic to all QControls. Individual
 * controls can add parameters to this dialog by implementing the GetModelConnectorParams function.
 *
 * Everything gets saved in the configuration/codegen_options.json file.
 *
 * @was QModelConnectorEditDlg
 * @package QCubed\ModelConnector
 */
class EditDlg extends QDialog
{
    /** @var  QControl */
    protected $objCurrentControl;
    /** @var Tabs */
    protected $tabs;

    protected $txtName;
    protected $txtControlId;
    protected $txtControlClass;
    protected $lstFormGen;

    protected $params;
    protected $objModelConnectorOptions;

    /** @var   QModelConnectorParam[] */
    protected $generalOptions;
    /** @var  Table */
    protected $dtgGeneralOptions;

    /** @var  QModelConnectorParam[][] */
    protected $categories;

    protected $datagrids;

    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->AutoRenderChildren = true;
        $this->Width = 750;

        $this->objModelConnectorOptions = new QModelConnectorOptions();

        $objPanel = new Panel($this);    // This is a kind of bug fix, to fix a unique problem when putting tabs in a dialog
        $objPanel->Height = 400;    // We need an intermediate object to force the height
        $objPanel->AutoRenderChildren = true;

        $this->tabs = new Tabs($objPanel, 'qconnectoreditTabs');
        $this->tabs->HeightStyle = "content";

        $this->addButton('Save', 'save');
        $this->addButton('Save, Regenerate and Reload', 'saveRefresh');
        $this->addButton('Cancel', 'cancel');

        $this->addAction(new Event\DialogButton, new AjaxControl($this, 'ButtonClick'));
    }

    /**
     * Recreate the tabs in the dialog
     */
    protected function setupTabs()
    {
        $strClassNames = $this->createClassNameArray();
        $this->tabs->removeChildControls(true);
        $this->categories = array();

        $this->dtgGeneralOptions = new Table($this->tabs);
        $this->dtgGeneralOptions->ShowHeader = false;
        $this->dtgGeneralOptions->Name = "General";
        $this->dtgGeneralOptions->createPropertyColumn('Attribute', 'Name');
        $col = $this->dtgGeneralOptions->addColumn(new CallableColumn('Attribute',
            array($this, 'dtg_ValueRender'), $this->dtgGeneralOptions));
        $col->HtmlEntities = false;
        $this->dtgGeneralOptions->setDataBinder('dtgGeneralOptions_Bind', $this);

        /**
         * The following default options are somewhat matched to the default list and edit templates. A more robust
         * implementation would get the options from the templates, or what the templates generate, so that the templates
         * decide what to put there. If someone wants to radically change the templates, but still have them use this dialog
         * to edit the options, then would be the time to change the code below.
         */
        if ($this->objCurrentControl->LinkedNode->_ParentNode) {
            // Specify general options for a database column
            $this->generalOptions = array(
                new QModelConnectorParam(QModelConnectorParam::GENERAL_CATEGORY, 'ControlClass',
                    'Override of the PHP type for the control. If you change this, save the dialog and reopen to reload the tabs to show the control specific options.',
                    QModelConnectorParam::SELECTION_LIST, $strClassNames),
                new QModelConnectorParam(QModelConnectorParam::GENERAL_CATEGORY, 'FormGen',
                    'Whether or not to generate this object, just a label for the object, just the control, or both the control and label',
                    QModelConnectorParam::SELECTION_LIST,
                    array(
                        QModelConnectorOptions::FORMGEN_BOTH => 'Both',
                        QModelConnectorOptions::FORMGEN_NONE => 'None',
                        QModelConnectorOptions::FORMGEN_CONTROL_ONLY => 'Control',
                        QModelConnectorOptions::FORMGEN_LABEL_ONLY => 'Label'
                    )),
                new QModelConnectorParam(QModelConnectorParam::GENERAL_CATEGORY, 'Name', 'Control\'s Name',
                    Type::STRING, ["translate"=>true]),
                new QModelConnectorParam(QModelConnectorParam::GENERAL_CATEGORY, 'NoColumn',
                    'True to prevent a column in the lister from being generated.', Type::BOOLEAN)
            );
        } else {
            // Specify general options for a database table, meaning an object that is listing the content of a whole table.
            // These would be options at a higher level than the control itself, and would modify how the control is used in a form.
            $this->generalOptions = array(
                new QModelConnectorParam(QModelConnectorParam::GENERAL_CATEGORY, 'ControlClass',
                    'Override of the PHP type for the control. If you change this, save the dialog and reopen to reload the tabs to show the control specific options.',
                    QModelConnectorParam::SELECTION_LIST, $strClassNames),
                new QModelConnectorParam(QModelConnectorParam::GENERAL_CATEGORY, 'Name',
                    'The Control\'s Name. Generally leave this blank, or use a plural name.', Type::STRING, ["translate"=>true]),
                new QModelConnectorParam(QModelConnectorParam::GENERAL_CATEGORY, 'ItemName',
                    'The public name of an item in the list. Its used by the title of the edit form, for example. Defaults to the name of the table in the database.',
                    Type::STRING, ["translate"=>true]),
                new QModelConnectorParam(QModelConnectorParam::GENERAL_CATEGORY, 'CreateFilter',
                    'Whether to generate a separate control to filter the data. If the data list control does its own filtering, set this to false. Default is true.',
                    Type::BOOLEAN),
                new QModelConnectorParam(QModelConnectorParam::GENERAL_CATEGORY, 'EditMode',
                    'How to edit an item. 1) Options are: to go to a separate form, 2) popup a dialog, or 3) popup a dialog only if not on a mobile device since mobile devices struggle with showing dialogs that are bigger than the screen.',
                    QModelConnectorParam::SELECTION_LIST,
                    array(
                        'form' => 'Edit with a QForm',
                        'dialog' => 'Edit with a QDialog',
                        'both' => 'Edit with a form on mobile devices, and a dialog on desktops.'
                    ))
            );
        }

        // load values from settings file
        foreach ($this->generalOptions as $objParam) {
            $objControl = $objParam->getControl($this->dtgGeneralOptions);    // get a control that will edit this option
            $strName = $objControl->Name;

            if (isset($this->params[$strName])) {
                $value = $this->params[$strName];
                if (is_array($value)) {
                    $value = $value["value"];
                }
                $objControl->Value = $value;
                if ($strName == 'ControlClass') {
                    $strControlClass = $value;
                }
            } else {
                $objControl->Value = null;
            }
        }

        if (!isset($strControlClass)) {
            $strControlClass = get_class($this->objCurrentControl);
        }
        $params = $strControlClass::getModelConnectorParams();

        // gather categories
        foreach ($params as $param) {
            $this->categories[$param->Category][] = $param;
        }

        // Add any additional general items to the general tab
        if (isset($this->categories[QModelConnectorParam::GENERAL_CATEGORY])) {
            // load values from settings file
            foreach ($this->categories[QModelConnectorParam::GENERAL_CATEGORY] as $objParam) {
                $objControl = $objParam->getControl($this->dtgGeneralOptions);    // get a control that will edit this option
                $strName = $objControl->Name;

                if (isset($this->params[$strName])) {
                    $value = $this->params[$strName];
                    if (is_array($value)) {
                        $value = $value["value"];
                    }
                    $objControl->Value = $value;
                } else {
                    $objControl->Value = null;
                }
                $this->generalOptions[] = $objParam;
            }

            unset($this->categories[QModelConnectorParam::GENERAL_CATEGORY]);
        }

        foreach ($this->categories as $tabName => $params) {
            //$panel = new QPanel ($this->tabs);
            //$panel->setCssStyle('overflow-y', 'scroll');
            //$panel->setCssStyle('max-height', '200');
            //$panel->AutoRenderChildren = true;
            //$panel->Name = $tabName;

            $dtg = new Table($this->tabs);
            $dtg->ShowHeader = false;
            $dtg->Name = $tabName;
            $dtg->createPropertyColumn('Attribute', 'Name');
            $col = $dtg->addColumn(new CallableColumn('Attribute', array($this, 'dtg_ValueRender'), $dtg));
            $col->HtmlEntities = false;
            $dtg->setDataBinder('dtgControlBind', $this);
            $dtg->Name = $tabName; // holder for category
            $this->datagrids[$tabName] = $dtg;

            // load values from settings file
            foreach ($params as $objParam) {
                $objControl = $objParam->getControl($this->datagrids[$tabName]);
                if ($objControl) {
                    $strName = $objControl->Name;

                    if (isset($this->params['Overrides'][$strName])) {
                        $value = $this->params['Overrides'][$strName];
                        if (is_array($value)) {
                            $value = $value["value"];
                        }
                        $objControl->Value = $value;
                    } else {
                        $objControl->Value = null;
                    }
                }
            }
        }
    }

    /**
     * Bind the general options
     */
    public function dtgGeneralOptions_Bind()
    {
        $this->dtgGeneralOptions->DataSource = $this->generalOptions;
    }

    /**
     * Binder for the control specific options
     * @param Table $dtg
     */
    public function dtgControlBind(Table $dtg)
    {
        $dtg->DataSource = $this->categories[$dtg->Name];
    }

    /**
     * Render the value column, which allows the user to specify the value of an option for the control.
     *
     * @param QModelConnectorParam $objControlParam
     * @param QControl $objParent
     * @return string
     */
    public function dtg_ValueRender(QModelConnectorParam $objControlParam, QControl $objParent)
    {
        $objControl = $objControlParam->getControl($objParent);
        if ($objControl) {
            return $objControl->render(false);
        } else {
            return "";
        }
    }

    /**
     * Entry point for the dialog. Brings up the dialog and loads all the options so that it can be edited.
     *
     * @param QControl $objControl
     */
    public function editControl(QControl $objControl)
    {
        $this->objCurrentControl = $objControl;

        $this->Title = $objControl->Name . ' Edit';

        $blnEditable = $this->readParams();
        if ($blnEditable) {
            $this->setupTabs();
            $this->open();
            $this->tabs->refresh();
        }
    }

    /**
     * Dialog button has been clicked. Save the options, or Save, codegen, and then reload.
     *
     * @param $strFormId
     * @param $strControlId
     * @param $mixParam
     */
    public function buttonClick($strFormId, $strControlId, $mixParam)
    {
        if ($mixParam == 'save') {
            $this->updateControlInfo();
            $this->writeParams();
        } elseif ($mixParam == 'saveRefresh') {
            $this->updateControlInfo();
            $this->writeParams();
            QCodeGen::run(QCUBED_CONFIG_DIR . '/codegen_settings.xml');
            foreach (QCodeGen::$CodeGenArray as $objCodeGen) {
                $objCodeGen->generateAll(); // silently codegen
            }
            Application::redirect($_SERVER['PHP_SELF']);
        }

        $this->close();
    }

    /**
     * Puts the values of the dialog into the params array to be saved off into the settings file.
     */
    protected function updateControlInfo()
    {
        $objParams = $this->generalOptions;
        foreach ($objParams as $objParam) {
            $objControl = $objParam->getControl($this->dtgGeneralOptions);
            $strName = $objControl->Name;
            $value = $objControl->Value;

            if (!is_null($value)) {
                $this->params[$strName] = $value;
            } else {
                unset($this->params[$strName]);
            }
        }

        foreach ($this->categories as $objParams) {
            foreach ($objParams as $objParam) {
                $objControl = $objParam->getControl();
                if ($objControl) {
                    $strName = $objControl->Name;
                    $value = $objControl->Value;

                    if ($objParam->getOption('translate')) {
                        $value = ["value"=>$value, "translate"=>true];
                    }

                    if (!is_null($value)) {
                        $this->params['Overrides'][$strName] = $value;
                    } else {
                        unset($this->params['Overrides'][$strName]);
                    }
                } else {
                    unset($this->params['Overrides'][$strName]);
                }
            }
        }

        if (empty($this->params['Overrides'])) {
            unset($this->params['Overrides']);
        }
    }

    /**
     * Write the current params into the settings file.
     */
    protected function writeParams()
    {
        $node = $this->objCurrentControl->LinkedNode;
        if ($node) {
            if ($node->_ParentNode) {
                $strClassName = $node->_ParentNode->_ClassName;
                $this->objModelConnectorOptions->setOptions($strClassName, $node->_PropertyName, $this->params);
                $this->objModelConnectorOptions->save();
            } else {
                // Table options
                $this->objModelConnectorOptions->setOptions($node->_ClassName,
                    QModelConnectorOptions::TABLE_OPTIONS_FIELD_NAME, $this->params);
                $this->objModelConnectorOptions->save();
            }
        }
    }

    /**
     * Read the params from the settings file.
     * Returns false if there were no params to be read, meaning this control is not attached to a database object.
     * @return bool
     */
    protected function readParams()
    {
        $node = $this->objCurrentControl->LinkedNode;
        if ($node) {
            if ($node->_ParentNode) {
                $strClassName = $node->_ParentNode->_ClassName;
                $this->params = $this->objModelConnectorOptions->getOptions($strClassName, $node->_PropertyName);
            } else {
                // Table options
                $this->params = $this->objModelConnectorOptions->getOptions($node->_ClassName,
                    QModelConnectorOptions::TABLE_OPTIONS_FIELD_NAME);
            }
            return true;
        }
        return false;
    }

    /**
     * Returns an array of class names that can be used to edit the current control's data type.
     *
     * @return array
     */
    protected function createClassNameArray()
    {
        // get the control array
        $dir = realpath(QCUBED_CONFIG_DIR . '/control_registry');
        $controls = [];

        if ($dir !== false) {    // does the active directory exist?
            foreach (new \DirectoryIterator($dir) as $fileInfo) {
                if ($fileInfo->isDot()) {
                    continue;
                }
                $strFileName = $fileInfo->getPathname();
                if (substr($strFileName, -8) == '.inc.php') {
                    $controls2 = include($strFileName);
                    if ($controls2 && is_array($controls2)) {
                        $controls = array_merge($controls, $controls2);
                    }
                }
            }
        }

        // $controls is now an array indexed by Type, with each entry a Control type name

        // Figure out what type of control we are looking for
        // For the most part, the control category types are the same as the database type
        $node = $this->objCurrentControl->LinkedNode;
        $type = $node->_Type;
        if (($node->_Type == Type::REVERSE_REFERENCE && $node->isUnique()) || $node->_Type == Type::ARRAY_TYPE) {
            $type = ControlCategoryType::SINGLE_SELECT;
        } elseif (($node->_Type == Type::REVERSE_REFERENCE && !$node->isUnique()) || $node->_Type == Type::ASSOCIATION) {
            $type = ControlCategoryType::MULTI_SELECT;
        } elseif ($node->_TableName) { // indicates a reference to a table
            if ($node->_ParentNode) {
                // A foreign key to another table
                $type = ControlCategoryType::SINGLE_SELECT;
            } else {
                // A top level table, so a grid or list view
                $type = ControlCategoryType::TABLE;
            }
        }

        if (isset($controls[$type])) {
            $a = [];
            foreach ($controls[$type] as $strClassName) {
                $a[$strClassName] = $strClassName;    // remove duplicates
            }

            return $a;
        } else {
            return null;
        }
    }
}
