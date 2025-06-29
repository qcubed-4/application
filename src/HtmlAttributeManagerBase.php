<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed;

use QCubed\Css\DisplayType;
use QCubed\Exception\InvalidCast;
use QCubed\Exception\Caller;
use QCubed\Exception\UndefinedProperty;

/**
 * Class HtmlAttributeManagerBase
 *
 * A base class for objects that manage HTML attributes. Uses array functions and defines a couple of arrays to manage
 * the attributes. Values will be HTML escaped when printed.
 *
 * Includes:
 * - helper functions to manage the class, style, and data-* attributes specially.
 * - helper functions to manage 'name-*' classes that are found in CSS frameworks like
 *   Bootstrap and Foundation.
 * - helpers for __get and __set functions, partially for backwards compatibility and also to make the setting
 *   of some attributes and styles easier (so you don't have to remember how to set them).
 *
 * Usage: Use the helper functions to set up your styles, classes, data-* and other attributes, then call
 *	      renderHtmlAttributes() to render the attributes to insert them into a tag.
 *
 *
 * @property string $AccessKey allows you to specify what Alt-Letter combination will automatically focus that control on the form
 * @property string $BackColor sets the CSS background-color of the control
 * @property string $BackgroundImageUrl set the url for a background image
 * @property string $BorderColor sets the CSS border-color of the control
 * @property string $BorderWidth sets the CSS border-width of the control
 * @property string $BorderStyle is used to set CSS border-style by {@link \QCubed\Css\BorderStyleType}
 * @property string $BorderCollapse    defines the BorderCollapse CSS style for a table
 * @property string $CssClass sets or returns the CSS class for this control. When setting, if you precede the class name
 *  with a plus sign (+), it will add the class(es) to the currently existing classes, rather than replace them. Can add or
 *  set more than one class at once by separating names with a space.
 * @property string $Cursor is used to set CSS cursor property by {@link \QCubed\Css\Cursor}
 * @property boolean $Display shows or hides the control using the CSS display property.  In either case, the control is
 *  still rendered on the page. See the Visible property if you wish to not render a control.
 * @property string $DisplayStyle is used to set CSS display property by {@link \QCubed\Css\Display}
 * @property boolean $Enabled specifies whether or not this is enabled (it will grey out the control and make it
 *  inoperable if set to true.
 * @property boolean $FontBold sets the font bold or normal
 * @property boolean $FontItalic sets the Font italic or normal
 * @property string $FontNames sets the name of used fonts
 * @property boolean $FontOverline
 * @property string $FontSize sets the font-size of the control
 * @property boolean $FontStrikeout
 * @property boolean $FontUnderline sets the font underlined
 * @property string $ForeColor sets the CSS color property, which controls text color
 * @property string $Height
 * @property string $Left CSS left property
 * @property integer $Opacity sets the opacity of the control (0-100)
 * @property string $Overflow is used to set CSS overflow property by {@link \QCubed\Css\Overflow}
 * @property string $Position is used to set CSS position property by {@link \QCubed\Css\Position}
 * @property integer $TabIndex specifies the index/tab order on a form
 * @property string $ToolTip specifies the text to be displayed when the mouse is hovering over the control
 * @property string $Top
 * @property string $Width
 * @property string $TextAlign sets the CSS text-align property
 * @property string $VerticalAlign sets the CSS vertical-align property
 * @property-write mixed $Padding sets the CSS padding property. Will accept a string, which is passed verbatim, or
 *  an array, either numerically indexed, in which case it is in top, right, bottom, left order, or keyed with the
 *  names 'top', 'right', 'bottom', 'left'.
 * @property-write mixed $Margin sets the CSS margin property. Will accepts a string, which is passed verbatim, or
 *  an array, either numerically indexed, in which case it is in top, right, bottom, left order, or keyed with the
 *  names 'top', 'right', 'bottom', 'left'
 * @property-write array $Data a key/value array of data-* items to set. Keys can be in camelCase notation, in which case they will be
 *  converted to dashed notation. Use GetDataAttribute() to retrieve the value of a data attribute.
 * @property boolean $NoWrap sets the CSS white-space property to nowrap
 * @property boolean $ReadOnly is the "readonly" HTML attribute (making a textbox "ReadOnly" similar to setting the textbox to Enabled
 *  Readonly textboxes are selectable, and their values get posted. Disabled textboxes are not selectable and values do not post.
 * @property string $AltText text used for 'alt' attribute in images
 * @property string $OrderedListType type for ordered lists. Expects a QOrderedListType.
 * @property string $UnorderedListStyle style for unordered lists. Expects a QUnorderedListType.
 * @package QCubed
 * @was QHtmlAttributeManagerBase
 */

