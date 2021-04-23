<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Cryptography;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Type;
use QCubed as Q;

/**
 * Class HList
 *
 * A control that lets you dynamically create an html unordered or ordered hierarchical list with
 * sub-lists. These structures are often used as the basis for javascript widgets like
 * menu bars.
 *
 * Also supports data binding. When using the data binder, it will recreate the item list each time it draws,
 * and then delete the item list so that the list does not get stored in the formstate. It is common for lists like
 * this to associate items in a database with items in a list through the value attribute of each item.
 * In an effort to make sure that database ids are not exposed to the client (for security reasons), the value
 * attribute is encrypted.
 *
 * @property string $Tag            Tag for main wrapping object
 * @property string $ItemTag        Tag for each item
 * @property bool $EncryptValues    Whether to encrypt the values that are printed in the html. Useful if the values
 *                                        are something you want to publicly hide, like database ids. True by default.
 * @was QHListControl
 * @package QCubed\Control
 */
class HList extends Q\Project\Control\ControlBase
{
    use ListItemManagerTrait, DataBinderTrait;

    /** @var string  top level tag */
    protected $strTag = 'ul';
    /** @var string  item tag */
    protected $strItemTag = 'li';
    /** @var null|ListItemStyle The common style for all elements in the list */
    protected $objItemStyle = null;
    /** @var null|Cryptography the temporary cryptography object for encrypting database values sent to the client */
    protected $objCrypt = null;
    /** @var bool Whether to encrypt values */
    protected $blnEncryptValues = false;

    /**
     * Adds an item to the list.
     *
     * @param HListItem|string $mixListItemOrName
     * @param null|string $strValue
     * @param null|string $strAnchor
     */
    public function addItem($mixListItemOrName, $strValue = null, $strAnchor = null)
    {
        if (gettype($mixListItemOrName) == Type::OBJECT) {
            $objListItem = Type::cast($mixListItemOrName, "\QCubed\Control\HListItem");
        } else {
            $objListItem = new HListItem($mixListItemOrName, $strValue, $strAnchor);
        }

        $this->addListItem($objListItem);
    }

    /**
     * Adds an array of items to the list. The array can also be an array of key>val pairs
     * @param array $objItemArray An array of HListItems or key=>val pairs to be sent to constructor.
     */
    public function addItems($objItemArray)
    {
        if (!$objItemArray) {
            return;
        }

        if (!is_object(reset($objItemArray))) {
            foreach ($objItemArray as $key => $val) {
                $this->addItem($key, $val);
            }
        } else {
            $this->addListItems($objItemArray);
        }
    }

    /**
     * This is not a typical input control, so there is no post data to read.
     */
    public function parsePostData()
    {
    }

    /**
     * Validate the submitted data
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * Return the id. Used by QListItemManager trait.
     * @return string
     */
    public function getId()
    {
        return $this->strControlId;
    }

    /**
     * Returns the HTML for the control and all subitems.
     *
     * @return string
     */
    public function getControlHtml()
    {
        $strHtml = '';
        if ($this->hasDataBinder()) {
            $this->callDataBinder();
        }
        if ($this->getItemCount()) {
            $strHtml = '';
            foreach ($this->getAllItems() as $objItem) {
                $strHtml .= $this->getItemHtml($objItem);
            }

            $strHtml = $this->renderTag($this->strTag, null, null, $strHtml);
        }
        if ($this->hasDataBinder()) {
            $this->removeAllItems();
        }

        return $strHtml;
    }

    /**
     * Return the html to draw an item.
     *
     * @param mixed $objItem
     * @return string
     */
    protected function getItemHtml($objItem)
    {
        $strHtml = $this->getItemText($objItem);
        $strHtml .= "\n";
        if ($objItem->getItemCount()) {
            $strSubHtml = '';
            foreach ($objItem->getAllItems() as $objSubItem) {
                $strSubHtml .= $this->getItemHtml($objSubItem);
            }
            $strTag = $objItem->Tag;
            if (!$strTag) {
                $strTag = $this->strTag;
            }
            $strHtml .= Q\Html::renderTag($strTag, $this->getSubTagAttributes($objItem), $strSubHtml);
        }
        $objStyler = $this->getItemStyler($objItem);
        $strHtml = Q\Html::renderTag($this->strItemTag, $objStyler->renderHtmlAttributes(), $strHtml);

        return $strHtml;
    }

    /**
     * Return the text html of the item.
     *
     * @param mixed $objItem
     * @return string
     */
    protected function getItemText($objItem)
    {
        $strHtml = Q\QString::htmlEntities($objItem->Text);

        if ($strAnchor = $objItem->Anchor) {
            $strHtml = Q\Html::renderTag('a', ['href' => $strAnchor], $strHtml, false, true);
        }
        return $strHtml;
    }

    /**
     * Return the item styler for the given item. Combines the generic item styles found in this class with
     * any specific item styles found in the item.
     *
     * @param mixed $objItem
     * @return ListItemStyle
     */
    protected function getItemStyler($objItem)
    {
        if ($this->objItemStyle) {
            $objStyler = clone $this->objItemStyle;
        } else {
            $objStyler = new ListItemStyle();
        }
        $objStyler->setHtmlAttribute('id', $objItem->Id);

        // since we are going to embed the value in the tag, we are going to encrypt it in case its a database record id.
        if ($objItem->Value) {
            if ($this->blnEncryptValues) {
                $strValue = $this->encryptValue($objItem->Value);
            } else {
                $strValue = $objItem->Value;
            }
            $objStyler->setDataAttribute('value', $strValue);
        }
        if ($objStyle = $objItem->ItemStyle) {
            $objStyler->override($objStyle);
        }
        return $objStyler;
    }

    /**
     * Return the encrypted value of the given object
     *
     * @param string $value
     * @return string
     */
    protected function encryptValue($value)
    {
        if (!$this->objCrypt) {
            $this->objCrypt = new Cryptography(null, true);
        }
        return $this->objCrypt->encrypt($value);
    }

    /**
     * Return the decrypted value of the given value string.
     *
     * @param $strEncryptedValue
     * @return string
     */
    public function decryptValue($strEncryptedValue)
    {
        if (!$this->objCrypt) {
            $this->objCrypt = new Cryptography(null, true);
        }
        return $this->objCrypt->decrypt($strEncryptedValue);
    }

    /**
     * Return the attributes for the sub tag that wraps the item tags
     * @param mixed $objItem
     * @return array|null|string
     */
    protected function getSubTagAttributes($objItem)
    {
        return $objItem->getSubTagStyler()->renderHtmlAttributes();
    }


    /////////////////////////
    // Public Properties: GET
    /////////////////////////
    /**
     * PHP magic function
     * @param string $strName
     *
     * @return mixed
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            // APPEARANCE
            case "Tag":
                return $this->strTag;
            case "ItemTag":
                return $this->strItemTag;
            case "EncryptValues":
                return $this->blnEncryptValues;
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
     * PHP magic method
     * @param string $strName
     * @param string $mixValue
     *
     * @return void
     * @throws Caller|InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            // APPEARANCE
            case "Tag":
                try {
                    $this->strTag = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "ItemTag":
                try {
                    $this->strItemTag = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "EncryptValues":
                try {
                    $this->blnEncryptValues = Type::cast($mixValue, Type::BOOLEAN);
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
}
