<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Control;

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\ObjectBase;
    use QCubed\Type;

    /**
     * Class ListItemBase
     *
     * This base class represents an item in some kind of HTML item list. There are many types of possible lists, including
     * checklists and hierarchical lists. This is the core functionality common to all of them.
     *
     * @package Controls
     * @property string $Name      Usually what gets displayed. Can be overridden by the Label attribute in certain situations.
     * @property string $Value     Is any text that represents the value of an item (e.g., maybe a DB ID)?
     * @property-read boolean $Empty     true when both $Name and $Value are null, in which case this item will be rendered with an empty value in the list control
     * @property ListItemStyle $ItemStyle Custom HTML attributes for this particular item.
     * @property string $Text      synonym of Name. Used to store longer text with the item.
     * @property string $Id    A place to save an ID for the item. It is up to the corresponding list class to use this in the object.
     * @package QCubed\Control
     */
    class ListItemBase extends ObjectBase
    {
        ///////////////////////////
        // Private Member Variables
        ///////////////////////////
        /** @var null|string Name of the Item */
        protected ?string $strName = null;
        /** @var null|string Value of the Item */
        protected ?string $strValue = null;
        /** @var null|ListItemStyle Custom attributes of the list item */
        protected ?ListItemStyle $objItemStyle = null;
        /** @var  string|null the internal ID */
        protected ?string $strId = null;

        /////////////////////////
        // Methods
        /////////////////////////

        /**
         * @param string $strName
         * @param null|string $strValue
         * @param null|ListItemStyle $objItemStyle
         * @return void
         */
        public function __construct(string $strName, ?string $strValue = null, ?ListItemStyle $objItemStyle = null)
        {
            $this->strName = $strName;
            $this->strValue = $strValue;
            $this->objItemStyle = $objItemStyle;
        }

        /**
         * Returns the list item styler
         *
         * @return null|ListItemStyle
         */
        public function getStyle(): ?ListItemStyle
        {
            if (!$this->objItemStyle) {
                $this->objItemStyle = new ListItemStyle();
            }
            return $this->objItemStyle;
        }

        /**
         * Marks the current object or entity as modified to indicate that it has been changed.
         *
         * @return void
         */
        public function markAsModified(): void
        {
        }

        /**
         * Rebuilds or updates the index to reflect the current state of data.
         *
         * @return void
         */
        public function reindex(): void
        {
        }

        /**
         * @param string $strId
         * @return null|ListItemBase
         */
        public function findItem(string $strId): ?ListItemBase
        {
            return null;
        }

        /**
         * Return the ID. Used by a trait.
         * @return string|null
         */
        public function getId(): ?string
        {
            return $this->strId;
        }

        /**
         * @param mixed $strId
         * @return void
         */
        public function setId(mixed $strId): void
        {
            $this->strId = $strId;
        }

        /////////////////////////
        // Public Properties: GET
        /////////////////////////

        /**
         * @param string $strName The name of the property to retrieve.
         * @return mixed The value of the requested property, which can be of various types depending on the property.
         * @throws Caller If the property does not exist or cannot be accessed.
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case "Text":
                case "Name":
                    return $this->strName;
                case "Value":
                    return $this->strValue;
                case "ItemStyle":
                    return $this->objItemStyle;
                case "Empty":
                    return $this->strValue == null && $this->strName == null;
                case "Id":
                    return $this->strId;

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
         * Sets the value of a property on the object.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the property.
         * @return void
         * @throws InvalidCast If the value cannot be cast to the expected type.
         * @throws Caller If the property does not exist or is not accessible.
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case "Text":
                case "Name":
                    try {
                        $this->strName = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "Value":
                    try {
                        $this->strValue = Type::cast($mixValue, Type::STRING);
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "ItemStyle":
                    try {
                        $this->objItemStyle = Type::cast($mixValue, "\\QCubed\\Control\\ListItemStyle");
                        break;
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                case "Id":
                    try {
                        $this->strId = Type::cast($mixValue, Type::STRING);
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
