<?php
use QCubed\Action\Ajax;
use QCubed\Action\Terminate;
use QCubed\Control\Label;
use QCubed\Event\EnterKey;
use QCubed\Event\EscapeKey;
use QCubed\Event\KeyPress;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\TextBox;

require_once('../qcubed.inc.php');

	class ExampleForm extends FormBase {
		protected TextBox $txtItem;
		protected Label $lblSelected;

		protected function formCreate(): void
        {
			// Define the Controls
			$this->txtItem = new TextBox($this);
			$this->txtItem->Name = 'Random Data';

			$this->lblSelected = new Label($this);
			$this->lblSelected->Name = 'What You Entered';
			$this->lblSelected->Text = '<none>';

            // We want to update the label every time the user enters data into the text box.
            // To avoid too many simultaneous data entries, we add a half-second delay to the KeyPress event.
			$this->txtItem->addAction(new KeyPress(500), new Ajax('txtItem_KeyPress'));

			// Because this is just an example, we'll go ahead and terminate on Enter/ESC to prevent
			// any inadvertent form posts -- this can obviously be changed to a \QCubed\Action\Ajax to a separate
			// method/function, etc.
			$this->txtItem->addAction(new EnterKey(), new Terminate());
			$this->txtItem->addAction(new EscapeKey(), new Terminate());
		}

		protected function txtItem_KeyPress(): void
        {
			// Update the Label
			$this->lblSelected->Text = trim($this->txtItem->Text);
		}
	}

	ExampleForm::run('ExampleForm');

