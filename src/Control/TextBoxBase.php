<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

require_once(dirname(__DIR__, 2) . '/i18n/i18n-lib.inc.php');

use HTMLPurifier_Config;
use QCubed\Codegen\Generator\TextBox;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use Throwable;
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
 * @property integer $Columns               is the "cols" HTML attribute (applicable for MultiLine textboxes)
 * @property string $Format
 * @property string $Text                  are the contents of the textbox, itself?
 * @property string|null $Value            Returns the value of the text. If the text is empty, it will return null.
 *                                            Subclasses can use this to return a specific type of data.
 * @property string $LabelForRequired
 * @property string $LabelForRequiredUnnamed
 * @property string $LabelForTooShort
 * @property string $LabelForTooShortUnnamed
 * @property string $LabelForTooLong
 * @property string $LabelForTooLongUnnamed
 * @property string $Placeholder           HTML5 Only. Placeholder text that gets erased once a user types.
 * @property string $CrossScripting        Can be Allow, HtmlEntities or Deny. Default is denied. Prevents cross-scripting hacks.
 *                                          HtmlEntities automatically calls the php function HTML entities on the input
 *                                          data of the framework. Allow allows everything to pass through without any modification.
 *                                          USE "ALLOW" wisely: Using ALLOW on text entries and then outputting that data
 *                                          allows hackers to perform cross-scripting hacks.
 * @property integer $MaxLength             is the "maxlength" HTML attribute (applicable for SingleLine textboxes)
 * @property integer $MinLength             is the minimum required length to pass validation
 * @property integer $Rows                  is the "rows" HTML attribute (applicable for MultiLine textboxes)
 * @property string $TextMode              a TextMode item. Determines if it is a single or multi-line textbox, and the "type" property of the input.
 * @property boolean $AutoTrim              to automatically remove white space from the beginning and end of data
 * @property boolean $AllowMultipleEmails   to allow multiple emails to be entered into the textbox
 * @property integer $SanitizeFilter        PHP filter constant to apply to incoming data
 * @property mixed $SanitizeFilterOptions PHP filter constants or array to apply to SanitizeFilter option
 * @property integer $ValidateFilter        PHP filter constant to apply to validate with
 * @property mixed $ValidateFilterOptions PHP filter constants or array to apply to ValidateFilter option
 * @property mixed $LabelForInvalid       PHP filter constants or array to apply to ValidateFilter option
 *
 * @package QCubed\Control
 */
abstract class TextBoxBase extends Q\Project\Control\ControlBase
{
    // Text modes
    public const SINGLE_LINE = 'text'; // Single line text inputs INPUT type="text" boxes
    public const MULTI_LINE = 'MultiLine'; // Textarea
    public const PASSWORD = 'password'; //Single-line password inputs
    public const SEARCH = 'search';
    public const NUMBER = 'number';
    public const EMAIL = 'email';
    public const TEL = 'tel';
    public const URL = 'url';

    public const XSS_ALLOW = 'Allow';
    public const XSS_HTML_ENTITIES = 'HtmlEntities';   // simple entity maker
    public const XSS_HTML_PURIFIER = 'HTMLPurifier'; // use html purifier
    public const XSS_PHP_SANITIZE = 'PhpSanitize'; // Use PHP's built-in cleaner
    // Legacy and Deny are removed. Use something else.

    public static string $DefaultCrossScriptingMode = self::XSS_PHP_SANITIZE;

/** @var int */
    protected int $intColumns = 0;
    /** @var string|null */
    protected ?string $strText = null;
    /** @var string|null */
    protected ?string $strLabelForRequired = null;
    /** @var string|null */
    protected ?string $strLabelForRequiredUnnamed = null;
    /** @var string|null */
    protected ?string $strLabelForTooShort = null;
    /** @var string|null */
    protected ?string $strLabelForTooShortUnnamed = null;
    /** @var string|null */
    protected ?string $strLabelForTooLong = null;
    /** @var string|null */
    protected ?string $strLabelForTooLongUnnamed = null;
    /** @var string|null */
    protected ?string $strPlaceholder = '';
    /** @var string */
    protected string $strFormat = '%s';

    // BEHAVIOR
    /** @var int|null */
    protected ?int $intMaxLength = null;
    /** @var int|null */
    protected ?int $intMinLength = null;
    /** @var int|null */
    protected ?int $intRows = null;
    /** @var string Subclasses should not set this directly but rather use the TextMode accessor */
    protected string $strTextMode = self::SINGLE_LINE;
    /** @var string|null */
    protected ?string $strCrossScripting = null;
    /** @var object|null */
    protected ?object $objHTMLPurifierConfig = null;

    // Sanitization and validating
    /** @var bool */
    protected ?bool $blnAutoTrim = false;
    /** @var int|null */
    protected ?int $intSanitizeFilter = null;
    /** @var bool */
    protected ?bool $blnAllowMultipleEmails = false;

