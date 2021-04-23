<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

require_once(dirname(dirname(__DIR__)) . '/i18n/i18n-lib.inc.php');
use QCubed\Application\t;

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\QString;
use QCubed\Type;
use QCubed\ModelConnector\Param as QModelConnectorParam;
use QCubed as Q;

/**
 * Class TextBoxBase
 *
 * This class will render an HTML Textbox -- which can either be [input type="text"],
 * [input type="password"] or [textarea] depending on the TextMode (see below).
 *
 * @package Controls\Base
 * @property integer $Columns               is the "cols" html attribute (applicable for MultiLine textboxes)
 * @property string $Format
 * @property string $Text                  is the contents of the textbox, itself
 * @property string|null $Value            Returns the value of the text. If the text is empty, will return null.
 *                                            Subclasses can use this to return a specific type of data.
 * @property string $LabelForRequired
 * @property string $LabelForRequiredUnnamed
 * @property string $LabelForTooShort
 * @property string $LabelForTooShortUnnamed
 * @property string $LabelForTooLong
 * @property string $LabelForTooLongUnnamed
 * @property string $Placeholder           HTML5 Only. Placeholder text that gets erased once a user types.
 * @property string $CrossScripting        can be Allow, HtmlEntities, or Deny.  Deny is the default. Prevents cross scripting hacks.  HtmlEntities causes framework to automatically call php function htmlentities on the input data.  Allow allows everything to come through without altering at all.  USE "ALLOW" judiciously: using ALLOW on text entries, and then outputting that data WILL allow hackers to perform cross scripting hacks.
 * @property integer $MaxLength             is the "maxlength" html attribute (applicable for SingleLine textboxes)
 * @property integer $MinLength             is the minimum requred length to pass validation
 * @property integer $Rows                  is the "rows" html attribute (applicable for MultiLine textboxes)
 * @property string $TextMode              a TextMode item. Determines if its a single or multi-line textbox, and the "type" property of the input.
 * @property boolean $AutoTrim              to automatically remove white space from beginning and end of data
 * @property integer $SanitizeFilter        PHP filter constant to apply to incoming data
 * @property mixed $SanitizeFilterOptions PHP filter constants or array to apply to SanitizeFilter option
 * @property integer $ValidateFilter        PHP filter constant to apply to validate with
 * @property mixed $ValidateFilterOptions PHP filter constants or array to apply to ValidateFilter option
 * @property mixed $LabelForInvalid       PHP filter constants or array to apply to ValidateFilter option
 *
 * @was QTextBoxBase
 * @package QCubed\Control
 */
abstract class TextBoxBase extends Q\Project\Control\ControlBase
{
    // Text modes
    const SINGLE_LINE = 'text'; // Single line text inputs INPUT type="text" boxes
    const MULTI_LINE = 'MultiLine'; // Textareas
    const PASSWORD = 'password'; //Single line password inputs
    const SEARCH = 'search';
    const NUMBER = 'number';
    const EMAIL = 'email';
    const TEL = 'tel';
    const URL = 'url';

    const XSS_ALLOW = 'Allow';
    const XSS_HTML_ENTITIES = 'HtmlEntities';   // simple entity maker
    const XSS_HTML_PURIFIER = 'HTMLPurifier'; // use html purifier
    const XSS_PHP_SANITIZE = 'PhpSanitize'; // Use PHP's built in sanitizer
    // Legacy and Deny are remvoed. Use something else.

    public static $DefaultCrossScriptingMode = self::XSS_PHP_SANITIZE;

/** @var int */
    protected $intColumns = 0;
    /** @var string */
    protected $strText = null;
    /** @var string */
    protected $strLabelForRequired;
    /** @var string */
    protected $strLabelForRequiredUnnamed;
    /** @var string */
    protected $strLabelForTooShort;
    /** @var string */
    protected $strLabelForTooShortUnnamed;
    /** @var string */
    protected $strLabelForTooLong;
    /** @var string */
    protected $strLabelForTooLongUnnamed;
    /** @var string */
    protected $strPlaceholder = '';
    /** @var string */
    protected $strFormat = '%s';