class HtmlAttributeManagerBase extends ObjectBase
{
    /**
     * An array holding HTML attributes and their values.
     *
     * This array is used to store key-value pairs where the key represents the attribute name,
     * and the value represents the attribute's assigned value. Attributes stored within this
     * array are typically used to dynamically generate and render HTML elements with their
     * associated attributes.
     *
     * The array may include attributes such as
     * - Standard HTML attributes like ID, name, title
     * - Styling attributes like class, style
     * - Custom data attributes (e.g., data-* attributes)
     * - Any other valid HTML attributes supported by the target elements
     */
	protected array $attributes = array();
    /**
     * Array $styles
     *
     * This array is used to store CSS styles as key-value pairs where the key represents the CSS property
     * (e.g., 'color', 'font-size'), and the value represents the corresponding value assigned to that property.
     *
     * The array is typically leveraged to dynamically manage and render inline styles for an HTML element,
     * allowing for programmatic manipulation of size, appearance, and other visual aspects.
     *
     * Usage involves adding, modifying, or removing key-value pairs representing CSS styles,
     * which can then be processed into a valid CSS string for inclusion in HTML.
     */
    protected array $styles = array();

    /**
     * Sets the value of an HTML attribute. If the attribute value is modified or removed, the object is marked as modified.
     *
     * @param string $strName The name of the HTML attribute to set.
     * @param string|null $strValue The value to assign to the attribute. Pass null to remove the attribute.
     * @return bool Returns true if the attribute value was modified or removed; false otherwise.
     */
	public function setHtmlAttribute(string $strName, ?string $strValue): bool {
		if (!is_null($strValue)) {
			if (!isset($this->attributes[$strName]) || $this->attributes[$strName] !== $strValue) {
				// only make a change if it has actually changed value.
				$this->attributes[$strName] = $strValue;
				$this->markAsModified();
				return true;
			}
		} else {
			if (isset($this->attributes[$strName])) {
				unset($this->attributes[$strName]);
				$this->markAsModified();
				return true;
			}
		}
		return false;
	}

    /**
     * Removes an HTML attribute from the internal attributes collection by setting its value to null.
     *
     * @param string $strName The name of the HTML attribute to be removed.
     * @return void
     */
	public function removeHtmlAttribute(string $strName): void {
		$this->setHtmlAttribute($strName, null);
	}

    /**
     * Retrieves the value of a specified HTML attribute.
     *
     * @param string $strName The name of the HTML attribute to retrieve.
     * @return string|null The value of the specified HTML attribute, or null if the attribute does not exist.
     */
	public function getHtmlAttribute(string $strName): ?string {
        return $this->attributes[$strName] ?? null;
	}

    /**
     * Merges and processes HTML attributes with optional overrides for attributes and styles.
     *
     * @param array|null $attributeOverrides An optional array of attribute overrides to merge into the existing attributes.
     * @param array|null $styleOverrides An optional array of style overrides to generate and include in the `style` attribute.
     * @param array|string|null $selection An optional list of attribute keys to filter the resulting attributes.
     *
     * @return array The processed array of HTML attributes, including any applied overrides and filters.
     */
    public function getHtmlAttributes(?array $attributeOverrides = null, ?array $styleOverrides = null, array|string|null $selection = null): array {
        // Start with the initial attributes
        $attributes = $this->attributes;

        // Mix in the value of override attributes if they are set
        if ($attributeOverrides) {
            $attributes = array_merge($attributes, $attributeOverrides);
        }

        // Render the styles and set the 'style' attribute
        $strStyles = $this->renderCssStyles($styleOverrides);
        if ($strStyles) {
            $attributes['style'] = $strStyles;
        }

        // Check and process $selection
        if ($selection) {
            // If $selection is a string, convert it to an array
            $selectionArray = is_string($selection)
                ? array_map('trim', explode(',', $selection)) // Trim every component
                : $selection;

            // Filter the required attributes according to the keys
            $attributes = array_intersect_key($attributes, array_flip($selectionArray));
        }

        return $attributes;
    }

