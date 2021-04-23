<?php
namespace QCubed\Jqui;

use QCubed;
use QCubed\Type;
use QCubed\Project\Application;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\ModelConnector\Param as QModelConnectorParam;

/**
 * Class AutocompleteGen
 *
 * This is the AutocompleteGen class which is automatically generated
 * by scraping the JQuery UI documentation website. As such, it includes all the options
 * as listed by the JQuery UI website, which may or may not be appropriate for QCubed. See
 * the AutocompleteBase class for any glue code to make this class more
 * usable in QCubed.
 *
 * @see AutocompleteBase
 * @package QCubed\Jqui
 * @property mixed $AppendTo
 * Which element the menu should be appended to. When the value is null,
 * the parents of the input field will be checked for a class of
 * ui-front. If an element with the ui-front class is found, the menu
 * will be appended to that element. Regardless of the value, if no
 * element is found, the menu will be appended to the body.
 * Note: The appendTo option should not be changed while the suggestions
 * menu is open.
 *
 * @property boolean $AutoFocus
 * If set to true the first item will automatically be focused when the
 * menu is shown.
 *
 * @property mixed $Classes
 * Specify additional classes to add to the widgets elements. Any of
 * classes specified in the Theming section can be used as keys to
 * override their value. To learn more about this option, check out the
 * learn article about the classes option.

 *
 * @property integer $Delay
 * The delay in milliseconds between when a keystroke occurs and when a
 * search is performed. A zero-delay makes sense for local data (more
 * responsive), but can produce a lot of load for remote data, while
 * being less responsive.
 *
 * @property boolean $Disabled
 * Disables the autocomplete if set to true.
 *
 * @property integer $MinLength
 * The minimum number of characters a user must type before a search is
 * performed. Zero is useful for local data with just a few items, but a
 * higher value should be used when a single character search could match
 * a few thousand items.
 *
 * @property mixed $Position
 * Identifies the position of the suggestions menu in relation to the
 * associated input element. The of option defaults to the input element,
 * but you can specify another element to position against. You can refer
 * to the jQuery UI Position utility for more details about the various
 * options.
 *
 * @property mixed $Source
 * Defines the data to use, must be specified.
 * Independent of the variant you use, the label is always treated as
 * text. If you want the label to be treated as html you can use Scott
 * González html extension. The demos all focus on different variations
 * of the source option - look for one that matches your use case, and
 * check out the code.
 * Multiple types supported:
 * 
 * 	* Array: An array can be used for local data. There are two supported
 * formats: 
 * 
 * 	* An array of strings: [ "Choice1", "Choice2" ]
 * 	* An array of objects with label and value properties: [ { label:
 * "Choice1", value: "value1" }, ... ]
 * 
 *  The label property is displayed in the suggestion menu. The value
 * will be inserted into the input element when a user selects an item.
 * If just one property is specified, it will be used for both, e.g., if
 * you provide only value properties, the value will also be used as the
 * label.	* String: When a string is used, the Autocomplete plugin
 * expects that string to point to a URL resource that will return JSON
 * data. It can be on the same host or on a different one (must provide
 * JSONP). The Autocomplete plugin does not filter the results, instead a
 * query string is added with a term field, which the server-side script
 * should use for filtering the results. For example, if the source
 * option is set to "http://example.com" and the user types foo, a GET
 * request would be made to http://example.com?term=foo. The data itself
 * can be in the same format as the local data described above.
 * 
 * 	* Function: The third variation, a callback, provides the most
 * flexibility and can be used to connect any data source to
 * Autocomplete. The callback gets two arguments: 
 * 
 * 	* A request object, with a single term property, which refers to the
 * value currently in the text input. For example, if the user enters
 * "new yo" in a city field, the Autocomplete term will equal "new yo".
 * 	* A response callback, which expects a single argument: the data to
 * suggest to the user. This data should be filtered based on the
 * provided term, and can be in any of the formats described above for
 * simple local data. Its important when providing a custom source
 * callback to handle errors during the request. You must always call the
 * response callback even if you encounter an error. This ensures that
 * the widget always has the correct state.
 * 
 * When filtering data locally, you can make use of the built-in
 * $.ui.autocomplete.escapeRegex function. Itll take a single string
 * argument and escape all regex characters, making the result safe to
 * pass to new RegExp().
 * 

 *
 * @was QAutocompleteGen

 */

