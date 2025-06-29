<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Table;

use QCubed as Q;
use QCubed\Control\FormBase;
use QCubed\Control\Proxy;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use Exception;
use QCubed\Query\Node\NodeBase;
use QCubed\Query\Node\ReverseReference;
use QCubed\Type;


/**
 * Class LinkColumn
 *
 * A multipurpose link column. This column lets you specify a column whose purpose is to show an anchor tag
 * with text, attributes, and properties related to the row item. It can handle row items that are objects or arrays
 * and specify parameters or methods of objects, as well as offsets in arrays.
 *
 * You can specify the text of the link, the destination address, the HTML get variables, and the attributes
 * to the anchor tag in a variety of ways as follows:
 * - as a static string
 * - as a two-member array callable, with the row item passed to the callable
 * - as an object property or string of properties (i.e., $item->prop1->prop2) by starting the string with "->" and
 *   separating each property with a "->". If the property ends with, "()", then it will be a method call instead.
 *   The same can be accomplished by passing an array, with each item being a step in the property chain. This
 *   is provided the row item is an object.
 * - as an index into an array, or a multi-index array (i.e., $item['index1']['index2']) bypassing a string of the
 *   form "[index1][index2]...". You can also pass an array that contains the indexes into the array. This is provided
 *   the row item is an array.
 *
 * Other options:
 * - Specify null for $mixDestination, and no link will be created, just text. This is helpful for turning off the
 *   link mode without having to create a completely different kind of column.
 * - Specify a Proxy for $mixDestination to draw it as a proxy control. In this case, $blnAsButton can be
 *   used to draw the proxy as a button rather than a link.
 *
 * Examples:
 *
 *  Create a column to edit a person, with "Edit" in the header, the name of the person as the label of each link, and give each
 *   anchor a class of "link".
 *  $objColumn = new QHtmlTableLinkColumn ("Edit", "->Name", "person_edit.php", ["intId"=>"->Id"], ["class"=>"link"]);
 *
 *
 *  Create a similar column, but use a proxy instead, with the person id as the action parameter to the proxy and
 *   drawing the proxy as a button.
 *  $objProxy = new \QCubed\Control\Proxy($this);
 *  $objColumn = new \QCubed\Table\LinkColumn ("Edit", "Edit", $objProxy, "->Id", null, true);
 *
 *  Create a "zoom" column for a table that uses an array of arrays as its source. Pass the 'id' index from the item
 *   as the id to the destination link. Use the "title" index as the label for the link.
 *  $objColumn = new QHtmlTableLinkColumn ("Zoom", "[title]", "zoom.php", ["intId"=>"[id]"]);
 *
 *  Create a simple link column that just specifies a data attribute and uses event delegation attached to the table to trap a click on the link.
 *   Return the id of the item clicked to the action as the action parameter.
 *  $objTable = new QHtmlTable ($this);
 *  $objTable->createLinkColumn("", "->Name", "#", null, ["data-id"=>"->Id"]);
 *  $objTable->addAction(new QClickEvent(0, null, "a"), new QAjaxAction("myActionScript", null, null, '$j(this).data("id")'));
 *
 * @property bool $AsButton    Only used if this is drawing a Proxy. Will draw the proxy as a button.
 * @property-write null|string|array $Text The text to display as the label of the anchor, a callable callback to get the text,
 *   a string that represents a property chain or a multidimensional array, or an array that represents the same. Depends on
 *   what time of the row item is passed.
 * @property-write null|string|array|Proxy $Destination The text representing the destination of the anchor, a callable callback to get the destination,
 *   a string that represents a property chain or a multidimensional array, or an array that represents the same,
 *   or a Proxy. Depends on what time of the row item is passed.
 * @property-write null|string|array $GetVars An array of key=>value pairs to use as the GET variables in the link URL,
 *   or in the case of a Proxy, possibly a string to represent the action parameter. In either case, each item
 *   can be a property chain, an array index list, or a callable callback as specified above.
 * @property-write null|array $TagAttributes An array of key=>value pairs to use as additional attributes in the tag.
 *   For example, it could be used to add a class or an id to each tag.
 * @was QHtmlTableLinkColumn
 * @package QCubed\Table
 */
class LinkColumn extends DataColumn
{
    /** @var bool */
    protected bool $blnHtmlEntities = false;    // we are rendering a link so turn off entities

    /** @var  string|array */
    protected string|array $mixText;
    /** @var  string|array|Proxy|null */
    protected Proxy|string|array|null $mixDestination;
    /** @var  array|string|null */
    protected  mixed $getVars;
    /** @var  array|null */
    protected ?array $tagAttributes;
    /** @var bool */
    protected bool $blnAsButton = false;