    /**
     * Checks if a specific HTML attribute exists.
     *
     * @param string $strName The name of the HTML attribute to check.
     * @return bool Returns true if the attribute exists, false otherwise.
     */
	public function hasHtmlAttribute(string $strName): bool {
		return (isset($this->attributes[$strName]));
	}

    /**
     * Sets a custom data attribute by converting the given camelCase name
     * to a data-* attribute name format and assigning the provided value.
     *
     * @param string $strName The name of the attribute in camelCase format.
     * @param string $strValue The value to assign to the attribute.
     *
     * @return void
     * @throws Caller
     */
	public function setDataAttribute(string $strName, string $strValue): void {
		$strName = 'data-' . Js\Helper::dataNameFromCamelCase($strName);
		$this->setHtmlAttribute($strName, $strValue);
	}

    /**
     * Retrieves a data attribute value based on the given attribute name.
     *
     * @param string $strName The name of the attribute in camel case format.
     * @return string|null The value of the data attribute, or null if it does not exist.
     * @throws Caller
     */
	public function getDataAttribute(string $strName): ?string {
		$strName = 'data-' . Js\Helper::dataNameFromCamelCase($strName);
		return $this->getHtmlAttribute($strName);
	}

    /**
     * Removes a data attribute from the HTML attributes array.
     *
     * @param string $strName The camelCase name of the data attribute to be removed.
     * @return void
     * @throws Caller
     */
	public function removeDataAttribute(string $strName): void {
		$strName = 'data-' . Js\Helper::dataNameFromCamelCase($strName);
		$this->removeHtmlAttribute($strName);
	}


    /**
     * Sets a CSS style property for the instance.
     *
     * @param string $strName The name of the CSS style property to set.
     * @param string|null $strValue The value to assign to the specified CSS style property.
     * @param bool|null $blnIsLength Optional. Indicates if the style value should be treated as a length property.
     * @return bool Returns true if the style was successfully set or modified, false otherwise.
     */
	public function setCssStyle(string $strName, ?string $strValue, ?bool $blnIsLength = false): bool {
        $ret = false;
        if (!is_null($strValue)) {
            if ($blnIsLength) {
                $oldValue = $this->styles[$strName] ?? '';
                if (Html::setLength($oldValue, $strValue)) {
                    $ret = true;
                    $this->markAsModified();
                    $this->styles[$strName] = $oldValue; // oldValue was updated
                }
            }
            elseif (!isset($this->styles[$strName]) || $this->styles[$strName] !== $strValue) {
                $this->styles[$strName] = $strValue;
                $this->markAsModified();
                $ret = true;
            }
        } else {
            if (isset($this->styles[$strName])) {
                unset($this->styles[$strName]);
                $this->markAsModified();
                $ret = true;
            }
        }
        return $ret;
	}

    /**
     * Removes a specific CSS style from the collection by setting its value to null.
     *
     * @param string $strName The name of the CSS style to be removed.
     * @return bool Returns true if the style was successfully removed, false otherwise.
     */
	public function removeCssStyle(string $strName): bool {
		return $this->setCssStyle($strName, null);
	}

    /**
     * Checks if a given CSS style exists within the style property.
     *
     * @param string $strName The name of the CSS style to check.
     * @return bool Returns true if the CSS style exists, otherwise false.
     */
	public function hasCssStyle(string $strName): bool {
		return isset($this->styles[$strName]);
	}

    /**
     * Retrieves the CSS style value for the given style name.
     *
     * @param string $strName The name of the CSS style to retrieve.
     * @return string|null The value of the CSS style if it exists, or null if it does not exist.
     */
	public function getCssStyle(string $strName): ?string {
        return $this->styles[$strName] ?? null;
	}

