<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\ModelConnector;

require_once(dirname(dirname(__DIR__)) . '/i18n/i18n-lib.inc.php');
use QCubed\Application\t;

use QCubed\Control\IntegerTextBox;
use QCubed\Control\RadioButtonList;
use QCubed\Exception\Caller;
use QCubed;
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
 * @was QModelConnectorParam
 * @package QCubed\ModelConnector
 */

class Param extends ObjectBase
{
    /** Specifies a list of items to present to the user to select from. */
    const SELECTION_LIST = 'list';

    const GENERAL_CATEGORY = 'General';

    /** @var string  */
    protected $strCategory;
    /** @var string  */
    protected $strName;
    /** @var string  */
    protected $strDescription;
    /** @var  string One of the controlType constants */
    protected $controlType;
    /** @var mixed Options dependent on the control type */
    protected $options;

    /** @var  ControlBase caching the created control */
    protected $objControl;

    public function __construct($strCategory, $strName, $strDescription, $controlType, $options = null)
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
     * associated with this parameter, and caches that control so that its easy to get to.
     *
     * @param ControlBase|null $objParent
     * @return null|ControlBase
     */
    public function getControl($objParent = null)
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
     * Creates the actual control that will edit the value.
     *
     * @param ControlBase $objParent
     * @return IntegerTextBox|ListBox|RadioButtonList|TextBox
     */
    protected function createControl(ControlBase $objParent)
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

            case Type::ARRAY_TYPE:    // an array the user will specify in a comma separated list
                $ctl = new TextBox($objParent);
                break;

            case self::SELECTION_LIST: // a specific set of choices to present to the user
                $ctl = new ListBox($objParent);

                foreach ($this->options as $key => $val) {
                    $ctl->addItem($val, $key === '' ? null : $key); // allow null item keys
                }
                break;

            default: // i.e. QJsClosure, or other random items. Probably codegened, and not used much.
                $ctl = new TextBox($objParent);
                break;

        }

        $ctl->Name = $this->strName;
        $ctl->ToolTip = $this->strDescription;
        return $ctl;
    }

    /**
     * @param string $strOptName
     * @return mixed
     */
    public function getOption($strOptName) {
        if (isset($this->options[$strOptName])) {
            return $this->options[$strOptName];
        }
        return null;
    }

    public function __get($strName)
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