    /**
     * QHtmlTableLinkColumn constructor.
     *
     * @param string $strName Column name to be displayed in the table header.
     * @param array|string|NodeBase|null $mixText The text to display as the label of the anchor, a callable callback to get the text,
     *   a string that represents a property chain or a multidimensional array, or an array that represents the same, or a NodeBase representing the property.
     *   Depends on what type of row item is passed.
     * @param array|string|Proxy|null $mixDestination The text representing the destination of the anchor, a callable callback to get the destination,
     *   a string that represents a property chain or a multidimensional array, or an array that represents the same,
     *   or a Q\Control\Proxy. Depends on what type of row item is passed.
     * @param array|string|null $getVars An array of key=>value pairs to use as the GET variables in the link URL,
     *   or in the case of a Proxy, possibly a string to represent the action parameter. In either case, each item
     *   can be a property chain, an array index list, a NodeBase, or a callable callback as specified above. If the destination is a
     *   Proxy, this would be what to use as the action parameter.
     * @param array|null $tagAttributes An array of key=>value pairs to use as additional attributes in the tag.
     *   For example, it could be used to add a class or an id to each tag.
     * @param bool $blnAsButton Only used if this is drawing a Proxy. Will draw the proxy as a button.
     */
    public function __construct(
        string                     $strName,
        array|string|NodeBase|null $mixText,
        array|string|Proxy|null    $mixDestination = null,
        mixed                       $getVars = null,
        ?array                      $tagAttributes = null,
        bool                       $blnAsButton = false
    ) {
        parent::__construct($strName);
        $this->Text = $mixText;
        $this->Destination = $mixDestination;
        $this->GetVars = $getVars;
        $this->TagAttributes = $tagAttributes;
        $this->blnAsButton = $blnAsButton;
    }

    /**
     * Utility function to pre-process a value specifier. This will take a property list chain or an array index
     * chain and split it into an array representing the parts.
     *
     * @param mixed $mixSpec
     * @return mixed
     */
    protected static function splitSpec(mixed $mixSpec): mixed
    {
        if (is_array($mixSpec)) {
            return $mixSpec; // already split
        } elseif (is_string($mixSpec)) {
            if (str_starts_with($mixSpec, '->')) {
                // It is an object property list ($item->param1->param2)
                return explode('->', substr($mixSpec, 2));
            } elseif ($mixSpec[0] == '[' && str_ends_with($mixSpec, ']')) {
                // It is a list of array dereferences
                return explode('][', $mixSpec, substr(1, strlen($mixSpec) - 2));
            } else {
                return $mixSpec;
            }
        } else {
            return $mixSpec;
        }
    }


    /**
     * Utility function to post-process a value specifier. Will walk through an object property chain or an array
     * index chain and return the final value.
     *
     * @param mixed $mixSpec
     * @param mixed $item
     * @return mixed
     * @throws Caller
     */
    protected static function getObjectValue(mixed $mixSpec, mixed $item): mixed
    {
        if (is_array($mixSpec)) {
            if (is_object($mixSpec[0]) && is_callable($mixSpec)) {
                // If it's a callable array, then call it
                return call_user_func($mixSpec, $item);
            } elseif (is_object($item)) {
                // It is an object property list ($item->param1->param2 or $item->method()->method2()). Can mix these too.
                $value = $item;
                foreach ($mixSpec as $part) {
                    // Evaluate as a function, or a param
                    if (str_ends_with($part, '()')) {
                        // call as a method
                        $value = $value->$part();
                    } else {
                        $value = $value->$part;
                    }
                }
                return $value;
            } elseif (is_array($item)) {
                $value = $item;
                foreach ($mixSpec as $part) {
                    $value = $value[$part];
                }
                return $value;
            } else {
                return $item; // We have no idea what this is, so return the item for possible further processing
            }
        } elseif ($mixSpec instanceof NodeBase) {
            $properties = array($mixSpec->_PropertyName);
            $objNode = $mixSpec;
            while ($objNode = $objNode->_ParentNode) {
                if (!($objNode instanceof NodeBase)) {
                    throw new Caller('NodeBase cannot go through any "To Many" association nodes.');
                }
                if (($objNode instanceof ReverseReference) && !$objNode->isUnique()) {
                    throw new Caller('NodeBase cannot go through any "To Many" association nodes.');
                }
                if ($strPropName = $objNode->_PropertyName) {
                    $properties[] = $strPropName;
                }
            }
            $properties = array_reverse($properties);
            $value = $item;
            foreach ($properties as $prop) {
                $value = $value->$prop;
            }
            if (is_object($value)) {
                return $value->__toString();
            } else {
                return $value;
            }
        }
        return $mixSpec; // In this case, we return a static value
    }

