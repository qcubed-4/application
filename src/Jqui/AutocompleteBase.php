<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Jqui;

use QCubed as Q;
use QCubed\Control\ListItem;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Control\ControlBase;
use QCubed\Control\FormBase;
use QCubed\Type;

/**
 * Class AutocompleteBase
 *
 * Implements the JQuery UI Autocomplete widget
 *
 * The Autocomplete is JQuery UIs version of a field with an attached drop down menu. As you type in
 * the field, the menu appears, and the items in the menu are filtered by what the user types. This class allows
 * you to use an array of QListItems, or an array of database objects as the source. You can also pass this array
 * statically in the Source parameter at creation time, or dynamically via Ajax by using SetDataBinder, and then
 * in your data binder function, setting the DataSource parameter.
 *
 * @property string $SelectedId the id of the selected item. When QAutocompleteListItem objects are used for the DataSource, this corresponds to the Value of the item
 * @property boolean $MustMatch if true, non matching values are not accepted by the input
 * @property string $MultipleValueDelimiter if set, the Autocomplete will keep appending the new selections to the previous term, delimited by this string.
 *    This is useful when making QAutocomplete handle multiple values (see http://jqueryui.com/demos/autocomplete/#multiple ).
 * @property boolean $DisplayHtml if set, the Autocomplete will treat the 'label' portion of each data item as Html.
 * @property-write array $Source an array of strings, QListItem's, or data objects. To be used at creation time. {@inheritdoc }
 * @property-write array $DataSource an array of strings, QListItem's, or data objects
 * @link http://jqueryui.com/autocomplete/
 * @access private
 * @package Controls\Base
 * @was QAutocompleteBase
 * @package QCubed\Jqui
 */
class AutocompleteBase extends AutocompleteGen
{
    /** @var string */
    protected $strSelectedId = null;
    /** @var boolean */
    protected $blnUseAjax = false;

    /* Moved to QAutoComplete2 plugin */
    //protected $blnMustMatch = false;
    //protected $strMultipleValueDelimiter = null;
    //protected $blnDisplayHtml = false;

    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->addJavascriptFile(QCUBED_JS_URL . '/qcubed.autocomplete.js');

