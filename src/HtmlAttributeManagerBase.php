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
use QCubed\Js;

/**
 * Class QHtmlAttributeManagerBase
 *
 * A base class for objects that manage html attributes. Uses array functions and defines a couple of arrays to manage
 * the attributes. Values will be html escaped when printed.
 *
 * Includes:
 * - helper functions to manage the class, style, and data-* attributes specially.
 * - helper functions to manage 'name-*' classes that are found in css frameworks like
 *   Bootstrap and Foundation.
 * - helpers for __get and __set functions, partially for backwards compatibility and also to make the setting
 *   of some attributes and styles easier (so you don't have to remember how to set them).
 *
 * Usage: Use the helper functions to setup your styles, classes, data-* and other attributes, then call
 *	      renderHtmlAttributes() to render the attributes to insert them into a tag.
 *
 *
 * @property string $AccessKey allows you to specify what Alt-Letter combination will automatically focus that control on the form
 * @property string $BackColor sets the CSS background-color of the control
 * @property string $BackgroundImageUrl set the url for a background image
 * @property string $BorderColor sets the CSS border-color of the control
 * @property string $BorderWidth sets the CSS border-width of the control
 * @property string $BorderStyle is used to set CSS border-style by {@link QCubed\Css\BorderStyle}
 * @property string $BorderCollapse    defines the BorderCollapse css style for a table
 * @property string $CssClass sets or returns the CSS class for this control. When setting, if you precede the class name
 *  with a plus sign (+), it will add the class(es) to the currently existing classes, rather than replace them. Can add or
 *  set more than one class at once by separating names with a space.
 * @property string $Cursor is used to set CSS cursor property by {@link QCubed\Css\Cursor}
 * @property boolean $Display shows or hides the control using the CSS display property.  In either case, the control is
 *  still rendered on the page. See the Visible property if you wish to not render a control.
 * @property string $DisplayStyle is used to set CSS display property by {@link QCubed\Css\Display}
 * @property boolean $Enabled specifies whether or not this is enabled (it will grey out the control and make it
 *  inoperable if set to true)
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
 * @property string $Overflow is used to set CSS overflow property by {@link QCubed\Css\Overflow}
 * @property string $Position is used to set CSS position property by {@link QCubed\Css\Position}
 * @property integer $TabIndex specifies the index/tab order on a form
 * @property string $ToolTip specifies the text to be displayed when the mouse is hovering over the control
 * @property string $Top
 * @property string $Width
 * @property string $TextAlign sets the CSS text-align property
 * @property string $VerticalAlign sets the CSS vertical-align property
 * @property-write mixed $Padding sets the CSS padding property. Will accepts a string, which is passed verbatim, or
 *  an array, either numerically indexed, in which case it is in top, right, bottom, left order, or keyed with the
 *  names 'top', 'right', 'bottom', 'left'.
 * @property-write mixed $Margin sets the CSS margin property. Will accepts a string, which is passed verbatim, or
 *  an array, either numerically indexed, in which case it is in top, right, bottom, left order, or keyed with the
 *  names 'top', 'right', 'bottom', 'left'
 * @property-write array $Data a key/value array of data-* items to set. Keys can be in camelCase notation, in which case they will be
 *  converted to dashed notation. Use GetDataAttribute() to retrieve the value of a data attribute.
 * @property boolean $NoWrap sets the CSS white-space  property to nowrap
 * @property boolean $ReadOnly is the "readonly" html attribute (making a textbox "ReadOnly" is  similar to setting the textbox to Enabled
 *  Readonly textboxes are selectedable, and their values get posted. Disabled textboxes are not selectable and values do not post.
 * @property string $AltText text used for 'alt' attribute in images
 * @property string $OrderedListType type for ordered lists. Expects a QOrderedListType.
 * @property string $UnorderedListStyle style for unordered lists. Expects a QUnorderedListType.
 * @package QCubed
 * @was QHtmlAttributeManagerBase
 */

class HtmlAttributeManagerBase extends ObjectBase {
	/** @var  array attributes stored in PHP native format so they can be retrieved. Escaping happens when they are drawn. */
	protected $attributes = array();
	protected $styles = array();

