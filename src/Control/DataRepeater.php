<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Html;
use QCubed\Exception\InvalidCast;
use QCubed\Type;
use QCubed\Exception\Caller;
use Throwable;

/**
 * Class DataRepeater
 *
 * The DataRepeater is a generic HTML base object for creating an object that contains a list of items tied
 * to the database. To specify how to draw the items, you can either create a template file, override the
 * GetItemHtml method, override the GetItemInnerHtml and GetItemAttributes methods, or specify
 * corresponding callbacks for those methods.
 *
 * The callbacks below can be specified as either a string or an array. If a string, it should be the name of a
 * public method in the parent form. If an array, it should be a PHP callable array. If your callback is a method in
 * a form, do NOT pass the form object in to the array, but rather just pass the name of the method as a string.
 * (This is due to a problem PHP has with serializing recursive objects.) If its method in a control, pass an array
 * with the control and method name, i.e. [$objControl, 'RenderMethod']
 *
 * @package Controls
 *
 * @property-read 	integer $CurrentItemIndex	The zero-based index of the item being drawn.
 * @property 		string  $TagName			The tag name to be used as the main object
 * @property        string $ItemTagName        The tag name to be used for each item (if Template is not defined)
 * @property 		string 	$Template			A PHP template file that will be evaluated for each item. The template will have
 * 												$_ITEM as the item in the DataSource array, $_CONTROL as this control, and $_FORM as
 * 												the form object. If you provide a template, the callbacks will not be used.
 * @property-write 	callable $ItemHtmlCallback	A PHP callable which will be called to get the HTML for each item.
 * 												Parameters passed are the item from the DataSource array, and the index of the
 * 												item being drawn. The callback should return the entire HTML for the item. If
 * 												you provide this callback, the ItemAttributesCallback and ItemInnerHtmlCallback
 * 												will not be used.
 * @property-write 	callable $ItemAttributesCallback	A PHP callable which will be called to get the attributes for each item.
 * 												Use this with the ItemInnerHtmlCallback and the ItemTagName. The callback
 * 												will be passed to the item and the index of the item. It should return key/value
 * 												pairs which will be used as the attributes for the item's tag. Use only
 * 												if you are not using a Template or the ItemHtmlCallback.
 * @property-write 	callable $ItemInnerHtmlCallback	A PHP callable which will be called to get the inner HTML for each item.
 * 												Use this with the ItemAttributesCallback and the ItemTagName. The callback
 * 												will be passed to the item and the index of the item. It should return the complete
 * 												text to appear inside the open and close tags for the item.	 *
 * @package QCubed\Control
 */
class DataRepeater extends PaginatedControl
{
    ///////////////////////////
    // Private Member Variables
    ///////////////////////////

    // APPEARANCE
    /** @var string|null */
    protected ?string $strTemplate = null;
    /** @var integer|null */
    protected ?int $intCurrentItemIndex = null;

    /** @var string  */
    protected string $strTagName = 'div';
    /** @var string  */
    protected string $strItemTagName = 'div';

    /** @var  callable */
    protected mixed $itemHtmlCallback = null;
    /** @var  callable */
    protected mixed $itemAttributesCallback = null;
    /** @var  callable */
    protected mixed $itemInnerHtmlCallback = null;

    /** @var array DataSource, from which the items are picked and rendered */
    protected array $objDataSource = [];


    //////////
    // Methods
    //////////
    public function parsePostData(): void
    {
    }

    /**
     * Returns the HTML corresponding to a given item. You have many ways of rendering an item:
     * - Specify a template that will get evaluated for each item. See EvaluateTemplate for more info.
     * - Specify a HtmlCallback callable to be called for each item to get the HTML for the item.
     * - Override this routine.
     * - Specify the item's tag name, and then use the helper functions or callbacks to return just the
     * attributes and/or inner HTML of the object.
     *
     * @param mixed $objItem The item to be processed for generating HTML.
     * @return string The generated HTML for the given item.
     * @throws Caller If the item tag name is not specified before rendering the list.
     */
    protected function getItemHtml(mixed $objItem): string
    {
        if ($this->strTemplate) {
            return $this->evaluateTemplate($this->strTemplate);
        } elseif ($this->itemHtmlCallback) {
            return call_user_func($this->itemHtmlCallback, $objItem, $this->intCurrentItemIndex);
        }

        if (!$this->strItemTagName) {
            throw new Caller("You must specify an item tag name before rendering the list.");
        }

        return Html::renderTag($this->strItemTagName, $this->getItemAttributes($objItem), $this->getItemInnerHtml($objItem));
    }

    /**
     * Return the attributes that go in the item tag, as an array of key=>value pairs. Values will be escaped for you.
     * If you define AttributesCallback, it will be used to determine
     * the attributes.
     *
     * @param mixed $objItem The item for which attributes need to be retrieved.
     * @return mixed|null The attributes for the given item if the callback is defined, or null otherwise.
     */
    protected function getItemAttributes(mixed $objItem): mixed
    {
        if ($this->itemAttributesCallback) {
            return call_user_func($this->itemAttributesCallback, $objItem, $this->intCurrentItemIndex);
        }
        return null;
    }