        /**
         * Fixing a problem with placing an autocomplete in a modal that is not a jquery ui modal (like a bootstrap modal)
         * We must append the menu to the modal, or the modal will obscure the menu.
         */
        while ($objParentObject && $objParentObject instanceof ControlBase) {
            if ($objParentObject->AutoRender) {
                $this->AppendTo = '#' . $objParentObject->ControlId; // selector for that control
                break;
            }
            $objParentObject = $objParentObject->ParentControl;
        }
    }

    /**
     * When this filter is passed to QAutocomplete::UseFilter, only the items in the source list that contain the typed term will be shown in the drop-down
     * This is the default filter used by the jQuery autocomplete. Useful when resetting from a previousely set filter.
     * @see QAutocomplete::UseFilter
     */
    const FILTER_CONTAINS = 'return $j.ui.autocomplete.escapeRegex(term);'; // this is the default filter
    /**
     * When this filter is passed to QAutocomplete::UseFilter, only the items in the source list that begin with the typed term will be shown in the drop-down
     * @see QAutocomplete::UseFilter
     */
    const FILTER_STARTS_WITH = 'return ("^" + $j.ui.autocomplete.escapeRegex(term));';

    /**
     * Set a filter to use when using a simple array as a source (in non-ajax mode). Note that ALL non-ajax autocompletes on the page
     * will use the new filter.
     *
     * @static
     * @throws Caller
     * @param string|\QCubed\Js\Closure $filter represents a closure that will be used as the global filter function for jQuery autocomplete.
     * The closure should take two arguments - array and term. array is the list of all available choices, term is what the user typed in the input box.
     * It should return an array of suggestions to show in the drop-down.
     * <b>Example:</b> <code>QAutocomplete::useFilter(QAutocomplete::FILTER_STARTS_WITH)</code>
     * @return void
     *
     * @see QAutocomplete::FILTER_CONTAINS
     * @see QAutocomplete::FILTER_STARTS_WITH
     */
    public static function useFilter($filter)
    {
        if (is_string($filter)) {
            $filter = new Q\Js\Closure($filter, ['term']);
        } else {
            if (!($filter instanceof Q\Js\Closure)) {
                throw new Caller("filter must be either a string or an instance of \\QCubed\\Js\\Closure");
            }
        }
        Application::executeJsFunction('qcubed.acUseFilter', $filter);
    }


    /**
     * Set the data binder for ajax filtering
     *
     * Call this at creation time to set the data binder of the item list you will display. The data binder
     * will be an AjaxAction function.
     *
     * The 'param' value in the params item passed to the action in particular will be the term that you should use for filtering.
     *
     * @param string $strMethodName Name of the method which has to be bound
     * @param FormBase|ControlBase $objParentControl The parent control on which the action is to be bound
     */
    public function setDataBinder($strMethodName, $objParentControl = null)
    {
        if ($objParentControl) {
            $objAction = new Q\Action\AjaxControl($objParentControl, $strMethodName, 'default', null, 'ui');
        } else {
            $objAction = new Q\Action\Ajax($strMethodName, 'default', null, 'ui');
        }
        $this->addAction(new Event\AutocompleteSource(), $objAction);

        $this->mixSource = new Q\Js\VarName('qcubed.acSourceFunction');

        $this->blnUseAjax = true;
        $this->blnModified = true;
    }

    // These functions are used to keep track of the selected value, and to implement
    // optional autocomplete functionality.
    /**
     * Gets the Javascript part of the control which is sent to the client side upon the completion of Render
     */
    protected function makeJqWidget()
    {
        parent::makeJqWidget();

        Application::executeJsFunction('qc.autocomplete', $this->getJqControlId(), Application::PRIORITY_HIGH);
    }


    // Response to an ajax request for data
    protected function prepareAjaxList($dataSource)
    {
        if (!$dataSource) {
            $dataSource = array();
        }
        Application::executeJsFunction('qc.acSetData', $this->getJqControlId(), $dataSource,
            Application::PRIORITY_EXCLUSIVE);
    }

    /**
     *
     */
    public function setEmpty()
    {
        $this->Text = '';
        $this->SelectedId = null;
    }

    /**
     * Control subclasses should return their state data that they will use to restore later.
     * @return mixed
     */
    protected function getState()
    {
        $state = parent::getState();
        $state['selectedId'] = $this->SelectedId;
        return $state;
    }

    /**
     * Restore the state of the control. The control will have already been
     * created and initialized. Subclasses should verify that the restored state is still valid for the data
     * available.
     * @param mixed $state
     */
    protected function putState($state)
    {
        parent::putState($state);
        if (isset($state['selectedId'])) {
            $this->SelectedId = $state['selectedId'];
        }
    }


    /**
     * PHP __set Magic method
     * @param string $strName Property Name
     * @param string $mixValue Property Value
     *
     * @throws InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case 'DataSource':
                // Assign data to a DataSource from within the data binder function only.
                // Data should be array items that at a minimum contain a 'value' and an 'id'
                // They can also contain a 'label', which will be displayed in the popup menu only
                if ($this->blnUseAjax) {
                    $this->prepareAjaxList($mixValue);
                } else {
                    $this->Source = $mixValue;
                }
                break;

            case "SelectedValue":    // mirror list control
            case "Value":
            case 'SelectedId':
                // Set this at creation time to initialize the selected id.
                // This is also set by the javascript above to keep track of subsequent selections made by the user.
                try {
                    if ($mixValue == 'null') {
                        $this->strSelectedId = null;
                    } else {
                        $this->strSelectedId = Type::cast($mixValue, Type::STRING);
                    }
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case 'Source':
                try {
                    if (is_array($mixValue) && count($mixValue) > 0 && $mixValue[0] instanceof ListItem) {
                        // figure out what item is selected
                        foreach ($mixValue as $objItem) {
                            if ($objItem->Selected) {
                                $this->strSelectedId = $objItem->Value;
                                $this->Text = $objItem->Name;
                            }
                        }
                    }
                    parent::__set($strName, $mixValue);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }

    /**
     * PHP __get magic method implementation
     * @param string $strName Name of the property
     *
     * @return mixed
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            case "SelectedValue":    // mirror list control
            case "Value": // most common situation
            case 'SelectedId':
                return $this->strSelectedId;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * If this control is attachable to a codegenerated control in a ModelConnector, this function will be
     * used by the ModelConnector designer dialog to display a list of options for the control.
     * @return Q\ModelConnector\Param[]
     **/
    public static function getModelConnectorParams()
    {
        return array_merge(parent::getModelConnectorParams(), array(
            new Q\ModelConnector\Param(Q\ModelConnector\Param::GENERAL_CATEGORY, 'NoAutoLoad',
                'Prevent automatically populating a list type control. Set this if you are doing more complex list loading.',
                Type::BOOLEAN)
        ));
    }
}
