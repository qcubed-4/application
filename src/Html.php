<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed;

use QCubed\Exception\Caller;
use QCubed\Project\Application;

/**
 * An abstract utility class to handle HTML tag rendering, as well as utilities to render
 * pieces of HTML and CSS code.  All methods are static.
 */
abstract class Html {

    const IS_VOID = true;

    // Common URL Protocols
    const HTTP = 'http://';
    const HTTPS = 'https://';
    const FTP = 'ftp://';
    const SFTP = 'sftp://';
    const SMB = 'smb://';

    // Font Families
    const FONT_FAMILY_ARIAL = 'Arial, Helvetica, sans-serif';
    const FONT_FAMILY_HELVETICA = 'Helvetica, Arial, sans-serif';
    const FONT_FAMILY_TAHOMA = 'Tahoma, Arial, Helvetica, sans-serif';
    const FONT_FAMILY_TREBUCHET_MS = "'Trebuchet MS', Arial, Helvetica, sans-serif";
    const FONT_FAMILY_VERDANA = 'Verdana, Arial, Helvetica, sans-serif';
    const FONT_FAMILY_TIMES_NEW_ROMAN = "'Times New Roman', Times, serif";
    const FONT_FAMILY_GEORGIA = "Georgia, 'Times New Roman', Times, serif";
    const FONT_FAMILY_LUCIDA_CONSOLE = "'Lucida Console', 'Courier New', Courier, monospaced";
    const FONT_FAMILY_COURIER_NEW = "'Courier New', Courier, monospaced";
    const FONT_FAMILY_COURIER = 'Courier, monospaced';

    const TEXT_ALIGN_LEFT = "left";
    const TEXT_ALIGN_RIGHT = "right";

    // type property for ordered lists
    const OL_NUMBERS = '1';
    const OL_UPPERCASE_LETTERS = 'A';
    const OL_LOWERCASE_LETTERS = 'a';
    const OL_UPPERCASE_ROMAN = 'I';
    const OL_LOWERCASE_ROMAN = 'i';

    // list-style-type property for an unordered list
    const UL_DISC = 'disc';
    const UL_CIRCLE = 'circle';
    const UL_SQUARE = 'square';
    const UL_NONE = 'none';

    /**
     * Contains/Defines Overflow CSS Styles to be used on QControls
     */
    const OVERFLOW_NOT_SET = 'NotSet';
    const OVERFLOW_AUTO = 'auto';
    const OVERFLOW_HIDDEN = 'hidden';
    const OVERFLOW_SCROLL = 'scroll';
    const OVERFLOW_VISIBLE = 'visible';

    /**
     * This faux constructor method throws a caller exception.
     * The CSS object should never be instantiated, and this constructor
     * override simply guarantees it.
     *
     * @throws Caller
     */
    public final function __construct() {
        throw new Caller('\\QCubed\\Html should never be instantiated.  All methods and variables are publicly statically accessible.');
    }

    /**
     * Renders an HTML tag with the given attributes and inner HTML.
     *
     * If the innerHtml is detected as being wrapped in an HTML tag of some sort, it will attempt to format the code so that
     * it has a structured view in a browser, with the inner HTML indented and on a new line in between the tags. You
     * can turn this off by setting QCUBED_MINIMIZE or bypassing in true to $blnNoSpace.
     *
     * There are a few special cases to consider:
     * - Void elements will not be formatted to avoid adding unnecessary white space since these are generally
     *   inline elements
     * - Non-void elements always use internal newlines, even in QCUBED_MINIMIZE mode. This is to prevent different behavior
     *   from appearing in QCUBED_MINIMIZE mode on inline elements, because inline elements with internal space will render with space to separate
     *   from surrounding elements. Usually, this is not an issue, but in the special situations where you really need inline
     *   elements to be right up against their siblings, set $blnNoSpace to true.
     *
     *
     * @param string $strTag				The tag name
     * @param null|mixed 	$mixAttributes 		String of attribute values or array of attribute values.
     * @param string|null $strInnerHtml 		The HTML to print between the opening and closing tags. This will NOT be escaped.
     * @param boolean $blnIsVoidElement 	True to print as a tag with no closing tag.
     * @param boolean $blnNoSpace		 	Renders with no white-space. Useful in special inline situations.
     * @return string						The rendered html tag
     */
    public static function renderTag(string $strTag, mixed $mixAttributes, ?string $strInnerHtml = null, ?bool $blnIsVoidElement = false, ?bool $blnNoSpace = false): string
    {
        assert (!empty($strTag));
        $strToReturn = '<' . $strTag;

        if ($mixAttributes) {
            if (is_string($mixAttributes)) {
                $strToReturn .=  ' ' . trim($mixAttributes);
            } else {
                // assume an array
                $strToReturn .=  self::renderHtmlAttributes($mixAttributes);
            }
        }

        if ($blnIsVoidElement) {
            $strToReturn .= ' />'; // conforms to both XHTML and HTML5 for both normal and foreign elements
        }
        else {
            // We check if $strInnerHtml is null and replace it with an empty string if it is
            $strInnerHtml = $strInnerHtml === null ? '' : $strInnerHtml;

            if ($blnNoSpace || !str_starts_with(trim($strInnerHtml), '<')) {
                $strToReturn .= '>' . $strInnerHtml . '</' . $strTag . '>';
            } else {
                // the hardcoded newlines below are important to prevent different drawing behavior in MINIMIZE mode
                $strToReturn .= '>' . "\n" . _indent(trim($strInnerHtml)) .  "\n" . '</' . $strTag . '>' . _nl();
            }
        }

        return $strToReturn;
    }