    /** @var mixed */
    protected mixed $mixSanitizeFilterOptions = null;
    /** @var int|null */
    protected ?int $intValidateFilter = null;
    /** @var mixed */
    protected mixed $mixValidateFilterOptions = null;
    /** @var string|null */
    protected ?string $strLabelForInvalid = null;

    /**
     * Constructor for the class.
     *
     * @param FormBase|ControlBase $objParentObject The parent object of the control or form.
     * @param string|null $strControlId An optional control ID. If not provided, an ID will be auto-generated.
     * @return void
     * @throws Caller
     */
    public function __construct(FormBase|ControlBase $objParentObject, ?string $strControlId = null)
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
     *  This function allows setting the Configuration for HTMLPurifier
     *  similar to the HTMLPurifierConfig::set() method from the HTMLPurifier API. This creates a custom purifier just
     *  for this textbox. See the Purifier class for setting global options.
     *
     * Set a configuration parameter for the HTML Purifier.
     *
     * @param string $strParameter The name of the configuration parameter to set.
     * @param mixed $mixValue The value to assign to the configuration parameter.
     * @return void
     */
    public function setPurifierConfig(string $strParameter, mixed $mixValue): void
    {
        $this->objHTMLPurifierConfig = HTMLPurifier_Config::createDefault();
        $this->objHTMLPurifierConfig->set($strParameter, $mixValue);
    }

