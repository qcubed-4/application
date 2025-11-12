<?php

use QCubed\Action\Ajax;
use QCubed\Action\JavaScript;
use QCubed\Action\Terminate;
use QCubed\Control\Label;
use QCubed\Event\Blur;
use QCubed\Event\Click;
use QCubed\Event\EnterKey;
use QCubed\Event\EscapeKey;
use QCubed\Project\Application;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\TextBox;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase {

    /** @var SelectableLabel[] */
    protected array $lblArray = [];
    /** @var TextBox[] */
    protected array $txtArray = [];

	protected function formCreate(): void
    {
		for ($intIndex = 0; $intIndex < 10; $intIndex++) {
			// Create the Label -- we must remember to explicitly specify the
			// Control ID so that we can code JavaScript against it
			// Note, we are using the regular \QCubed\Control\Label and not our custom SelectableLabel
			// because we will now store the "which label is selected" information on the
			// client/javascript side.
			$this->lblArray[$intIndex] = new Label($this, 'label' . $intIndex);
			$this->lblArray[$intIndex]->Text = 'This is a Test for Item #' . ($intIndex + 1);
			$this->lblArray[$intIndex]->CssClass = 'renamer_item';
			$this->lblArray[$intIndex]->ActionParameter = $intIndex;

			// Note that we now use a lblArray_Click function we write in JavaScript instead of
			// PHP to do the selection work.
			$this->lblArray[$intIndex]->AddAction(new Click(), new JavaScript('lblArray_Click(this)'));

			// Create the Textbox (hidden) -- we must remember to explicitly specify the
			// Control ID so that we can code JavaScript against it
			// Also, instead of making Visible false, we set Display to false.  This allows
			// the entire control to render as "display:none", so that we can code JavaScript
			// to make it appear and disappear (via a call to .toggleDisplay()).
			$this->txtArray[$intIndex] = new TextBox($this, 'textbox' . $intIndex);
			$this->txtArray[$intIndex]->ActionParameter = $intIndex;
			$this->txtArray[$intIndex]->Display = false;

            $this->txtArray[$intIndex]->BorderWidth = '1px';
            $this->txtArray[$intIndex]->BorderColor = 'gray';
            $this->txtArray[$intIndex]->BorderStyle= 'Solid';

			// Create Actions to Save Textbox on Blur or on "Enter" Key
			$this->txtArray[$intIndex]->AddAction(new Blur(), new Ajax('TextItem_Save'));
			$this->txtArray[$intIndex]->AddAction(new EnterKey(), new Ajax('TextItem_Save'));
			$this->txtArray[$intIndex]->AddAction(new EnterKey(), new Terminate());

			// Create Action to CANCEL/Revert Textbox on "Escape" Key
			$this->txtArray[$intIndex]->AddAction(new EscapeKey(), new Ajax('TextItem_Cancel'));
			$this->txtArray[$intIndex]->AddAction(new EscapeKey(), new Terminate());
		}
	}

	protected function TextItem_Save(string $strFormId, string $strControlId, string $strParameter): void
    {
		$strValue = $this->txtArray[$strParameter]->Text;

		if (!empty($strValue)) {
			// Copy the Textbox value back to the Label
			$this->lblArray[$strParameter]->Text = $strValue;
		}

		// Hide the Textbox, get the label cleaned up and ready to go
		$this->lblArray[$strParameter]->Display = true;
		$this->txtArray[$strParameter]->Display = false;
		$this->lblArray[$strParameter]->CssClass = 'renamer_item';

		Application::ExecuteJavaScript('intSelectedIndex = -1;');
	}

	protected function TextItem_Cancel(string $strFormId, string $strControlId, string $strParameter): void
    {
		// Hide the Textbox, get the label cleaned up and ready to go
		$this->lblArray[$strParameter]->Display = true;
		$this->txtArray[$strParameter]->Display = false;
		$this->lblArray[$strParameter]->CssClass = 'renamer_item';

		Application::ExecuteJavaScript('intSelectedIndex = -1;');
	}

}

ExampleForm::Run('ExampleForm');