    /**
     * Renders an input element with a label tag. Uses separate styling for the label and the input object.
     * In particular, this gives you the option of wrapping the input with a label (which is what Bootstrap
     * expects on checkboxes) or putting the label next to the object (which is what jQueryUI expects).
     *
     * Note that if you are not setting $blnWrapped, it is up to you to insert the "for" attribute into
     * the label attributes.
     *
     * @param string $strLabel The text content for the label element.
     * @param bool $blnTextLeft Determines whether the label appears to the left of the input element.
     * @param string $strAttributes The attributes to be applied to the input element.
     * @param string $strLabelAttributes The attributes to be applied to the label element.
     * @param bool $blnWrapped Specifies whether the label and input should be wrapped together within a single label element.
     * @return string The rendered HTML string with the input element and associated label.
     */
    public static function renderLabeledInput(string $strLabel, bool $blnTextLeft, string $strAttributes, string $strLabelAttributes, bool $blnWrapped): string
    {
        $strHtml = trim(self::renderTag('input', $strAttributes, null, true));

        if ($blnWrapped) {
            if ($blnTextLeft) {
                $strCombined = $strLabel .  $strHtml;
            } else {
                $strCombined = $strHtml . $strLabel;
            }

            $strHtml = self::renderTag('label', $strLabelAttributes, $strCombined);
        }
        else {
            $strLabel = trim(self::renderTag('label', $strLabelAttributes, $strLabel));
            if ($blnTextLeft) {
                $strHtml = $strLabel .  $strHtml;
            } else {
                $strHtml = $strHtml . $strLabel;
            }
        }
        return $strHtml;
    }

    /**
     * Formats the given value as a CSS-compliant length.
     *
     * If the input is numeric and non-zero, it appends "px" to the value.
     * If the input is zero, it returns the value as is.
     * If the input is not numeric, it is returned as is without modification.
     *
     * @param mixed $strValue The input value to be formatted, which can be numeric or a string.
     * @return string The formatted length with or without "px", or the unmodified string if input is non-numeric.
     */
    public final static function formatLength(mixed $strValue): string
    {
        if (is_numeric($strValue)) {
            if (0 == $strValue) {
                if (!is_int($strValue)) {
                    $fltValue = floatval($strValue);
                    return sprintf('%s', $fltValue);
                } else {
                    return sprintf('%s', $strValue);
                }
            } else {
                if (!is_int($strValue)) {
                    $fltValue = floatval($strValue);
                    return sprintf('%spx', $fltValue);
                } else {
                    return sprintf('%spx', $strValue);
                }
            }
        } else {
            return sprintf('%s', $strValue);
        }
    }

