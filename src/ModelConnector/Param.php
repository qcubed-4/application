<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\ModelConnector;

require_once(dirname(__DIR__, 2) . '/i18n/i18n-lib.inc.php');

//use QCubed\Application\t;

use QCubed\Control\IntegerTextBox;
use QCubed\Control\RadioButtonList;
use QCubed\Exception\Caller;
use QCubed\ObjectBase;
use QCubed\Control\ControlBase;
use QCubed\Project\Control\ListBox;
use QCubed\Project\Control\TextBox;
use QCubed\Type;

/**
 * Class Param
 *
 * Encapsulates a description of an editable ModelConnector parameter.
 *
 * For example, this class would be used to tell the QModelConnectorEditDlg that you can set the
 * name of a control using a text box, or the visibility state of a control using boolean selector.
 *
 * You can currently specify a boolean value, a text value, an integer value, or a list of options.
 *
 * @property-read string $Category
 * @property-read string $Name
 * @package QCubed\ModelConnector
 */

class Param extends ObjectBase
{
    /** Specifies a list of items to present to the user to select from. */
    const SELECTION_LIST = 'list';

    const GENERAL_CATEGORY = 'General';

    /** @var string  */
    protected string $strCategory;
    /** @var string  */
    protected string $strName;
    /** @var string  */
    protected string $strDescription;
    /** @var  string One of the controlType constants */
    protected string $controlType;
    /** @var mixed Options dependent on the control type */
    protected mixed $options;

    /** @var  ControlBase|null caching the created control */
    protected ?ControlBase $objControl = null;

    /**
     * Initializes a new instance of the class.
     *
     * @param string $strCategory The category of the instance.
     * @param string $strName The name of the instance.
     * @param string $strDescription A description of the instance.
     * @param mixed $controlType The type of control associated with the instance.
     * @param mixed|null $options Optional additional options. Required for a selection list control type.
     * @return void
     *
     * @throws Caller If the control type is a selection list, but no options are provided.
     */
    public function __construct(string $strCategory, string $strName, string $strDescription, mixed $controlType, mixed $options = null)
    {
        $this->strCategory = t($strCategory);
        $this->strName = t($strName);
        $this->strDescription = t($strDescription);
        $this->controlType = $controlType;

        $this->options = $options;

        if ($controlType == static::SELECTION_LIST && !$options) {
            throw new Caller('Selection list without a list of items to select.');
        }
    }

    /**
     * Called by the QModelConnectorEditDlg dialog. Creates a control that will allow the user to edit the value
     * associated with this parameter and caches that control so that it's easy to get to.
     *
     * @param ControlBase|null $objParent
     * @return IntegerTextBox|ListBox|RadioButtonList|TextBox|ControlBase|null
     * @throws Caller
     */
    public function getControl(?ControlBase $objParent = null): IntegerTextBox|ListBox|RadioButtonList|TextBox|ControlBase|null
    {
        if ($this->objControl) {
            if ($objParent) {
                $this->objControl->setParentControl($objParent);
            }
            return $this->objControl;
        } elseif ($objParent) {
            $this->objControl = $this->createControl($objParent);
            return $this->objControl;
        }
        return null;
    }

    /**
     * Creates and returns a control instance based on the defined control type.
     *
     * @param ControlBase $objParent The parent control to associate with the newly created control.
     * @return TextBox|RadioButtonList|ListBox|IntegerTextBox The created control instance configured with associated properties and settings.
     * @throws Caller
     */
    protected function createControl(ControlBase $objParent): TextBox|RadioButtonList|ListBox|IntegerTextBox
    {
        switch ($this->controlType) {
            case Type::BOOLEAN:
                $ctl = new RadioButtonList($objParent);
                $ctl->addItem('True', true);
                $ctl->addItem('False', false);
                $ctl->addItem('None', null);
                $ctl->RepeatColumns = 3;
                break;

            case Type::STRING:
                $ctl = new TextBox($objParent);
                break;

            case Type::INTEGER:
                $ctl = new IntegerTextBox($objParent);
                break;

            case Type::ARRAY_TYPE:    // an array the user will specify in a comma-separated list
                $ctl = new TextBox($objParent);
                break;

            case self::SELECTION_LIST: // a specific set of choices to present to the user
                $ctl = new ListBox($objParent);

                foreach ($this->options as $key => $val) {
                    $ctl->addItem($val, $key === '' ? null : $key); // allow null item keys
                }
                break;

            default: // i.e., QJsClosure, or other random items. Probably codegen, and not used much.
                $ctl = new TextBox($objParent);
                break;

        }

        $ctl->Name = $this->strName;
        $ctl->ToolTip = $this->strDescription;
        return $ctl;
    }

    /**
     * Retrieves the value of the specified option if it exists.
     *
     * @param string $strOptName The name of the option to retrieve.
     * @return mixed|null The value of the option if it exists, otherwise null.
     */
    public function getOption(string $strOptName): mixed
    {
        if (isset($this->options[$strOptName])) {
            return $this->options[$strOptName];
        }
        return null;
    }

    /**
     * Magic method to retrieve the value of a property.
     *
     * @param string $strName The name of the property to retrieve.
     * @return mixed The value of the requested property or the parent's implementation result.
     * @throws Caller Thrown if the property does not exist.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'Name':
                return $this->strName;

            case 'Category':
                return $this->strCategory;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }
}