    /**
     * Returns the HTML between the item tags. Uses __toString on the object by default. Will use the
     * InnerHtmlCallback if provided.
     *
     * @param mixed $objItem The item for which to generate the inner HTML. Typically, a database object or data item.
     * @return string The internal HTML generated for the item, which defaults to using the string representation of the item.     */
    protected function getItemInnerHtml(mixed $objItem): string
    {
        if ($this->itemInnerHtmlCallback) {
            return call_user_func($this->itemInnerHtmlCallback, $objItem, $this->intCurrentItemIndex);
        }
        return (string)$objItem;    // default to rendering a database object
    }

    /**
     * Generates the HTML output for the control by iterating through its data source and rendering each item.
     *
     * The method binds the data source to the control, processes each item to generate its corresponding HTML,
     * and wraps the output with the specified container tag.
     *
     * @return string The generated HTML for the control, including its container and all rendered items.
     * @throws Caller
     */
    protected function getControlHtml(): string
    {
        $this->dataBind();

        // Iterate through everything
        $this->intCurrentItemIndex = 0;
        $strEvaldItems = '';
        if ($this->objDataSource) {
            global $_CONTROL;
            global $_ITEM;

            $objCurrentControl = $_CONTROL;
            $_CONTROL = $this;

            foreach ($this->objDataSource as $objObject) {
                $_ITEM = $objObject;
                $strEvaldItems .= $this->getItemHtml($objObject);
                $this->intCurrentItemIndex++;
            }

            $_CONTROL = $objCurrentControl;
        }

        $strToReturn = $this->renderTag($this->strTagName,
            null,
            null,
            $strEvaldItems);

        $this->objDataSource = [];
        return $strToReturn;
    }

    /**
     * Fix up a possible embedded reference to the form.
     */
    public function sleep(): array
    {
        $this->itemHtmlCallback = ControlBase::sleepHelper($this->itemHtmlCallback);
        $this->itemAttributesCallback = ControlBase::sleepHelper($this->itemAttributesCallback);
        $this->itemInnerHtmlCallback = ControlBase::sleepHelper($this->itemInnerHtmlCallback);
        return parent::sleep();
    }

    /**
     * Restore serialized references.
     * @param FormBase $objForm
     */
    public function wakeup(FormBase $objForm): void
    {
        parent::wakeup($objForm);
        $this->itemHtmlCallback = ControlBase::wakeupHelper($objForm, $this->itemHtmlCallback);
        $this->itemAttributesCallback = ControlBase::wakeupHelper($objForm, $this->itemAttributesCallback);
        $this->itemInnerHtmlCallback = ControlBase::wakeupHelper($objForm, $this->itemInnerHtmlCallback);
    }

    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * Magic method to retrieve the value of a property.
     *
     * @param string $strName The name of the property to retrieve.
     * @return mixed The value of the requested property.
     * @throws Caller If the property is not defined or inaccessible.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            // APPEARANCE
            case "Template": return $this->strTemplate;
            case "CurrentItemIndex": return $this->intCurrentItemIndex;
            case "TagName": return $this->strTagName;
            case "ItemTagName": return $this->strItemTagName;

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
     * Magic method to set the value of a property. Custom behavior is defined for specific properties.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property. The type depends on the specific property being set.
     * @return void
     * @throws Caller Thrown if an invalid property is attempted to be set or if a valid property value is invalid.
     * @throws InvalidCast Thrown if a value cannot be cast to the expected data type for the property.
     * @throws Throwable
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            // APPEARANCE
            case "Template":
                try {
                    $this->blnModified = true;
                    if ($mixValue) {
                        if (file_exists($strPath = $this->getTemplatePath($mixValue))) {
                            $this->strTemplate = Type::cast($strPath, Type::STRING);
                        } else {
                            throw new Caller('Could not find a template file: ' . $mixValue);
                        }
                    } else {
                        $this->strTemplate = null;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "TagName":
                try {
                    $this->blnModified = true;
                    $this->strTagName = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ItemTagName':
                try {
                    $this->blnModified = true;
                    $this->strItemTagName = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case 'ItemHtmlCallback':
                try {
                    $this->blnModified = true;
                    $this->itemHtmlCallback = Type::cast($mixValue, Type::CALLABLE_TYPE);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case 'ItemAttributesCallback':    // callback should return an array of key/value items
                $this->blnModified = true;
                $this->itemAttributesCallback = Type::cast($mixValue, Type::CALLABLE_TYPE);
                break;

            case 'ItemInnerHtmlCallback':
                $this->blnModified = true;
                $this->itemInnerHtmlCallback = Type::cast($mixValue, Type::CALLABLE_TYPE);
                break;

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
}