    // BEHAVIOR
    /** @var int */
    protected $intMaxLength = 0;
    /** @var int */
    protected $intMinLength = 0;
    /** @var int */
    protected $intRows = 0;
    /** @var string Subclasses should not set this directly, but rather use the TextMode accessor */
    protected $strTextMode = self::SINGLE_LINE;
    /** @var string */
    protected $strCrossScripting;
    /** @var null */
    protected $objHTMLPurifierConfig = null;

    // Sanitization and validating
    /** @var bool */
    protected $blnAutoTrim = false;
    /** @var int */
    protected $intSanitizeFilter = null;
    /** @var mixed */
    protected $mixSanitizeFilterOptions = null;
    /** @var int */
    protected $intValidateFilter = null;
    /** @var mixed */
    protected $mixValidateFilterOptions = null;
    /** @var string */
    protected $strLabelForInvalid = null;


    /**
     * TextBoxBase constructor.
     * @param ControlBase|FormBase $objParentObject
     * @param null $strControlId
     */
    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        $this->strLabelForRequired = t('%s is required');
        $this->strLabelForRequiredUnnamed = t('Required');

        $this->strLabelForTooShort = t('%s must have at least %s characters');
        $this->strLabelForTooShortUnnamed = t('Must have at least %s characters');

        $this->strLabelForTooLong = t('%s must have at most %s characters');
        $this->strLabelForTooLongUnnamed = t('Must have at most %s characters');
    }

    /**
     * This function allows to set the Configuration for HTMLPurifier
     * similar to the HTMLPurifierConfig::set() method from the HTMLPurifier API. This creates a custom purifier just
     * for this textbox. See the Purifier class for setting global options.
     *
     * @param strParameter : The parameter to set for HTMLPurifier
     * @param mixValue : Value of the parameter.
     *                     NOTE: THERE IS NO SUPPORT FOR THE DEPRECATED API OF HTMLPURIFIER, HENCE NO THIRD ARGUMENT TO THE
     *                     FUNCTION CAN BE PASSED.
     *                     Visit http://htmlpurifier.org/live/configdoc/plain.html for the list of parameters and their effects.
     */
    public function setPurifierConfig($strParameter, $mixValue)
    {
        if ($this->objHTMLPurifierConfig == null) {
            $this->objHTMLPurifierConfig = \HTMLPurifier_Config::createDefault();
        }
        $this->objHTMLPurifierConfig->set($strParameter, $mixValue);
    }

    /**
     * Parse the data posted back via the control.
     * This function basically test for the Crossscripting rules applied to the TextBox
     */
    public function parsePostData()
    {
        // Check to see if this Control's Value was passed in via the POST data
        if (array_key_exists($this->strControlId, $_POST)) {
            // It was -- update this Control's value with the new value passed in via the POST arguments
            $strText = $_POST[$this->strControlId];
            $strText = str_replace("\r\n", "\n", $strText); // Convert posted newlines to PHP newlines
            $this->strText = $strText;

            $this->sanitize();

            switch ($this->strCrossScripting) {
                case self::XSS_ALLOW: // Do Nothing, allow everything
                case self::XSS_PHP_SANITIZE: // Already filtered by the built-in PHP sanitizer
                    break;
                case self::XSS_HTML_ENTITIES: // Perform HtmlEntities on the text
                    $this->strText = QString::htmlEntities($this->strText);
                    break;
                case self::XSS_HTML_PURIFIER: // Very advanced filtering
                    $this->strText = Application::purify($this->strText, $this->objHTMLPurifierConfig); // don't save data as html entities! Encode at display time.
                    break;
                default:
                    throw new \Exception("Unknown cross scripting setting. Legacy purifier is not supported any more. Try XSS_PHP_SANITIZE");
                    break;
            }
        }
    }

    /**
     * Sanitizes the current value.
     */
    protected function sanitize()
    {
        if ($this->blnAutoTrim) {
            $this->strText = trim($this->strText);
        }

        if ($this->intSanitizeFilter) {
            $this->strText = filter_var($this->strText, $this->intSanitizeFilter, $this->mixSanitizeFilterOptions);
        }
    }

    /**
     * Returns the HTML formatted string for the control
     * @return string HTML string
     */
    protected function getControlHtml()
    {
        $attrOverride = array('name' => $this->strControlId);

        switch ($this->strTextMode) {
            case self::MULTI_LINE:
                $strText = QString::htmlEntities($this->strText);

                return $this->renderTag('textarea',
                    $attrOverride,
                    null,
                    $strText);

            default:
                $attrOverride['value'] = $this->strText;
                return $this->renderTag('input',
                    $attrOverride,
                    null,
                    null,
                    true
                );

        }
    }


    /**
     * Render HTML attributes for the purpose of drawing the tag. Text objects have a number of parameters specific
     * to them, some of which we use for validation, and some of which are dual purpose.
     * We render those here, rather than setting the attributes when those are set.
     *
     * @param null $attributeOverrides
     * @param null $styleOverrides
     *
     * @return string
     */
    public function renderHtmlAttributes($attributeOverrides = null, $styleOverrides = null)
    {
        if ($this->intMaxLength) {
            $attributeOverrides['maxlength'] = $this->intMaxLength;
        }
        if ($this->strTextMode == self::MULTI_LINE) {
            if ($this->intColumns) {
                $attributeOverrides['cols'] = $this->intColumns;
            }
            if ($this->intRows) {
                $attributeOverrides['rows'] = $this->intRows;
            }
            //if (!$this->blnWrap) {
            /**
             * $strToReturn .= 'wrap="off" '; Note that this is not standard HTML5 and not supported by all browsers
             * In fact, HTML5 has completely changed its meaning to mean whether the text itself has embedded
             * hard returns inserted when the textarea wraps. Deprecating. We will have to wait for another solution.
             */
            //}
        } else {
            if ($this->intColumns) {
                $attributeOverrides['size'] = $this->intColumns;
            }
            $typeStr = $this->strTextMode ? $this->strTextMode : 'text';
            $attributeOverrides['type'] = $typeStr;
        }

        if (strlen($this->strPlaceholder) > 0) {
            $attributeOverrides['placeholder'] = $this->strPlaceholder;
        }

        return parent::renderHtmlAttributes($attributeOverrides, $styleOverrides);
    }


    /**
     * Tests that the value given inside the textbox passes the rules set for the input
     * Tests it does:
     * (1) Checks if the textbox was empty while 'Required' property was set to true
     * (2) Checks for length contrainsts set by 'MaxLength' and 'MinLength' properties
     *
     * @return bool whether or not the control is valid
     */
    public function validate()
    {
        // Copy text
        $strText = $this->strText;
        // Check for Required
        if ($this->blnRequired) {
            if (mb_strlen($strText, Application::encodingType()) == 0) {
                if ($this->strName) {
                    $this->ValidationError = sprintf($this->strLabelForRequired, $this->strName);
                } else {
                    $this->ValidationError = $this->strLabelForRequiredUnnamed;
                }
                return false;
            }
        }

        // Check against minimum length?
        if ($this->intMinLength > 0) {
            if (mb_strlen($strText, Application::encodingType()) < $this->intMinLength) {
                if ($this->strName) {
                    $this->ValidationError = sprintf($this->strLabelForTooShort, $this->strName, $this->intMinLength);
                } else {
                    $this->ValidationError = sprintf($this->strLabelForTooShortUnnamed, $this->intMinLength);
                }
                return false;
            }
        }

        // Check against maximum length?
        if ($this->intMaxLength > 0) {
            if (mb_strlen($strText, Application::encodingType()) > $this->intMaxLength) {
                if ($this->strName) {
                    $this->ValidationError = sprintf($this->strLabelForTooLong, $this->strName, $this->intMaxLength);
                } else {
                    $this->ValidationError = sprintf($this->strLabelForTooLongUnnamed, $this->intMaxLength);
                }
                return false;
            }
        }

        // Check against PHP validation
        if ($this->intValidateFilter && $this->strText) {
            if (!filter_var($this->strText, $this->intValidateFilter, $this->mixValidateFilterOptions)) {
                $this->ValidationError = $this->strLabelForInvalid;
                return false;
            }
        }

        // If we're here, then everything is a-ok.  Return true.
        return true;
    }

    /**
     * This will focus on and do a "select all" on the contents of the textbox
     */
    public function select()
    {
        Application::executeJavaScript(sprintf('qc.getW("%s").select();', $this->strControlId));
    }

    /**
     * Returns the current state of the control to be able to restore it later.
     * @return mixed
     */
    protected function getState()
    {
        return array('text' => $this->Text);
    }

    /**
     * Restore the state of the control.
     * @param mixed $state Previously saved state as returned by GetState above.
     */
    protected function putState($state)
    {
        if (isset($state['text'])) {
            $this->Text = $state['text'];
        }
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
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
            // APPEARANCE
            case "Columns":
                return $this->intColumns;
            case "Format":
                return $this->strFormat;
            case "Text":
                return $this->strText;
            case "LabelForRequired":
                return $this->strLabelForRequired;
            case "LabelForRequiredUnnamed":
                return $this->strLabelForRequiredUnnamed;
            case "LabelForTooShort":
                return $this->strLabelForTooShort;
            case "LabelForTooShortUnnamed":
                return $this->strLabelForTooShortUnnamed;
            case "LabelForTooLong":
                return $this->strLabelForTooLong;
            case "LabelForTooLongUnnamed":
                return $this->strLabelForTooLongUnnamed;
            case "Placeholder":
                return $this->strPlaceholder;
            case 'Value':
                return empty($this->strText) ? null : $this->strText;


            // BEHAVIOR
            case "CrossScripting":
                return $this->strCrossScripting;
            case "MaxLength":
                return $this->intMaxLength;
            case "MinLength":
                return $this->intMinLength;
            case "Rows":
                return $this->intRows;
            case "TextMode":
                return $this->strTextMode;

            // LAYOUT
            //case "Wrap": return $this->blnWrap;

            // FILTERING and VALIDATION
            case "AutoTrim":
                return $this->blnAutoTrim;
            case "SanitizeFilter":
                return $this->intSanitizeFilter;
            case "SanitizeFilterOptions":
                return $this->mixSanitizeFilterOptions;
            case "ValidateFilter":
                return $this->intValidateFilter;
            case "ValidateFilterOptions":
                return $this->mixValidateFilterOptions;
            case "LabelForInvalid":
                return $this->strLabelForInvalid;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /////////////////////////
    // Public Properties: SET
    /////////////////////////
    /**
     * PHP __set magic method implementation
     *
     * @param string $strName Name of the property
     * @param string $mixValue Value of the property
     *
     * @return void
     * @throws Caller|InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        // Setters that do not cause a complete redraw
        switch ($strName) {
            case "Text":
            case "Value":
                try {
                    $val = Type::cast($mixValue, Type::STRING);
                    if ($val !== $this->strText) {
                        $this->strText = $val;
                        $this->addAttributeScript('val', $val);
                    }
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            // APPEARANCE
            case "Columns":
                try {
                    if ($this->intColumns !== ($mixValue = Type::cast($mixValue, Type::INTEGER))) {
                        $this->blnModified = true;
                        $this->intColumns = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Format":
                try {
                    if ($this->strFormat !== ($mixValue = Type::cast($mixValue, Type::STRING))) {
                        $this->blnModified = true;
                        $this->strFormat = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "LabelForRequired":
                try {
                    // no redraw needed
                    $this->strLabelForRequired = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "LabelForRequiredUnnamed":
                try {
                    $this->strLabelForRequiredUnnamed = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "LabelForTooShort":
                try {
                    $this->strLabelForTooShort = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "LabelForTooShortUnnamed":
                try {
                    $this->strLabelForTooShortUnnamed = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "LabelForTooLong":
                try {
                    $this->strLabelForTooLong = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "LabelForTooLongUnnamed":
                try {
                    $this->strLabelForTooLongUnnamed = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Placeholder":
                try {
                    if ($this->strPlaceholder !== ($mixValue = Type::cast($mixValue, Type::STRING))) {
                        $this->blnModified = true;
                        $this->strPlaceholder = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            // BEHAVIOR
            case "CrossScripting":
                try {
                    $this->strCrossScripting = Type::cast($mixValue, Type::STRING);
                    if ($this->strCrossScripting == self::XSS_PHP_SANITIZE) {
                        $this->intSanitizeFilter = FILTER_SANITIZE_STRING;  // Use PHP's built in sanitizer
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "MaxLength":
                try {
                    if ($this->intMaxLength !== ($mixValue = Type::cast($mixValue, Type::INTEGER))) {
                        $this->blnModified = true;
                        $this->intMaxLength = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "MinLength":
                try {
                    if ($this->intMinLength !== ($mixValue = Type::cast($mixValue, Type::INTEGER))) {
                        $this->blnModified = true;
                        $this->intMinLength = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Rows":
                try {
                    if ($this->intRows !== ($mixValue = Type::cast($mixValue, Type::INTEGER))) {
                        $this->blnModified = true;
                        $this->intRows = $mixValue;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "TextMode":
                try {
                    if ($this->strTextMode !== ($strMode = Type::cast($mixValue, Type::STRING))) {
                        $this->blnModified = true;
                        $this->strTextMode = $strMode;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            // LAYOUT
            //case "Wrap":
            // Deprecated. HTML5 has changed the meaning of this, and wrap=off is not consistenly implemented
            // across browers.

            // FILTERING and VALIDATING, no redraw needed
            case "AutoTrim":
                try {
                    $this->blnAutoTrim = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "SanitizeFilter":
                try {
                    $this->intSanitizeFilter = Type::cast($mixValue, Type::INTEGER);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "SanitizeFilterOptions":
                try {
                    $this->mixSanitizeFilterOptions = $mixValue; // can be integer or array. See PHP doc.
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "ValidateFilter":
                try {
                    $this->intValidateFilter = Type::cast($mixValue, Type::INTEGER);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "ValidateFilterOptions":
                try {
                    $this->mixValidateFilterOptions = $mixValue; // can be integer or array. See PHP doc.
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "LabelForInvalid":
                try {
                    $this->strLabelForInvalid = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }

    /**
     * Returns an description of the options available to modify by the designer for the code generator.
     *
     * @return QModelConnectorParam[]
     */
    public static function getModelConnectorParams()
    {
        return array_merge(parent::getModelConnectorParams(), array(
            new QModelConnectorParam(get_called_class(), 'Columns', 'Width of field', Type::INTEGER),
            new QModelConnectorParam(get_called_class(), 'Rows', 'Height of field for multirow field',
                Type::INTEGER),
            new QModelConnectorParam(get_called_class(), 'Format', 'printf format string to use',
                Type::STRING),
            new QModelConnectorParam(get_called_class(), 'Placeholder', 'HTML5 Placeholder attribute',
                Type::STRING, ["translate"=>true]),
            new QModelConnectorParam(get_called_class(), 'ReadOnly', 'Editable or not', Type::BOOLEAN),
            new QModelConnectorParam(get_called_class(), 'TextMode', 'Field type', QModelConnectorParam::SELECTION_LIST,
                array(
                    null => '-',
                    '\\QCubed\\Project\\Control\\TextBox::SEARCH' => 'Search',
                    '\\QCubed\\Project\\Control\\TextBox::MULTI_LINE' => 'Multiline (textarea)',
                    '\\QCubed\\Project\\Control\\TextBox::PASSWORD' => 'Password',
                    '\\QCubed\\Project\\Control\\TextBox::SINGLE_LINE' => 'Single Line',
                    '\\QCubed\\Project\\Control\\TextBox::NUMBER' => 'Number',
                    '\\QCubed\\Project\\Control\\TextBox::EMAIL' => 'Email',
                    '\\QCubed\\Project\\Control\\TextBox::TEL' => 'Telephone',
                    '\\QCubed\\Project\\Control\\TextBox::URL' => 'Url'
                )),
            new QModelConnectorParam(get_called_class(), 'MinLength', 'Minimum length to pass validation', Type::INTEGER),
            new QModelConnectorParam(get_called_class(), 'MaxLength', 'Maximum length to pass validation', Type::INTEGER)

        ));
    }

    /**
     * Returns the generator corresponding to this control.
     *
     * @return Q\Codegen\Generator\GeneratorBase
     */
    public static function getCodeGenerator() {
        return new Q\Codegen\Generator\TextBox();
    }

}