class AutocompleteGen extends QCubed\Project\Control\TextBox
{
    protected $strJavaScripts = QCUBED_JQUI_JS;
    protected $strStyleSheets = QCUBED_JQUI_CSS;
    /** @var mixed */
    protected $mixAppendTo = null;
    /** @var boolean */
    protected $blnAutoFocus = null;
    /** @var mixed */
    protected $mixClasses = null;
    /** @var integer */
    protected $intDelay = null;
    /** @var boolean */
    protected $blnDisabled = null;
    /** @var integer */
    protected $intMinLength = null;
    /** @var mixed */
    protected $mixPosition = null;
    /** @var mixed */
    protected $mixSource;

    /**
     * Builds the option array to be sent to the widget constructor.
     *
     * @return array key=>value array of options
     */
    protected function makeJqOptions() {
        $jqOptions = parent::MakeJqOptions();
        if (!is_null($val = $this->AppendTo)) {$jqOptions['appendTo'] = $val;}
        if (!is_null($val = $this->AutoFocus)) {$jqOptions['autoFocus'] = $val;}
        if (!is_null($val = $this->Classes)) {$jqOptions['classes'] = $val;}
        if (!is_null($val = $this->Delay)) {$jqOptions['delay'] = $val;}
        if (!is_null($val = $this->Disabled)) {$jqOptions['disabled'] = $val;}
        if (!is_null($val = $this->MinLength)) {$jqOptions['minLength'] = $val;}
        if (!is_null($val = $this->Position)) {$jqOptions['position'] = $val;}
        if (!is_null($val = $this->Source)) {$jqOptions['source'] = $val;}
        return $jqOptions;
    }

    /**
     * Return the JavaScript function to call to associate the widget with the control.
     *
     * @return string
     */
    public function getJqSetupFunction()
    {
        return 'autocomplete';
    }


