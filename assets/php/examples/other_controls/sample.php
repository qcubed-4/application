<?php
use QCubed\Action\Alert;
use QCubed\Project\Control\FormBase;

require_once('../qcubed.inc.php');

require_once('SampleControl.php');

class ExampleForm extends FormBase
{
    protected $ctlCustom;

    protected function formCreate()
    {
        // Get the Custom Control
        $this->ctlCustom = new SampleControl($this);

        // Note that custom controls can act just like regular controls,
        // complete with events and attributes
        $this->ctlCustom->Foo = 'Click on me!';
        $this->ctlCustom->onClick(new Alert('Hello, world!'));
    }
}

// And now run our defined form
ExampleForm::run('ExampleForm');

