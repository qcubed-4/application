<?php
use QCubed\Action\Ajax;
use QCubed\Action\StopPropagation;
use QCubed\Control\Panel;
use QCubed\Event\Click;
use QCubed\Project\Application;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

    class ExampleForm extends FormBase {
        protected $objPanel1;
        protected $objPanel2;
        protected $objPanel3;
        protected $objPanel4;

        protected function formCreate() {
            /*
            * These two panels here will demonstrate even bubbling
            */
            $this->objPanel1 = new Panel($this);
            $this->objPanel1->AutoRenderChildren = true;
            $this->objPanel1->CssClass = 'container';
            $this->objPanel1->Text = "I'm panel 1";
            $this->objPanel1->addAction(new Click(), new Ajax('objPanel1_Click'));

            $this->objPanel2 = new Panel($this->objPanel1);
            $this->objPanel2->CssClass = 'container';
            $this->objPanel2->addCssClass('insidePanel');
            $this->objPanel2->Text = "I'm panel 2 and I'm a child of panel 1.<br/><br/>If you click me, both my click action and panel 1's click action will fire";
            $this->objPanel2->addAction(new Click(), new Ajax('objPanel2_Click'));

            /*
            * These two panels here will demenstrate how to STOP even bubbling
            */
            $this->objPanel3 = new Panel($this);
            $this->objPanel3->CssClass = 'container';
            $this->objPanel3->AutoRenderChildren = true;
            $this->objPanel3->Text = "I'm panel 3";
            $this->objPanel3->addAction(new Click(), new Ajax('objPanel3_Click'));

            $this->objPanel4 = new Panel($this->objPanel3);
            $this->objPanel4->CssClass = 'container';
            $this->objPanel4->addCssClass('insidePanel');
            $this->objPanel4->Text = "I'm panel 4 and I'm a child of panel 3.<br/><br/>If you click me only my click action will fire thanks to StopPropagation";
            // Note the addition of \QCubed\Action\StopPropagation()
            $this->objPanel4->addAction(new Click(), new StopPropagation());
            $this->objPanel4->addAction(new Click(), new Ajax('objPanel4_Click'));

        }

        public function objPanel1_Click($strFormId, $strControlId, $strParameter) {
            Application::displayAlert('Panel 1 Clicked');
        }

        public function objPanel2_Click($strFormId, $strControlId, $strParameter) {
            Application::displayAlert('Panel 2 Clicked');
        }

        public function objPanel3_Click($strFormId, $strControlId, $strParameter) {
            Application::displayAlert('Panel 3 Clicked');
        }

        public function objPanel4_Click($strFormId, $strControlId, $strParameter) {
            Application::displayAlert('Panel 4 Clicked, panel 3 will not trigger a click');
        }

    }

    ExampleForm::run('ExampleForm');