    /**
     * Closes the Autocomplete menu. Useful in combination with the search
     * method, to close the open menu.
     * 
     * 	* This method does not accept any arguments.
     */
    public function close()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "close", Application::PRIORITY_LOW);
    }
    /**
     * Removes the autocomplete functionality completely. This will return
     * the element back to its pre-init state.
     * 
     * 	* This method does not accept any arguments.
     */
    public function destroy()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "destroy", Application::PRIORITY_LOW);
    }
    /**
     * Disables the autocomplete.
     * 
     * 	* This method does not accept any arguments.
     */
    public function disable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "disable", Application::PRIORITY_LOW);
    }
    /**
     * Enables the autocomplete.
     * 
     * 	* This method does not accept any arguments.
     */
    public function enable()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "enable", Application::PRIORITY_LOW);
    }
    /**
     * Retrieves the autocompletes instance object. If the element does not
     * have an associated instance, undefined is returned.
     * 
     * Unlike other widget methods, instance() is safe to call on any element
     * after the autocomplete plugin has loaded.
     * 
     * 	* This method does not accept any arguments.
     */
    public function instance()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "instance", Application::PRIORITY_LOW);
    }
    /**
     * Gets the value currently associated with the specified optionName.
     * 
     * Note: For options that have objects as their value, you can get the
     * value of a specific key by using dot notation. For example, "foo.bar"
     * would get the value of the bar property on the foo option.
     * 
     * 	* optionName Type: String The name of the option to get.
     * @param $optionName
     */
    public function option($optionName)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, Application::PRIORITY_LOW);
    }
    /**
     * Gets an object containing key/value pairs representing the current
     * autocomplete options hash.
     * 
     * 	* This signature does not accept any arguments.
     */
    public function option1()
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", Application::PRIORITY_LOW);
    }
    /**
     * Sets the value of the autocomplete option associated with the
     * specified optionName.
     * 
     * Note: For options that have objects as their value, you can set the
     * value of just one property by using dot notation for optionName. For
     * example, "foo.bar" would update only the bar property of the foo
     * option.
     * 
     * 	* optionName Type: String The name of the option to set.
     * 	* value Type: Object A value to set for the option.
     * @param $optionName
     * @param $value
     */
    public function option2($optionName, $value)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $optionName, $value, Application::PRIORITY_LOW);
    }
    /**
     * Sets one or more options for the autocomplete.
     * 
     * 	* options Type: Object A map of option-value pairs to set.
     * @param $options
     */
    public function option3($options)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "option", $options, Application::PRIORITY_LOW);
    }
    /**
     * Triggers a search event and invokes the data source if the event is
     * not canceled. Can be used by a selectbox-like button to open the
     * suggestions when clicked. When invoked with no parameters, the current
     * inputs value is used. Can be called with an empty string and
     * minLength: 0 to display all items.
     * 
     * 	* value Type: String
     * @param $value
     */
    public function search($value = null)
    {
        Application::executeControlCommand($this->getJqControlId(), $this->getJqSetupFunction(), "search", $value, Application::PRIORITY_LOW);
    }


    public function __get($strName)
    {
        switch ($strName) {
            case 'AppendTo': return $this->mixAppendTo;
            case 'AutoFocus': return $this->blnAutoFocus;
            case 'Classes': return $this->mixClasses;
            case 'Delay': return $this->intDelay;
            case 'Disabled': return $this->blnDisabled;
            case 'MinLength': return $this->intMinLength;
            case 'Position': return $this->mixPosition;
            case 'Source': return $this->mixSource;
            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case 'AppendTo':
                $this->mixAppendTo = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'appendTo', $mixValue);
                break;

            case 'AutoFocus':
                try {
                    $this->blnAutoFocus = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'autoFocus', $this->blnAutoFocus);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Classes':
                $this->mixClasses = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'classes', $mixValue);
                break;

            case 'Delay':
                try {
                    $this->intDelay = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'delay', $this->intDelay);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Disabled':
                try {
                    $this->blnDisabled = Type::Cast($mixValue, Type::BOOLEAN);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'disabled', $this->blnDisabled);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'MinLength':
                try {
                    $this->intMinLength = Type::Cast($mixValue, Type::INTEGER);
                    $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'minLength', $this->intMinLength);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'Position':
                $this->mixPosition = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'position', $mixValue);
                break;

            case 'Source':
                $this->mixSource = $mixValue;
                $this->addAttributeScript($this->getJqSetupFunction(), 'option', 'source', $mixValue);
                break;


            case 'Enabled':
                $this->Disabled = !$mixValue;	// Tie in standard QCubed functionality
                parent::__set($strName, $mixValue);
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                    break;
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
    * If this control is attachable to a codegenerated control in a ModelConnector, this function will be
    * used by the ModelConnector designer dialog to display a list of options for the control.
    * @return QModelConnectorParam[]
    **/
    public static function getModelConnectorParams()
    {
        return array_merge(parent::GetModelConnectorParams(), array(
            new QModelConnectorParam (get_called_class(), 'AutoFocus', 'If set to true the first item will automatically be focused when themenu is shown.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'Delay', 'The delay in milliseconds between when a keystroke occurs and when asearch is performed. A zero-delay makes sense for local data (moreresponsive), but can produce a lot of load for remote data, whilebeing less responsive.', Type::INTEGER),
            new QModelConnectorParam (get_called_class(), 'Disabled', 'Disables the autocomplete if set to true.', Type::BOOLEAN),
            new QModelConnectorParam (get_called_class(), 'MinLength', 'The minimum number of characters a user must type before a search isperformed. Zero is useful for local data with just a few items, but ahigher value should be used when a single character search could matcha few thousand items.', Type::INTEGER),
        ));
    }
}
