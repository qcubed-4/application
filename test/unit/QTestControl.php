<?php
/*
 * This is used by control tests. Must be here so it can be unserialized, since tests are dynamically loaded.
 */

/*
 * This is used by control tests. Must be here so it can be unserialized, since tests are dynamically loaded.
 */
class QTestControl extends \QCubed\Project\Control\ControlBase
{
	public $savedValue1 = 1;
	public $savedValue2 = 0;
	public $savedValue3 = 0;

	protected function getControlHtml() {
		return "";
	}

	public function parsePostData() {
		
	}

	public function validate() {
		return true;
	}
	
	public function getWrapperStyleAttributes($blnIsBlockElement=false) {
		return parent::getWrapperStyleAttributes($blnIsBlockElement);
	}
}