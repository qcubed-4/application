<?php
	/**
	 * This file contains the QTreeNavItem class.
	 *
	 * @package Controls
	 */

	/**
	 * @package Controls
	 *
	 * @property string $Name
	 * @property string $Value
	 * @property boolean $Selected
	 * @property boolean $Expanded
	 * @property mixed $ChildItemArray
	 * @property string $ItemId
	 * @property mixed $TreeNav
	 * @property string $ParentItemId
	 * @property boolean $HasChildren
	 */
	class QTreeNavItem extends \QCubed\AbstractBase {
		///////////////////////////
		// Private Member Variables
		///////////////////////////
		protected $strName = null;
		protected $strValue = null;
		protected $strItemId = null;
		protected $blnSelected = false;
		protected $blnExpanded = false;

		protected $objChildItemArray = array();
		protected $objTreeNav;
		protected $strParentItemId;
		protected $blnHasChildren = false;

		/////////////////////////
		// Methods
		/////////////////////////
		public function __construct($strName, $strValue, $blnExpanded, $objParentObject, $strItemId = null) {
			if (strpos($strItemId, '_') !== false)
				throw new \QCubed\Exception\Caller('Invalid Item Id: ' . $strItemId);

			$this->strName = $strName;
			$this->strValue = $strValue;
			$this->blnExpanded = $blnExpanded;
			$this->strItemId = $strItemId;

			// Setup the local TreeNav object
			if ($objParentObject instanceof QTreeNav)
				$this->objTreeNav = $objParentObject;
			else {
				$this->objTreeNav = $objParentObject->objTreeNav;
				$this->strParentItemId = $objParentObject->ItemId;
			}

			// Setup the Item Id (if applicable)
			if (!$this->strItemId)
				$this->strItemId = $this->objTreeNav->GenerateItemId();

			$objParentObject->AddChildItem($this);
			$this->objTreeNav->AddItem($this);
		}

		public function AddChildItem(QTreeNavItem $objItem) {
			array_push($this->objChildItemArray, $objItem);
		}

		/////////////////////////
		// Public Properties: GET
		/////////////////////////
		public function __get($strName) {
			switch ($strName) {
				case "Name": return $this->strName;
				case "Value": return $this->strValue;
				case "Selected": return $this->blnSelected;
				case "Expanded": return $this->blnExpanded;
				case "ChildItemArray": return (array) $this->objChildItemArray;
				case "ItemId": return $this->strItemId;
				case "TreeNav": return $this->objTreeNav;
				case "ParentItemId": return $this->strParentItemId;
				case "HasChildren": return $this->blnHasChildren;

				default:
					try {
						return parent::__get($strName);
					} catch (\QCubed\Exception\Caller $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}

		/////////////////////////
		// Public Properties: SET
		/////////////////////////
		public function __set($strName, $mixValue) {
			switch ($strName) {
				case "Name":
					try {
						$this->strName = \QCubed\Type::Cast($mixValue, \QCubed\Type::STRING);
						break;
					} catch (\QCubed\Exception\InvalidCast $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case "Value":
					try {
						$this->strValue = \QCubed\Type::Cast($mixValue, \QCubed\Type::STRING);
						break;
					} catch (\QCubed\Exception\InvalidCast $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case "Selected":
					try {
						$this->blnSelected = \QCubed\Type::Cast($mixValue, \QCubed\Type::BOOLEAN);
						break;
					} catch (\QCubed\Exception\InvalidCast $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case "Expanded":
					try {
						$this->blnExpanded = \QCubed\Type::Cast($mixValue, \QCubed\Type::BOOLEAN);
						break;
					} catch (\QCubed\Exception\InvalidCast $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
				case "HasChildren":
					try {
						$this->blnHasChildren = \QCubed\Type::Cast($mixValue, \QCubed\Type::BOOLEAN);
						break;
					} catch (\QCubed\Exception\InvalidCast $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			
				default:
					try {
						parent::__set($strName, $mixValue);
					} catch (\QCubed\Exception\Caller $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
					break;
			}
		}
	}