	/**
	 * Sets the given html attribute to the given value.
	 * @param $strName
	 * @param $strValue
	 * @return boolean true if the attribute changed value
	 */
	public function setHtmlAttribute($strName, $strValue) {
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
	 * Removes the given html attribute.
	 *
	 * @param $strName
	 */
	public function removeHtmlAttribute($strName) {
		$this->setHtmlAttribute($strName, null);
	}

	/**
	 * Gets the Html Attribute, or return null if it does not exist.
	 *
	 * @param $strName
	 * @return null
	 */
	public function getHtmlAttribute($strName) {
		if (isset ($this->attributes[$strName])) {
			return $this->attributes[$strName];
		}
		else {
			return null;
		}
	}

	/**
	 * Returns an attribute array, with styles rendered.
	 * @param array|null $attributeOverrides
	 * @param array|null $styleOverrides
	 * @param array|null $selection an array of titles of attributes that you want the result to be limited to
	 * @return array
	 */
	public function getHtmlAttributes($attributeOverrides = null, $styleOverrides = null, $selection = null) {
		$attributes = $this->attributes;
		if ($attributeOverrides) {
			$attributes = array_merge ($attributes, $attributeOverrides);
		}
		$strStyles = $this->renderCssStyles($styleOverrides);
		if ($strStyles) {
			$attributes['style'] = $strStyles;
		}
		if ($selection) {
			$attributes = array_intersect_key($attributes, array_fill_keys($selection, 1));
		}
		return $attributes;
	}

	/**
	 * Returns true if the given attribute is set.
	 *
	 * @param string $strName
	 * @return bool
	 */
	public function hasHtmlAttribute($strName) {
		return (isset($this->attributes[$strName]));
	}

	/**
	 * Sets the given value as an html "data-*" attribute. The named value will be retrievable in jQuery by using
	 * '.data("$strName");'
	 *
	 * Note: Data name cases are handled specially in jQuery. Names are supposed to only be lower case. jQuery
	 * converts dashed notation to camelCase.
	 *
	 * For example, if your html looks like this:
	 *
	 * <div id='test1' data-test-case="my test"></div>
	 *
	 * You would get that value in jQuery by doing:
	 *
	 * $j('#test1').data('testCase');
	 *
	 * Conversion to special html data-* name formatting is handled here automatically. So if you SetDataAttribute('testCase') here,
	 * you can get it using .data('testCase') in jQuery
	 *
	 * @param $strName
	 * @param $strValue
	 */
	public function setDataAttribute($strName, $strValue) {
		$strName = 'data-' . Js\Helper::dataNameFromCamelCase($strName);
		$this->setHtmlAttribute($strName, $strValue);
	}

	/**
	 * Gets the data-* attribute value that was set previously in PHP.
	 *
	 * Does NOT call into javascript to return a value that was set on the browser side. You need to use another
	 * mechanism to retrieve that.
	 *
	 * @param $strName
	 * @return null
	 */
	public function getDataAttribute($strName) {
		$strName = 'data-' . Js\Helper::dataNameFromCamelCase($strName);
		return $this->getHtmlAttribute($strName);
	}

	/**
	 * Removes the given data attribute.
	 * @param $strName
	 */
	public function removeDataAttribute($strName) {
		$strName = 'data-' . Js\Helper::dataNameFromCamelCase($strName);
		$this->removeHtmlAttribute($strName);
	}



	/**
	 * Sets the Css named value to the given property.
	 *
	 * If blnIsLength is true, we assume the value is a unit length specifier, and so send it to the QHtml helper
	 * function, which has the ability to perform arithmetic operations on the old value.
	 *
	 * Will set the value of the parent classes blnModified value if something changes.
	 *
	 * @param string $strName
	 * @param string $strValue
	 * @param bool $blnIsLength true if this is a unit specifier e.g. 2px
	 * @return bool true if the style was actually changed (vs. it was already set that way)
	 */
	public function setCssStyle($strName, $strValue, $blnIsLength = false) {
		$ret = false;
		if (!is_null($strValue)) {
			if ($blnIsLength) {
				if (isset($this->styles[$strName])) {
					$oldValue = $this->styles[$strName];
				} else {
					$oldValue = '';
				}
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
	 * Removes the given CSS style property.
	 * @param $strName
	 * @return bool true if the style was removed, false if it wasn't there to begin with
	 */
	public function removeCssStyle($strName) {
		return $this->setCssStyle($strName, null);
	}

	/**
	 * Return true if the CSS style has been set.
	 * @param $strName
	 * @return bool
	 */
	public function hasCssStyle($strName) {
		return isset($this->styles[$strName]);
	}

	/**
	 * Retrieves the given CSS style property value.
	 * @param $strName
	 * @return string|null
	 */
	public function getCssStyle($strName) {
		if (isset ($this->styles[$strName])) {
			return $this->styles[$strName];
		}
		else {
			return null;
		}
	}

	/**
	 * Sets a box style, like margin or padding. Can accept:
	 *  - string, which will pass the string verbatim to the shortcut prefix (e.g. margin: 0px 1px)
	 *  - array, which can accept two styles:
	 * 		- [top, right, bottom, left], or
	 * 		- ['top'=>top, 'bottom'=>bottom, etc.]
	 *
	 * When passing an array, missing items or null items will be ignored. Also, you can use arithmetic operations
	 * to change the current value (provided you set a previous value).
	 *
	 * For example:
	 * $obj->setCssBoxStyle ('padding', ['right'=>3]);
	 * $obj->setCssBoxStyle ('padding', ['right'=>'*4']);
	 * Will set the padding-right to 12.
	 *
	 * @param string $strPrefix
	 * @param string|array $mixValue
	 */
	public function setCssBoxValue($strPrefix, $mixValue) {
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
	 * Sets the CSS class to the given value(s). Multiple class names can be separated with a space. If you
	 * preceed the class names with a plus(+) they will be added to the current class. If a minus (-) they
	 * will be removed.
	 *
     * @param string $strNewClass   The new class to set the control to.
     * @return bool
     */
	public function setCssClass($strNewClass) {
		assert ($strNewClass[0] != "+" || $strNewClass[1] == " "); // If a plus sign, be sure to follow with a space. For consistency.
		if (substr($strNewClass, 0, 2) == '+ ') {
			$ret = $this->addCssClass(substr($strNewClass, 2));
		}
		elseif (substr($strNewClass, 0, 2) == '- ') {
			$ret = $this->removeCssClass(substr($strNewClass, 2));
		}
		else {
			$ret = $this->setHtmlAttribute('class', $strNewClass);
		}
		return $ret;
	}

	/**
	 * Adds a css class name to the 'class' property. Prevents duplication.
	 * @param string $strNewClass
	 * @return bool true if the class was not already attached.
	 */
	public function addCssClass($strNewClass) {
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
	 * This will remove a css class name from the 'class' property (if it exists).
	 * @param string $strCssClass
	 */
	public function removeCssClass($strCssClass) {
		if (!$strCssClass) return;
		$strClasses = $this->getHtmlAttribute('class');
		if ($strClasses && Html::removeClass($strClasses, $strCssClass)) {
			$this->setHtmlAttribute('class', $strClasses);
		}
	}

	/**
	 * Many CSS frameworks use families of classes, which are built up from a base family name. For example,
	 * Bootstrap uses 'col-lg-6' to represent a column that is 6 units wide on large screens and Foundation
	 * uses 'large-6' to do the same thing. This utility removes classes that start with a particular prefix
	 * to remove whatever sizing class was specified.
	 *
	 * @param string $strPrefix
	 */
	public function removeCssClassesByPrefix($strPrefix) {
		if (!$strPrefix) return;
		$strClasses = $this->getHtmlAttribute('class');
		if ($strClasses && Html::removeClassesByPrefix($strClasses, $strPrefix)) {
			$this->setHtmlAttribute('class', $strClasses);
		}
	}


	/**
	 * Return true if the given class is in the attribute list.
	 *
	 * @param $strClass
	 * @return bool
	 */
	public function hasCssClass($strClass) {
		if (!isset($this->attributes['class'])) return false;
		$strClasses = explode (' ', $this->attributes['class']);
		return (in_array($strClass, $strClasses));
	}

	/**
	 * Permanently overrides the current styles with a new set of attributes and styles.
	 *
	 * @param HtmlAttributeManagerBase $objNewStyles
	 */
	protected function override(HtmlAttributeManagerBase $objNewStyles) {
		$this->attributes = array_merge($this->attributes, $objNewStyles->attributes);
		$this->styles = array_merge($this->styles, $objNewStyles->styles);
	}

	/**
	 * Mark the parent class as modified. The host class must implement this if this functionality is desired.
	 */
	public function markAsModified() {}

	/**
	 * Returns the html for the attributes. Allows the given arrays to override the attributes and styles before
	 * rendering.
	 * @param null|string 	$attributeOverrides
	 * @param null|string 	$styleOverrides
	 * @return string
	 */
	public function renderHtmlAttributes($attributeOverrides = null, $styleOverrides = null) {
		return Html::renderHtmlAttributes($this->getHtmlAttributes($attributeOverrides, $styleOverrides));
	}

	/**
	 * Returns the styles rendered as a css style string.
	 * @param null|string 	$styleOverrides
	 * @return string
	 */
	public function renderCssStyles($styleOverrides = null) {
		$styles = $this->styles;
		if ($styleOverrides) {
			$styles = array_merge($styles, $styleOverrides);
		}
		return Html::renderStyles($styles);
	}

	/**
	 * Helper function to render the current attributes and styles in a tag. Overrides will be merged in with
	 * current values before creating the output, but they will not affect the current values.
	 *
	 * @param $strTag
	 * @param null|array 		$attributeOverrides	key/value pairs of values for attribute overrides
	 * @param null|array 		$styleOverrides		key/value pairs of values for style overrides
	 * @param null|string 		$strInnerHtml		inner html to render. Will NOT be escaped.
	 * @param bool				$blnIsVoidElement	true if it should not have innerHtml or a closing tag.
     * @param bool              $blnNoSpace         some non-block elements render differently when there is a space between tags. This controls the space.
     * @return string			HTML out. Attributes will be escaped as needed, but innerHtml will be raw, so be careful.
	 */
	protected function renderTag($strTag, $attributeOverrides = null, $styleOverrides = null, $strInnerHtml = null, $blnIsVoidElement = false, $blnNoSpace = false) {
		$strAttributes = $this->renderHtmlAttributes($attributeOverrides, $styleOverrides);
		return Html::renderTag($strTag, $strAttributes, $strInnerHtml, $blnIsVoidElement, $blnNoSpace);
	}


	/**
	 * PHP magic method.
	 *
	 * The attributes include general attributes that span across most kinds of QControls. Attributes that are specific
	 * to a particular kind of tag might appear just in that control's definition.
	 *
	 * @param string $strName
	 * @return mixed
	 * @throws \Exception|Caller
	 */

	public function __get($strName) {
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
			case "Display": return !($this->getCssStyle('display') === DisplayType::NONE);
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
			case "Wrap": throw new Exception ("Wrap is deprecated. Use NoWrap instead");
			case "NoWrap": return $this->getCssStyle('white-space') == 'nowrap';
			case "UnorderedListStyle": return $this->getCssStyle('list-style-type');

			// Attributes
			case "CssClass": return $this->getHtmlAttribute('class');
			case "AccessKey": return $this->getHtmlAttribute('accesskey');
			case "Enabled": return $this->getHtmlAttribute('disabled') === null;
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
	 * PHP magic method
	 *
	 * @param string $strName
	 * @param string $mixValue
	 *
	 * @return mixed
	 * @throws \Exception|Caller|InvalidCast
	 */
	public function __set($strName, $mixValue) {
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
			case "Wrap": // Wrap is now an actual attribute. Original developer used Wrap instead of NoWrap, not anticipating future change to HTML
				throw new Caller ("Wrap is deprecated. Use NoWrap instead");
				break;

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

			/* case "Required": Not supported consistently by browsers. We handle this inhouse */

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
					return parent::__set($strName, $mixValue);
				} catch (Caller $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}
		}
		return true;
	}
}