    /**
     * Sets the CSS box value by applying styles with a given prefix and corresponding values.
     *
     * @param string $strPrefix The CSS property prefix (e.g., 'margin', 'padding').
     * @param mixed $mixValue The value to be applied. It can be a string for a single value.
     *                        a numerically indexed array for top, right, bottom, and left,
     *                        or an associative array with keys 'top', 'right', 'bottom', and 'left'.
     * @return void
     */
	public function setCssBoxValue(string $strPrefix, mixed $mixValue): void {
		if (is_string($mixValue)) {
			// shortcut
			$this->setCssStyle($strPrefix, $mixValue);
		} elseif (is_array($mixValue)) {
			if (array_key_exists(0, $mixValue)) {
				// top right bottom left, numerically indexed
				if (isset($mixValue[0])) $this->setCssStyle($strPrefix. '-top', $mixValue[0], true);
				if (isset($mixValue[1])) $this->setCssStyle($strPrefix. '-right', $mixValue[1], true);
				if (isset($mixValue[2])) $this->setCssStyle($strPrefix. '-bottom', $mixValue[2], true);
				if (isset($mixValue[3])) $this->setCssStyle($strPrefix. '-left', $mixValue[3], true);
			} else {
				// assume key/value
				if (isset($mixValue['top'])) $this->setCssStyle($strPrefix. '-top', $mixValue['top'], true);
				if (isset($mixValue['right'])) $this->setCssStyle($strPrefix. '-right', $mixValue['right'], true);
				if (isset($mixValue['bottom'])) $this->setCssStyle($strPrefix. '-bottom', $mixValue['bottom'], true);
				if (isset($mixValue['left'])) $this->setCssStyle($strPrefix. '-left', $mixValue['left'], true);
			}
		}
	}


    /**
     * Sets the CSS class for an element. This method can add, remove, or replace
     * the CSS class based on the input string format.
     *
     * @param string $strNewClass The new CSS class string. Prefix with '+' to add,
     *                            '- ' to remove or provide a class name directly to replace.
     * @return bool True if the CSS class is successfully set, modified, or removed; otherwise, false.
     */
    public function setCssClass(string $strNewClass): bool {
        assert ($strNewClass[0] != "+" || $strNewClass[1] == " "); // If a plus sign, be sure to follow with a space. For consistency.
        if (str_starts_with($strNewClass, '+ ')) {
            $ret = $this->addCssClass(substr($strNewClass, 2));
        }
        elseif (str_starts_with($strNewClass, '- ')) {
            $this->removeCssClass(substr($strNewClass, 2)); // Does not specify a return value
            $ret = true; // We assume that something is always done
        }
        else {
            $ret = $this->setHtmlAttribute('class', $strNewClass);
        }
        return $ret;
    }

    /**
     * Adds a new CSS class to the element's existing class attribute.
     *
     * @param string $strNewClass The CSS class name to add.
     * @return bool Returns true if the CSS class was successfully added, false otherwise.
     */
	public function addCssClass(string $strNewClass): bool {
		$ret = false;
		if (!$strNewClass) return false;

		$strClasses = $this->getHtmlAttribute('class');
		if (is_null($strClasses)) {
			$strClasses= '';
		}
		if (Html::addClass($strClasses, $strNewClass)) {
			$this->setHtmlAttribute('class', $strClasses);
			$ret = true;
		}
		return $ret;
	}

    /**
     * Removes a specified CSS class from the HTML `class` attribute if it exists.
     *
     * @param string $strCssClass The CSS class to be removed.
     * @return bool
     */
    public function removeCssClass(string $strCssClass): bool {
        if (!$strCssClass) {
            return false;
        }
        $strClasses = $this->getHtmlAttribute('class');
        if ($strClasses && Html::removeClass($strClasses, $strCssClass)) {
            $this->setHtmlAttribute('class', $strClasses);
            return true; // Class removal successful
        }
        return false; // There was no class or nothing changed.
    }


    /**
     * Removes CSS classes from the current element's class attribute that start with the specified prefix.
     *
     * @param string $strPrefix The prefix of the CSS classes to be removed. If empty, no action will be taken.
     * @return void
     */
	public function removeCssClassesByPrefix(string $strPrefix): void {
		if (!$strPrefix) return;
		$strClasses = $this->getHtmlAttribute('class');
		if ($strClasses && Html::removeClassesByPrefix($strClasses, $strPrefix)) {
			$this->setHtmlAttribute('class', $strClasses);
		}
	}