    /**
     * Returns the initial text that will be the label of the link. This text can be further processed by using
     * the inherited PostCallback function and similar properties.
     *
     * @param mixed $item
     * @return string
     * @throws Caller
     */
    public function fetchCellObject(mixed $item): mixed
    {
        return static::getObjectValue($this->mixText, $item);
    }

    /**
     * Returns the final string representing the content of the cell.
     *
     * @param mixed $item
     * @return string
     * @throws Caller
     */
    public function fetchCellValue(mixed $item): string
    {
        $strText = parent::fetchCellValue($item);    // allow post-processing of cell label

        $getVars = null;
        if ($this->getVars) {
            if (is_array($this->getVars)) {
                if (array_keys($this->getVars)[0] === 0) {
                    // Assume this is not an associative array. Likely we are here to extract a property list.
                    $getVars = static::getObjectValue($this->getVars, $item);
                } else {
                    // an associative array, so likely these are Get variables to be assigned individually
                    foreach ($this->getVars as $key => $val) {
                        $getVars[$key] = static::getObjectValue($val, $item);
                    }
                }
            } elseif ($this->getVars instanceof NodeBase) {
                $getVars = static::getObjectValue($this->getVars, $item);
            } else {
                $getVars = $this->getVars; // could be a simple action parameter.
            }
        }

        $tagAttributes = [];
        if ($this->tagAttributes && is_array($this->tagAttributes)) {
            foreach ($this->tagAttributes as $key => $val) {
                $tagAttributes[$key] = static::getObjectValue($val, $item);
            }
        }

        if ($this->mixDestination === null) {
            return Q\QString::htmlEntities($strText);
        } elseif ($this->mixDestination instanceof Proxy) {
            if ($this->blnAsButton) {
                return $this->mixDestination->renderAsButton($strText, $getVars, $tagAttributes);
            } else {
                return $this->mixDestination->renderAsLink($strText, $getVars, $tagAttributes);
            }
        } else {
            $strDestination = static::getObjectValue($this->mixDestination, $item);
            return Q\Html::renderLink(Q\Html::makeUrl($strDestination, $getVars), $strText, $tagAttributes);
        }
    }

    /**
     * Fix up possible embedded references to the form.
     */
    public function sleep(): void
    {
        $this->mixText = Q\Project\Control\ControlBase::sleepHelper($this->mixText);
        $this->mixDestination = Q\Project\Control\ControlBase::sleepHelper($this->mixDestination);
        $this->getVars = Q\Project\Control\ControlBase::sleepHelper($this->getVars);
        $this->tagAttributes = Q\Project\Control\ControlBase::sleepHelper($this->tagAttributes);
        parent::sleep();
    }

    /**
     * Restore embedded objects.
     *
     * @param FormBase $objForm
     */
    public function wakeup(FormBase $objForm): void
    {
        parent::wakeup($objForm);
        $this->mixText = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->mixText);
        $this->mixDestination = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->mixDestination);
        $this->getVars = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->getVars);
        $this->tagAttributes = Q\Project\Control\ControlBase::wakeupHelper($objForm, $this->tagAttributes);
    }


    /**
     * PHP magic method
     *
     * @param string $strName
     *
     * @return mixed
     * @throws Caller
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'AsButton':
                return $this->blnAsButton;
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
     * @return void
     * @throws Caller
     * @throws InvalidCast
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case "AsButton":
                try {
                    $this->blnAsButton = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "Text":
                $this->mixText = self::splitSpec($mixValue);
                break;

            case "Destination":
                if ($mixValue instanceof Proxy) {
                    $this->mixDestination = $mixValue;
                } else {
                    $this->mixDestination = self::splitSpec($mixValue);
                }
                break;

            case "GetVars":
                try {
                    if (is_null($mixValue)) {
                        $this->getVars = null;
                    } elseif (is_string($mixValue)) {
                        $this->getVars = self::splitSpec($mixValue); // a simple action parameter for a control proxy
                    } elseif (is_array($mixValue)) {
                        $this->getVars = [];
                        foreach ($mixValue as $key => $val) {
                            $this->getVars[$key] = self::splitSpec($val);
                        }
                    } elseif ($mixValue instanceof NodeBase) {
                        $this->getVars = $mixValue;
                    } else {
                        throw new Exception("Invalid type");
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "TagAttributes":
                try {
                    if (is_null($mixValue)) {
                        $this->tagAttributes = null;
                    } elseif (is_array($mixValue)) {
                        $this->tagAttributes = [];
                        foreach ($mixValue as $key => $val) {
                            $this->tagAttributes[$key] = self::splitSpec($val);
                        }
                    } else {
                        throw new Exception("Invalid type");
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

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
}