    /**
     * Updates the given length value based on the specified new length input.
     *
     * This method supports updating the length through direct assignment or applying
     * mathematical operations such as addition, subtraction, multiplication, or division.
     * If a mathematical operator is detected in the new length input, the corresponding
     * operation is performed on the old length's numeric value, preserving its units. If
     * no mathematical operator is present, the new length is directly formatted and set
     * as the updated length. The method determines if any change has occurred and adjusts
     * the old length accordingly.
     *
     * @param mixed &$strOldLength A reference to the old length value, typically a string in a
     *                             CSS-compliant format (e.g., "10px"). This value gets modified
     *                             if a valid change is applied.
     * @param mixed $newLength The new length input, which can include mathematical operations
     *                         (e.g., "+5px") or directly specify a new CSS-compliant length value.
     * @return bool True if the length was updated successfully, false if no changes were made.
     */
    public static function setLength(mixed &$strOldLength, mixed $newLength): bool
    {
        if ($newLength && preg_match('#^(\+|\-|/|\*)(.+)$#',$newLength, $matches)) { // do math operation
            $strOperator = $matches[1];
            $newValue = $matches[2];
            assert (is_numeric($newValue));
            if (!$strOldLength) {
                $oldValue  = 0;
                $oldUnits = 'px';
            } else {
                $oldValue = filter_var ($strOldLength, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                if (preg_match('/([A-Z]+|[a-z]+|%)$/', $strOldLength, $matches)) {
                    $oldUnits = $matches[1];
                } else {
                    $oldUnits = 'px';
                }
            }

            switch ($strOperator) {
                case '+':
                    $newValue = $oldValue + $newValue;
                    break;

                case '-':
                    $newValue = $oldValue - $newValue;
                    break;

                case '/':
                    $newValue = $oldValue / $newValue;
                    break;

                case '*':
                    $newValue = $oldValue * $newValue;
                    break;
            }
            if ($newValue != $oldValue) {
                $strOldLength = $newValue . $oldUnits; // update returned value
                return true;
            } else {
                return false; // nothing changed
            }
        } else { // no math operation
            $newLength = self::formatLength($newLength);

            if ($strOldLength !== $newLength) {
                $strOldLength = $newLength;
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Adds new CSS class names to an existing class list, ensuring no duplicates.
     *
     * This function takes an existing list of CSS class names and appends new class names to it,
     * only if they are not already present. Class names are separated by spaces.
     *
     * @param string &$strClassList The current list of CSS class names, passed by reference.
     *                              It may be empty or already contain class names.
     * @param string $strNewClasses A space-separated string of new class names to be added.
     * @return bool Returns true if at least one new class was added, otherwise false.
     */
    public static function addClass(?string &$strClassList, string $strNewClasses): bool
    {
        $strNewClasses = trim($strNewClasses);
        if (empty($strNewClasses)) return false;

        if (empty ($strClassList)) {
            $strCurrentClasses = array();
        }
        else {
            $strCurrentClasses = explode(' ', $strClassList);
        }

        $blnChanged = false;
        foreach (explode (' ', $strNewClasses) as $strClass) {
            if ($strClass && !in_array ($strClass, $strCurrentClasses)) {
                $blnChanged = true;
                if (!empty ($strClassList)) {
                    $strClassList .= ' ';
                }
                $strClassList .= $strClass;
            }
        }

        return $blnChanged;
    }

    /**
     * Removes specified CSS class names from a given class list.
     *
     * This method checks if the class list contains any of the class names to be removed
     * and removes them if present. Updates the original class list reference and indicates
     * whether any modifications were made.
     *
     * @param string &$strClassList A reference to the original list of CSS class names, separated by spaces.
     * @param string $strCssNamesToRemove A space-separated string of CSS class names to be removed from the class list.
     * @return bool True if one or more class names were removed, false otherwise.
     */
    public static function removeClass(string &$strClassList, string $strCssNamesToRemove): bool
    {
        $strNewCssClass = '';
        $blnRemoved = false;
        $strCssNamesToRemove = trim($strCssNamesToRemove);
        if (empty($strCssNamesToRemove)) return false;

        if (empty ($strClassList)) {
            $strCurrentClasses = array();
        }
        else {
            $strCurrentClasses = explode(' ', $strClassList);
        }
        $strRemoveArray = explode (' ', $strCssNamesToRemove);

        foreach ($strCurrentClasses as $strCssClass) {
            if ($strCssClass = trim($strCssClass)) {
                if (in_array($strCssClass, $strRemoveArray)) {
                    $blnRemoved = true;
                }
                else {
                    $strNewCssClass .= $strCssClass . ' ';
                }
            }
        }
        if ($blnRemoved) {
            $strClassList = trim($strNewCssClass);
        }
        return $blnRemoved;
    }

    /**
     * Removes classes from a space-separated list of classes that start with a specific prefix.
     *
     * This function iterates through the provided list of classes and removes any class that begins
     * with the specified prefix. If at least one class is removed, it updates the original list
     * and returns true. Otherwise, it returns false.
     *
     * @param string &$strClassList A space-separated list of classes to be filtered.
     *                              This parameter is passed by reference and will be updated
     *                              to exclude classes with the specified prefix.
     * @param string $strPrefix The prefix to match against each class in the list.
     *                          Any class that starts with this prefix will be removed.
     * @return bool True if one or more classes were removed, otherwise false.
     */
    public static function removeClassesByPrefix(string &$strClassList, string $strPrefix): bool
    {
        $aRet = array();
        $blnChanged = false;
        if ($strClassList) foreach (explode (' ', $strClassList) as $strClass) {
            if (!str_starts_with($strClass, $strPrefix)) {
                $aRet[] = $strClass;
            }
            else {
                $blnChanged = true;
            }
        }
        $strClassList = implode (' ', $aRet);
        return $blnChanged;
    }

    /**
     * Renders an associative array of HTML attributes into a string format.
     *
     * Each key-value pair in the array is converted into an HTML attribute, where the key
     * represents the attribute name and the value corresponds to the attribute's value.
     * - If a value is `false`, only the attribute name is included in the output.
     * - If a value is `null`, the attribute is excluded from the output.
     * - All attribute values are properly escaped using HTML entities.
     *
     * @param array $attributes An associative array where keys are attribute names and values are attribute values.
     * @return string A string containing the rendered HTML attributes, ready to be included in an HTML element tag.
     */
    public static function renderHtmlAttributes(array $attributes): string
    {
        $strToReturn = '';
        if ($attributes) {
            foreach ($attributes as $strName=>$strValue) {
                if ($strValue === false) {
                    $strToReturn .= (' ' . $strName);
                } elseif (!is_null($strValue)) {
                    $strToReturn .= (' ' . $strName . '="' . htmlspecialchars($strValue, ENT_COMPAT | ENT_HTML5, Application::encodingType()) . '"');
                }
            }
        }
        return $strToReturn;
    }


    /**
     * Converts an associative array of styles into a formatted CSS style string.
     *
     * The method takes an array of styles where the keys represent CSS property names
     * and the values represent the corresponding properties' values. The styles are
     * joined together as a single string with each property: a value pair separated by a semicolon.
     *
     * @param array|null $styles An associative array where keys are CSS property names and values are their associated values. If null or empty, an empty string is returned.
     * @return string A CSS style string formatted as "property:value; property:value". If no styles are provided, an empty string is returned.
     */
    public static function renderStyles(?array $styles): string
    {
        if (!$styles) return '';
        return implode('; ', array_map(
            function ($v, $k) { return $k . ':' . $v; },
            $styles,
            array_keys($styles))
        );
    }

    /**
     * Generates an HTML comment string based on the provided text.
     *
     * If the minimized mode is enabled in the application and the removal flag is set, an empty string is returned.
     * Otherwise, the comment is formatted with newline characters and encapsulated in HTML comment tags.
     *
     * @param string $strText The text to be included inside the HTML comment.
     * @param bool $blnRemoveOnMinimize Specifies whether the comment should be removed when minimize mode is active. Default is true.
     * @return string The generated HTML comment or an empty string based on the minimized mode and removal flag.
     */
    public static function comment(string $strText, bool $blnRemoveOnMinimize = true): string
    {
        if ($blnRemoveOnMinimize && Application::instance()->minimize()) {
            return '';
        }
        return  _nl() . '<!-- ' . $strText . ' -->' . _nl();

    }

    /**
     * Generate a URL from components. This URL can be used in the Application::redirect function or applied to
     * an anchor tag by setting the href attribute.
     *
     * You can also use this to modify a URL by passing a complete URL in the location. The URL will be modified by the parameters given.
     *
     * @param string $strLocation			absolute or relative path to resource, depending on your protocol. If not needed, enter an empty string. Can be a complete URL.
     * @param array|null $queryParams		key->value array of query parameters to add to the location.
     * @param string|null $strAnchor		anchor to add to the url
     * @param string|null $strScheme		protocol if specifying a resource outside of the current server (i.e., http)
     * @param string|null $strHost			server that the resource is on. Required if specifying a scheme.
     * @param string|null $strUser			user name if needed. Some protocols like mailto and ftp need this
     * @param string|null $strPassword		password if needed. Note that the password is sent in the clear.
     * @param string|null $intPort			port if different from default
     * @return string
     */
    public static function makeUrl(
        string  $strLocation,
        ?array  $queryParams = null,
        ?string $strAnchor = null,
        ?string $strScheme = null,
        ?string $strHost = null,
        ?string $strUser = null,
        ?string  $strPassword = null,
        ?string $intPort = null
    ): string {
        // Decompose
        if ($strLocation) {
            $params = parse_url($strLocation);
        }

        if (!empty($strLocation) && isset($params['path'])) {
            $strUrl = $params['path'];
        } else {
            $strUrl = '';
        }

        if (isset($params['query'])) {
            parse_str($params['query'], $queryParams2);
            if ($queryParams) {
                $queryParams = array_merge($queryParams2, $queryParams);
            } else {
                $queryParams = $queryParams2;
            }
        }

        if (empty($strAnchor) && isset($params['fragment'])) {
            $strAnchor = $params['fragment'];
        }

        if (empty($strScheme) && isset($params['scheme'])) {
            $strScheme = $params['scheme'];
        }

        if (empty($strHost) && isset($params['host'])) {
            $strHost = $params['host'];
        }

        if (empty($strUser) && isset($params['user'])) {
            $strUser = $params['user'];
        }
        if (empty($strPassword) && isset($params['pass'])) {
            $strPassword = $params['pass'];
        }
        if (empty($intPort) && isset($params['port'])) {
            $intPort = $params['port'];
        }

        if ($queryParams)  {
            $strUrl .= '?' . http_build_query($queryParams);
        }
        if ($strAnchor) {
            $strUrl .= '#' . urlencode($strAnchor);
        }

        // More complex URLs. Once you specify protocol, you will need to specify the server too.
        if ($strScheme) {
            assert(!empty($strHost));

            // We do not do any checking at this point since URLs can be complex. It is up to you to build a correct URL.
            // If you use a protocol that expects an absolute path, you must start with a slash (http), or a relative path (mailto), leave the slash off.

            // Build a server portion.
            if ($intPort) {
                $strHost .= ':' . $intPort;
            }
            if ($strUser) {
                $strUser = rawurlencode($strUser);
                if ($strPassword) {
                    $strUser = $strUser . ':' . rawurlencode($strPassword);
                }
                $strHost = $strUser . '@' . $strHost;
            }
            $strUrl = $strScheme . $strHost . $strUrl;
        }
        return $strUrl;
    }

    /**
     * Constructs a mailto URL based on user and server details, with optional query parameters and a display name.
     *
     * The method builds a properly encoded mailto URL for email links.
     * It supports adding query parameters such as subject or body, and can include a display name for the recipient.
     *
     * @param string $strUser The email username (local part before the "@" symbol).
     * @param string|null $strServer The email server (domain), optional. If null, only the username is used.
     * @param array|null $queryParams An associative array of query parameters to append to the mailto URL, optional.
     * @param string|null $strName A display name for the email recipient, optional.
     * @return string The encoded mailto URL string.
     */
    public static function mailToUrl(
        string $strUser,
        ?string $strServer = null,
        ?array $queryParams = null,
        ?string $strName = null
    ): string
    {
        if ($strServer) {
            $strUrl = $strUser . '@' . $strServer;
        } else {
            $strUrl = $strUser;
        }
        if ($strName) {
            $strUrl = '"' . $strName . '"' . '<' . $strUrl . '>';
        }
        $strUrl = rawurlencode($strUrl);
        if ($queryParams) {
            $strUrl .= '?' . http_build_query($queryParams, null, null, PHP_QUERY_RFC3986);
        }
        return $strUrl;
    }

    /**
     * Renders an HTML anchor tag with the specified URL, text, attributes, and an option to encode text content.
     *
     * Generates an anchor ("a") tag using the given URL and text.
     * Optionally, applies HTML entity encoding to the text content and includes any additional attributes provided.
     *
     * @param string $strUrl The URL to be assigned to the anchor's "href" attribute.
     * @param string $strText The text content to display for the anchor.
     * @param array|null $attributes An associative array of additional HTML attributes to include in the anchor tag. Default is null.
     * @param bool $blnHtmlEntities Whether to apply HTML entity encoding to the text content. Default is true.
     * @return string The rendered HTML anchor tag as a string.
     */
    public static function renderLink(string $strUrl, string $strText, ?array $attributes = null, bool $blnHtmlEntities = true): string
    {
        $attributes["href"] = $strUrl;
        if ($blnHtmlEntities) {
            $strText = QString::htmlEntities($strText);
        }
        return self::renderTag("a", $attributes, $strText);
    }

    /**
     * Converts the given string into a safe HTML-renderable format.
     *
     * The method escapes special HTML characters in the input text to prevent XSS attacks
     * and converts newline characters into HTML line breaks for proper display in an HTML context.
     *
     * @param string $strText The input string that needs to be processed for safe HTML output.
     * @return string The processed string with escaped HTML characters and converted line breaks.
     */
    public static function renderString(string $strText): string
    {
        return nl2br(htmlspecialchars($strText, ENT_COMPAT | ENT_HTML5, Application::encodingType()));
    }

    /**
     * Renders an HTML table from the provided data array.
     *
     * A quick way to render an HTML table from an array of data. For more control or to automatically render
     * data that may change, see QHtmlTable and its subclasses.
     *
     * Example:
     * $data = [
     * ['name'=>'apple', 'type'=>'fruit'],
     * ['name'=>'carrot', 'type'=>'vegetable']
     * ];
     *
     * print(Html::renderTable($data, ['name','type'], ['class'=>'mytable'], ['Name', 'Type']);
     *
     * Converts the input data into an HTML table string, including optional headers, attributes, and text escaping.
     * Specific fields from the data can be used to populate the table, and headers can be included optionally.
     *
     * @param array $data The input data to be rendered in the table. Each element should represent a row.
     * @param array|null $strFields Optional list of specific fields to extract and display in the table.
     * @param array|null $attributes Optional associative array of HTML attributes for the table element.
     * @param array|null $strHeaderTitles Optional list of header titles for the table's columns.
     * @param int|null $intHeaderColumnCount Number of columns to be treated as headers in the table body rows.
     * @param bool $blnHtmlEntities Whether to escape cell content using HTML entities for safe display. Default is true.
     * @return string The rendered HTML table as a string.
     */
    public static function renderTable(array $data, ?array $strFields = null, ?array $attributes = null, ?array $strHeaderTitles = null, ?int $intHeaderColumnCount = 0, bool $blnHtmlEntities = true): string
    {
        if (!$data) {
            return '';
        }

        $strHeader = '';
        if ($strHeaderTitles) {
            foreach ($strHeaderTitles as $strHeaderTitle) {
                if ($blnHtmlEntities) {
                    $strHeaderTitle = QString::htmlEntities($strHeaderTitle);
                }
                $strHeader .= '<th>' . $strHeaderTitle . '</th>';
            }
            $strHeader = '<thead><tr>' . $strHeader . '</tr></thead>';
        }
        $strBody = '';
        foreach ($data as $row) {
            $intFieldNum = 0;
            $strRow = '';
            if ($strFields) {
                foreach ($strFields as $strField) {
                    $intFieldNum ++;
                    $strItem = '';
                    if (is_object($row)) {
                        $strItem = $row->$strField;
                    } elseif (isset($row[$strField])) {
                        $strItem = $row[$strField];
                    }
                    if ($blnHtmlEntities) {
                        $strItem = QString::htmlEntities($strItem);
                    }
                    if ($intFieldNum <= $intHeaderColumnCount) {
                        $strRow .= '<th>' . $strItem . '</th>';
                    } else {
                        $strRow .= '<td>' . $strItem . '</td>';
                    }
                }
            } else {
                foreach ($row as $strItem) {
                    $intFieldNum ++;
                    if ($blnHtmlEntities) {
                        $strItem = QString::htmlEntities($strItem);
                    }
                    if ($intFieldNum <= $intHeaderColumnCount) {
                        $strRow .= '<th>' . $strItem . '</th>';
                    } else {
                        $strRow .= '<td>' . $strItem . '</td>';
                    }
                }
            }
            $strRow = '<tr>' . $strRow . '</tr>';
            $strBody .= $strRow;
        }
        $strBody = '<tbody>' . $strBody . '</tbody>';
        return self::renderTag('table', $attributes , $strHeader . $strBody);
    }

}