    /**
     * Checks if a specific CSS class exists in the element's class attribute.
     *
     * @param string $strClass The CSS class name to check for.
     * @return bool Returns true if the class exists, otherwise false.
     */
	public function hasCssClass(string $strClass): bool {
		if (!isset($this->attributes['class'])) return false;
		$strClasses = explode (' ', $this->attributes['class']);
		return (in_array($strClass, $strClasses));
	}

    /**
     * Overrides the current attributes and styles with new ones provided.
     *
     * @param HtmlAttributeManagerBase $objNewStyles The new styles and attributes to merge with the existing ones.
     * @return void
     */
	protected function override(HtmlAttributeManagerBase $objNewStyles): void {
		$this->attributes = array_merge($this->attributes, $objNewStyles->attributes);
		$this->styles = array_merge($this->styles, $objNewStyles->styles);
	}

    /**
     * Marks the current instance or resource as modified.
     * This method is intended to signal that changes have been made that may require further processing.
     *
     * @return void
     */
	public function markAsModified()
    {}

    /**
     * Renders the HTML attributes from the provided attributes and style overrides.
     *
     * @param array|null $attributeOverrides An optional array of attribute overrides to apply.
     * @param array|null $styleOverrides An optional array of style overrides to apply.
     * @return string The rendered HTML attributes as a string.
     */
	public function renderHtmlAttributes(?array $attributeOverrides = null, ?array $styleOverrides = null): string {
		return Html::renderHtmlAttributes($this->getHtmlAttributes($attributeOverrides, $styleOverrides));
	}

    /**
     * Renders CSS styles as a string, optionally applying style overrides.
     *
     * @param array|null $styleOverrides An optional array of style overrides to merge with the existing styles.
     * @return string The rendered CSS styles as a string.
     */
	public function renderCssStyles(?array $styleOverrides = null): string {
		$styles = $this->styles;
		if ($styleOverrides) {
			$styles = array_merge($styles, $styleOverrides);
		}
		return Html::renderStyles($styles);
	}

    /**
     * Renders an HTML tag with specified attributes, styles, and content.
     *
     * @param string $strTag The name of the HTML tag to render.
     * @param array|null $attributeOverrides An optional array of attribute overrides for the tag.
     * @param array|null $styleOverrides An optional array of style overrides for the tag.
     * @param string|null $strInnerHtml Optional inner HTML content for the tag.
     * @param bool|null $blnIsVoidElement An optional flag to indicate if the tag is a void element (self-closing).
     * @param bool|null $blnNoSpace An optional flag to determine whether to omit spacing for the tag.
     *
     * @return string The rendered HTML tag as a string.
     */
	protected function renderTag(string $strTag, ?array $attributeOverrides = null, ?array $styleOverrides = null, ?string $strInnerHtml = null, ?bool $blnIsVoidElement = false, ?bool $blnNoSpace = false): string {
		$strAttributes = $this->renderHtmlAttributes($attributeOverrides, $styleOverrides);
		return Html::renderTag($strTag, $strAttributes, $strInnerHtml, $blnIsVoidElement, $blnNoSpace);
	}