    /**
     * Parse the data posted back via the control.
     * This function basically tests for the Crossscripting rules applied to the TextBox
     * @throws Caller
     */
    public function parsePostData(): void
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
                    $this->strText = Application::purify($this->strText, $this->objHTMLPurifierConfig); // don't save data as HTML entities! Encode at display time.
                    break;
                default:
                    throw new Caller("Unknown cross-scripting setting. Legacy purifier is not supported anymore. Try XSS_PHP_SANITIZE");
            }
        }
    }

    /**
     * Sanitizes the input text based on configured properties.
     *
     * The method trims the text if auto-trim is enabled and applies the configured sanitized filter
     * if a filter is specified and multiple email entries are not allowed.
     *
     * @return void
     */
    protected function sanitize(): void
    {
        if ($this->blnAutoTrim) {
            $this->strText = trim($this->strText);
        }

        if ($this->intSanitizeFilter && !$this->blnAllowMultipleEmails) {
            $sanitizeOptions = $this->mixSanitizeFilterOptions ?? 0;
            $this->strText = filter_var($this->strText, $this->intSanitizeFilter, $sanitizeOptions);
        }
    }

    /**
     * Returns the HTML-formatted string for the control
     * @return string HTML string
     */
    protected function getControlHtml(): string
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
     * to them, some of which we use for validation, and some of which are for dual purposes.
     * We render those here rather than setting the attributes when those are set.
     *
     * @param null $attributeOverrides
     * @param null $styleOverrides
     *
     * @return string
     */
    public function renderHtmlAttributes($attributeOverrides = null, $styleOverrides = null): string
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
             * $strToReturn .= 'wrap="off"' Please note that this is not standard HTML5 and is not supported by all browsers
             * In fact, HTML5 has completely changed its meaning to mean whether the text itself has embedded
             * hard returns inserted when the textarea wraps. Deprecating. We will have to wait for another solution.
             */
            //}
        } else {
            if ($this->intColumns) {
                $attributeOverrides['size'] = $this->intColumns;
            }
            if ($this->strTextMode) {
                $typeStr = $this->strTextMode;
            } else {
                $typeStr = 'text';
            }
            $attributeOverrides['type'] = $typeStr;
        }

        if (strlen($this->strPlaceholder) > 0) {
            $attributeOverrides['placeholder'] = $this->strPlaceholder;
        }

        return parent::renderHtmlAttributes($attributeOverrides, $styleOverrides);
    }


    /**
     * Validates the input of the object based on predefined rules.
     *
     * This function checks whether the input meets certain conditions, such as
     * whether the input is required and not empty, and whether the input string
     * length satisfies the configured minimum length. If validation fails, an
     * appropriate error message is assigned to the ValidationError property.
     *
     * @return bool Returns true if the input satisfies all validation rules, otherwise false.
     */
    public function validate(): bool
    {
        // Copy the text
        $strText = $this->strText;

        // Check if this is a dynamic property that is not defined in the source
        if (!isset($this->blnRequired)) {
            $this->blnRequired = false;
        }
        if (!isset($this->strLabelForRequired)) {
            $this->strLabelForRequired = "Field required!";
        }

        // Check if fields are defined or replace with default values
        $requiredMessage = $this->strName ? sprintf($this->strLabelForRequired, $this->strName) : $this->strLabelForRequiredUnnamed;

        // Check if required is true and the string length is 0
        if ($this->blnRequired && mb_strlen($strText ?? '', Application::encodingType()) == 0) {
            $this->ValidationError = $requiredMessage;
            return false;
        }

        // Check the minimum length
        if (!isset($this->intMinLength)) {
            $this->intMinLength = 0;
        }

        // Check if the string length is less than the configured minimum length
        if ($this->intMinLength > 0 && mb_strlen($strText ?? '', Application::encodingType()) < $this->intMinLength) {
            $this->ValidationError = $this->strName
                ? sprintf($this->strLabelForTooShort, $this->strName, $this->intMinLength)
                : sprintf($this->strLabelForTooShortUnnamed, $this->intMinLength);
            return false;
        }

        // If a validation filter is set, use filter_var validation
        if ($this->intValidateFilter && $this->strText !== '') {
            $isValid = filter_var(
                $this->strText,
                $this->intValidateFilter,
                is_array($this->mixValidateFilterOptions) ? $this->mixValidateFilterOptions : []
            );
            if ($isValid === false) {
                $this->ValidationError = $this->strLabelForInvalid ?? t('Invalid value!');
                return false;
            }
        }

        // Additional validations and actions only here if needed

        return true;
    }

    /**
     * This will focus on and do a "select all" on the contents of the textbox
     * @throws Caller
     */
    public function select(): void
    {
        Application::executeJavaScript(sprintf('qc.getW("%s").select();', $this->strControlId));
    }

    /**
     * Retrieves the state of the object.
     *
     * @return array|null An associative array containing the state information, or null if no state is available.
     */
    protected function getState(): ?array
    {
        return array('text' => $this->Text);
    }

    /**
     * Restore the state of the control.
     * @param mixed $state Previously saved state as returned by GetState above.
     */
    protected function putState(mixed $state): void
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
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            // APPEARANCE
            case "Columns": return $this->intColumns;
            case "Format": return $this->strFormat;
            case "Text": return $this->strText;
            case "LabelForRequired": return $this->strLabelForRequired;
            case "LabelForRequiredUnnamed": return $this->strLabelForRequiredUnnamed;
            case "LabelForTooShort": return $this->strLabelForTooShort;
            case "LabelForTooShortUnnamed": return $this->strLabelForTooShortUnnamed;
            case "LabelForTooLong": return $this->strLabelForTooLong;
            case "LabelForTooLongUnnamed": return $this->strLabelForTooLongUnnamed;
            case "Placeholder": return $this->strPlaceholder;
            case 'Value': return empty($this->strText) ? null : $this->strText;

            // BEHAVIOR
            case "CrossScripting": return $this->strCrossScripting;
            case "MaxLength": return $this->intMaxLength;
            case "MinLength": return $this->intMinLength;
            case "Rows": return $this->intRows;
            case "TextMode": return $this->strTextMode;

            // LAYOUT
            //case "Wrap": return $this->blnWrap;

            // FILTERING and VALIDATION
            case "AutoTrim": return $this->blnAutoTrim;
            case "SanitizeFilter": return $this->intSanitizeFilter;
            case "SanitizeFilterOptions": return $this->mixSanitizeFilterOptions;
            case "ValidateFilter": return $this->intValidateFilter;
            case "ValidateFilterOptions": return $this->mixValidateFilterOptions;
            case "LabelForInvalid": return $this->strLabelForInvalid;

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
     * @param mixed $mixValue Value of the property
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     * @throws Throwable Exception
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        // Setters that do not cause a complete redrawing
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
                        $this->intSanitizeFilter = FILTER_SANITIZE_SPECIAL_CHARS;  // Use PHP's built-in sanitizer
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
                if (!is_int($mixValue) && !is_array($mixValue)) {
                    throw new InvalidCast("SanitizeFilterOptions should be an integer or an array.");
                }
                $this->mixSanitizeFilterOptions = $mixValue;
                break;

            case "ValidateFilter":
                try {
                    $this->intValidateFilter = Type::cast($mixValue, Type::INTEGER);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "ValidateFilterOptions":
                if (!is_int($mixValue) && !is_array($mixValue)) {
                    throw new InvalidCast("ValidateFilterOptions should be an integer or an array.");
                }
                $this->mixValidateFilterOptions = $mixValue;
                break;

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
     * Returns a description of the options that can be modified by the code generator designer.
     *
     * @return QModelConnectorParam[]
     * @throws Caller
     */
    public static function getModelConnectorParams(): array
    {
        return array_merge(parent::getModelConnectorParams(), array(
            new QModelConnectorParam(get_called_class(), 'Columns', 'Width of a field', Type::INTEGER),
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
     * Retrieves an instance of the code generator.
     *
     * @return TextBox An instance of the TextBox code generator.
     */
    public static function getCodeGenerator(): Q\Codegen\Generator\TextBox
    {
        return new Q\Codegen\Generator\TextBox(__CLASS__);
    }

}
