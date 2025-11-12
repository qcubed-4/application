<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Control;

    use Exception;
    use QCubed\Cryptography;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Html;
    use QCubed\QString;
    use QCubed\Type;
    use QCubed\Project\Control\ControlBase;

    /**
     * Class HList
     *
     * A control that lets you dynamically create an HTML unordered or ordered hierarchical list with
     * sub-lists. These structures are often used as the basis for JavaScript widgets like
     * menu bars.
     *
     * Also supports data binding. When using the data binder, it will recreate the item list each time it draws
     * and then delete the item list so that the list does not get stored in the formstate. It is common for lists like
     * this to associate items in a database with items in a list through the value attribute of each item.
     * In an effort to make sure that database IDs are not exposed to the client (for security reasons), the value
     * attribute is encrypted.
     *
     * @property string $Tag            Tag for the main wrapping object
     * @property string $ItemTag        Tag for each item
     * @property bool $EncryptValues    Whether to encrypt the values that are printed in the HTML. Useful if the values
     *                                        it is something you want to publicly hide, like database IDs. True by default.
     * @package QCubed\Control
     */
    class HList extends ControlBase
    {
        use ListItemManagerTrait, DataBinderTrait;

        /** @var string  top level tag */
        protected string $strTag = 'ul';
        /** @var string  item tag */
        protected string $strItemTag = 'li';
        /** @var null|ListItemStyle The common style for all elements in the list */
        protected ?ListItemStyle $objItemStyle = null;
        /** @var null|\QCubed\Cryptography the temporary cryptography object for encrypting database values sent to the client */
        protected ?Cryptography $objCrypt = null;
        /** @var bool Whether to encrypt value */
        protected ?bool $blnEncryptValues = false;

        /**
         * Adds an item to the list.
         *
         * @param HListItem|string $mixListItemOrName
         * @param string|null $strValue
         * @param string|null $strAnchor
         *
         * @throws Caller
         */
        public function addItem(HListItem|string $mixListItemOrName, ?string $strValue = null, ?string $strAnchor = null): void
        {
            if ($mixListItemOrName instanceof HListItem) {
                $objListItem = $mixListItemOrName;
            } else {
                $objListItem = new HListItem($mixListItemOrName, $strValue, $strAnchor);
            }

            $this->addListItem($objListItem);
        }

        /**
         * Adds an array of items to the list. The array can also be an array of key>val pairs
         * @param array $objItemArray An array of HListItems or key=>val pairs to be sent to constructor.
         * @throws Caller
         * @throws InvalidCast
         */
        public function addItems(array $objItemArray): void
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
         * This is not a typical input control, so there is no post-data to read.
         */
        public function parsePostData(): void
        {
        }

        /**
         * Validate the submitted data
         * @return bool
         */
        public function validate(): bool
        {
            return true;
        }

        /**
         * Return the id. Used by QListItemManager trait.
         * @return string|null
         */
        public function getId(): ?string
        {
            return $this->strControlId;
        }

        /**
         * Returns the HTML for the control and all subitems.
         *
         * @return string
         * @throws Caller
         */
        public function getControlHtml(): string
        {
            $strHtml = '';
            if ($this->hasDataBinder()) {
                $this->callDataBinder();
            }
            if ($this->getItemCount()) {
                //$strHtml = '';
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
         * Return the HTML to draw an item.
         *
         * @param mixed $objItem
         *
         * @return string
         * @throws Caller
         * @throws \Exception
         */
        protected function getItemHtml(mixed $objItem): string
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
                $strHtml .= Html::renderTag($strTag, $this->getSubTagAttributes($objItem), $strSubHtml);
            }
            $objStyler = $this->getItemStyler($objItem);
            /** @var mixed $strHtml */
            $strHtml = Html::renderTag($this->strItemTag, $objStyler->renderHtmlAttributes(), $strHtml);

            return $strHtml;
        }

        /**
         * Return the text HTML of the item.
         *
         * @param mixed $objItem
         * @return string
         */
        protected function getItemText(mixed $objItem): string
        {
            $strHtml = QString::htmlEntities($objItem->Text);

            if ($strAnchor = $objItem->Anchor) {
                $strHtml = Html::renderTag('a', ['href' => $strAnchor], $strHtml, false, true);
            }
            return $strHtml;
        }

        /**
         * Return the item styler for the given item. Combines the generic item styles found in this class with
         * any specific item styles found in the item.
         *
         * @param mixed $objItem
         *
         * @return ListItemStyle|null
         * @throws \Exception
         */
        protected function getItemStyler(mixed $objItem): ?ListItemStyle
        {
            if ($this->objItemStyle) {
                $objStyler = clone $this->objItemStyle;
            } else {
                $objStyler = new ListItemStyle();
            }
            $objStyler->setHtmlAttribute('id', $objItem->Id);

            // Since we are going to embed the value in the tag, we are going to encrypt it in case it is a database record id.
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
         * @throws Exception
         */
        protected function encryptValue(string $value): string
        {
            if (!$this->objCrypt) {
                $this->objCrypt = new Cryptography(null, true);
            }
            return $this->objCrypt->encrypt($value);
        }

        /**
         * Decrypts an encrypted value and returns the original string.
         *
         * @param string $strEncryptedValue The encrypted value to decrypt.
         *
         * @return string The decrypted original string.
         * @throws \QCubed\Exception\Cryptography
         */
        public function decryptValue(string $strEncryptedValue): string
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
        protected function getSubTagAttributes(mixed $objItem): array|string|null
        {
            return $objItem->getSubTagStyler()->renderHtmlAttributes();
        }


        /////////////////////////
        // Public Properties: GET
        /////////////////////////

        /**
         * PHP magic function
         *
         * @param string $strName
         *
         * @return mixed
         * @throws Caller
         * @throws \Exception
         */
        public function __get(string $strName): mixed
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
         *
         * @param string $strName
         * @param mixed $mixValue
         *
         * @return void
         * @throws Caller|InvalidCast
         * @throws \Exception
         */
        public function __set(string $strName, mixed $mixValue): void
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