    /**
     * Dynamically retrieves the value of a specified property or style.
     *
     * This method provides access to a variety of style properties, CSS attributes, and HTML attributes based
     * on the provided property name. If the requested property is not directly supported, an attempt is made
     * to retrieve it from the parent implementation.
     *
     * @param string $strName The name of the property or style to retrieve.
     * @return mixed The value of the specified property, style, or attribute. Returns null if the property is not set
     *               (for certain types) or a suitable type based on the requested property. Throw an exception if
     *               the property is deprecated or invalid.
     * @throws Caller If accessing a deprecated property or an invalid property not found in the parent.
     * @throws UndefinedProperty
     */
    public function __get(string $strName): mixed
    {
		switch ($strName) {
			// Styles
			case "BackColor": return $this->getCssStyle('background-color');
            case "BackgroundImageUrl":
                $strUrl = $this->getCssStyle('background-image');
                if ($strUrl) {
                    $pieces = explode('"', $strUrl);
                    if ($pieces && count($pieces) == 3) {
                        return $pieces[1];  // extract actual url from inside the url("...") block
                    }
                }
                return $strUrl;
			case "BorderColor": return $this->getCssStyle('border-color');
			case "BorderStyle": return $this->getCssStyle('border-style');
			case "BorderWidth": return $this->getCssStyle('border-width');
			case "BorderCollapse": return $this->getCssStyle('border-collapse');
			case "Display": return !($this->getCssStyle('display') == DisplayType::NONE);
			case "DisplayStyle": return $this->getCssStyle('display');
			case "FontBold": return $this->getCssStyle('font-weight') == 'bold';
			case "FontItalic": return $this->getCssStyle('font-style') == 'italic';
			case "FontNames": return $this->getCssStyle('font-family');
			case "FontOverline": return $this->getCssStyle('text-decoration') == 'overline';
			case "FontStrikeout": return $this->getCssStyle('text-decoration') == 'line-through';
			case "FontUnderline": return $this->getCssStyle('text-decoration') == 'underline';
			case "FontSize": return $this->getCssStyle('font-size');
			case "ForeColor": return $this->getCssStyle('color');
			case "Opacity": return $this->getCssStyle('opacity');
			case "Cursor": return $this->getCssStyle('cursor');
			case "Height": return $this->getCssStyle('height');
			case "Width": return $this->getCssStyle('width');
			case "Overflow": return $this->getCssStyle('overflow');
			case "Position": return $this->getCssStyle('position');
			case "Top": return $this->getCssStyle('top');
			case "Left": return $this->getCssStyle('left');
			case "TextAlign": return $this->getCssStyle('text-align');
			case "VerticalAlign": return $this->getCssStyle('vertical-align');
			case "Wrap": throw new Caller("Wrap is deprecated. Use NoWrap instead");
			case "NoWrap": return $this->getCssStyle('white-space') == 'nowrap';
			case "UnorderedListStyle": return $this->getCssStyle('list-style-type');

			// Attributes
			case "CssClass": return $this->getHtmlAttribute('class');
			case "AccessKey": return $this->getHtmlAttribute('accesskey');
			case "Enabled": return $this->getHtmlAttribute('disabled') == null;
			case "TabIndex": return $this->getHtmlAttribute('tabindex');
			case "ToolTip": return $this->getHtmlAttribute('title');
			case "ReadOnly": return $this->hasHtmlAttribute('readonly');
			case "AltText": return $this->hasHtmlAttribute('alt');
			case "OrderedListType": return $this->getHtmlAttribute('type');

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
     * Dynamically sets a property value for certain predefined style-related properties.
     * Updates corresponding CSS styles based on the given property name and value.
     * Some properties may require specific types or value ranges.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property. The expected type can vary depending on the property.
     *
     * @return void
     * @throws Caller If the value for "Opacity" is outside the range of 0 to 100.
     * @throws InvalidCast If the provided value cannot be properly cast to the required type.
     */
    public function __set(string $strName, mixed $mixValue): void {
        switch ($strName) {
            // Styles
			case "BackColor":
				try {
					$this->setCssStyle('background-color', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
            case "BackgroundImageUrl":
                try {
                    if ($mixValue) {
                        $this->setCssStyle('background-image', 'url("'  . Type::cast($mixValue, Type::STRING) . '")');
                    } else {
                        $this->setCssStyle('background-image', null);
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "BorderColor":
				try {
					$this->setCssStyle('border-color', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "BorderStyle":
				try {
					$this->setCssStyle('border-style', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "BorderWidth":
				try {
					$this->setCssStyle('border-width', Type::cast($mixValue, Type::STRING), true);
					if (!$this->hasCssStyle('border-style')) {
						$this->setCssStyle('border-style', 'solid');
					}
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "BorderCollapse":
				try {
					$this->setCssStyle('border-collapse', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case "Display":
			case "DisplayStyle":
				if (is_bool($mixValue)) {
					if ($mixValue) {
						$this->removeCssStyle('display'); // do the default
					}
					else {
						$this->setCssStyle('display', DisplayType::NONE);
					}
				} else {
					try {
						$this->setCssStyle('display', Type::cast($mixValue, Type::STRING));
						break;
					} catch (InvalidCast $objExc) {
						$objExc->incrementOffset();
						throw $objExc;
					}
				}
            break;

            case "FontBold":
				try {
					$this->setCssStyle('font-weight', Type::cast($mixValue, Type::BOOLEAN) ? 'bold' : null);
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case "FontItalic":
				try {
					$this->setCssStyle('font-style', Type::cast($mixValue, Type::BOOLEAN) ? 'italic' : null);
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case "FontNames":
				try {
					$this->setCssStyle('font-family', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case "FontOverline":
				try {
					$this->setCssStyle('text-decoration', Type::cast($mixValue, Type::BOOLEAN) ? 'overline' : null);
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "FontStrikeout":
				try {
					$this->setCssStyle('text-decoration', Type::cast($mixValue, Type::BOOLEAN) ? 'line-through' : null);
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "FontUnderline":
				try {
					$this->setCssStyle('text-decoration', Type::cast($mixValue, Type::BOOLEAN) ? 'underline' : null);
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "FontSize":
				try {
					$this->setCssStyle('font-size', Type::cast($mixValue, Type::STRING), true);
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case "ForeColor":
				try {
					$this->setCssStyle('color', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "Opacity":
				try {
					$mixValue = Type::cast($mixValue, Type::INTEGER);
					if (($mixValue < 0) || ($mixValue > 100)) {
						throw new Caller('Opacity must be an integer value between 0 and 100');
					}
					$this->setCssStyle('opacity', $mixValue);
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "Cursor":
				try {
					$this->setCssStyle('cursor', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case "Height":
				try {
					$this->setCssStyle('height', Type::cast($mixValue, Type::STRING), true);
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case "Width":
				try {
					$this->setCssStyle('width', Type::cast($mixValue, Type::STRING), true);
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case "Overflow":
				try {
					$this->setCssStyle('overflow', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "Position":
				try {
					$this->setCssStyle('position', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "Top":
				try {
					$this->setCssStyle('top', Type::cast($mixValue, Type::STRING), true);
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "Left":
				try {
					$this->setCssStyle('left', Type::cast($mixValue, Type::STRING), true);
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "TextAlign":
				try {
					$this->setCssStyle('text-align', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "VerticalAlign":
				try {
					$this->setCssStyle('vertical-align', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "Wrap": // Wrap is now an actual attribute. The Original developer used Wrap instead of NoWrap, not anticipating future change to HTML
				throw new Caller ("Wrap is deprecated. Use NoWrap instead");
                //break;

			case "NoWrap":
				try {
					$this->setCssStyle('white-space', Type::cast($mixValue, Type::BOOLEAN) ? 'nowrap' : null);
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case "Padding": // top right bottom left
				$this->setCssBoxValue('padding', $mixValue);
				break;

			case "Margin": // top right bottom left
				$this->setCssBoxValue('margin', $mixValue);
				break;

			case "UnorderedListStyle":
				try {
					$this->setCssStyle('list-style-type', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			// Attributes
			case "CssClass":
				try {
					$strCssClass = Type::cast($mixValue, Type::STRING);
					$this->setCssClass($strCssClass);
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "AccessKey":
				try {
					$this->setHtmlAttribute('accesskey', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "Enabled":
				try {
					$this->setHtmlAttribute('disabled',  Type::cast($mixValue, Type::BOOLEAN) ? null : 'disabled');
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

                /* Case "Required": Not supported consistently by browsers. We handle this inhouse */

			case "TabIndex":
				try {
					$this->setHtmlAttribute('tabindex', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
			case "ToolTip":
				try {
					$this->setHtmlAttribute('title', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case "Data":
				try {
					$dataArray = Type::cast($mixValue, Type::ARRAY_TYPE);
					foreach ($dataArray as $key=>$value) {
						$this->setDataAttribute($key, $value);
					}
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case "ReadOnly":
				try {
					$this->setHtmlAttribute('readonly',  Type::cast($mixValue, Type::BOOLEAN) ? 'readonly' : null);
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case "AltText":
				try {
					$this->setHtmlAttribute('alt', Type::cast($mixValue, Type::STRING));
					break;
				} catch (InvalidCast $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case "OrderedListType":
				try {
					$this->setHtmlAttribute('type', Type::cast($mixValue, Type::STRING));
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
		}
	}
}