<?php
    require_once('../qcubed.inc.php');

/**
 * Class InjectForm
 * This is aa test of javascript injection. It tests the ability to use ajax to insert a control into a form that also
 * depends on other javascript files.
 *
 * The autocomplete2 plugin in particular requires two separate javascript files to run correctly, so its a good test
 * of the mechanism in qcubed.js that uses jQuery deferred actions to load javascript files ahead of the actions.
 */
class InjectForm extends \QCubed\Project\Control\FormBase {
		protected $panel;
		protected $auto1;

		protected $btnServer;
		protected $btnAjax;

		protected function formCreate() {
			$this->panel = new \QCubed\Control\Panel($this);
			$this->panel->AutoRenderChildren = true;
			$this->panel->SetCssStyle('border', '2px solid black');
			$this->panel->Width = 200;
			$this->panel->Height = 100;

			$this->btnServer = new \QCubed\Project\Control\Button ($this);
			$this->btnServer->Text = 'Server Submit';
			$this->btnServer->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\Server('submit_click'));

			$this->btnAjax = new \QCubed\Project\Control\Button ($this);
			$this->btnAjax->Text = 'Ajax Submit';
			$this->btnAjax->AddAction(new \QCubed\Event\Click(), new \QCubed\Action\Ajax('submit_click'));
		}

		protected function submit_click($strFormId, $strControlId, $strParameter) {
			$this->insertAutoComplete();
		}

		protected function insertAutoComplete() {
			$this->auto1 = new QAutocomplete2($this->panel);
			$this->auto1->Name = 'Autocomplete';

			$a = [new \QCubed\Control\ListItem ('A', 1),
				new \QCubed\Control\ListItem ('B', 2),
				new \QCubed\Control\ListItem ('C', 3),
				new \QCubed\Control\ListItem ('D', 4)
			];

			$this->auto1->Source = $a;
		}
	}
	InjectForm::Run('InjectForm');
?>