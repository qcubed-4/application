<?php

use QCubed\Action\Ajax;
use QCubed\Action\Server;
use QCubed\Control\Label;
use QCubed\Control\Panel;
use QCubed\Event\Click;
use QCubed\Event\TimerExpired;
use QCubed\Project\Control\Button;
use QCubed\Project\Control\JsTimer;

include ('../qcubed.inc.php');

include ('./unit/QTestControl.php');

// Run these tests within the context of a form
class QTestForm extends \QCubed\Project\Control\FormBase
{
    public $ctlTest;
    public $btnRunTests;
    public $lblRunning;
    /** @var Panel */
    public $pnlOutput;

    protected function formCreate() {
        $_SESSION['HtmlReporterOutput'] = '';   // erase previous test results

        $this->ctlTest = new QTestControl($this);
        $this->pnlOutput = new Panel($this, 'outputPanel');
        $this->pnlOutput->Template = __DIR__ . "/unit/TestOutput.tpl.php";
        $this->btnRunTests = new Button($this);
        $this->btnRunTests->Text = "Run Tests";
        $this->btnRunTests->onClick(new Ajax('startTesting'));

        $this->lblRunning = new Label($this);
        $this->lblRunning->Text = "Running, please wait...";
        $this->lblRunning->Visible = false;
    }

    protected function startTesting() {
        $this->lblRunning->Visible = true;

        $t1 = new JsTimer($this, 50, false, true, 'timer1');
        $t1->AddAction(new TimerExpired(), new Ajax ('preTest'));
        $t2 = new JsTimer($this, 51, false, true, 'timer2');
        $t2->AddAction(new TimerExpired(0,null,null,true), new Ajax ('preTest2'));
        $t3 = new JsTimer($this, 52, false, true, 'timer3');
        $t3->AddAction(new TimerExpired(), new Ajax ('preTest3'));
        $t4 = new JsTimer($this, 600, false, true, 'timer4');
        $t4->AddAction(new TimerExpired(), new Server ('runTests'));
    }

    public function preTest() {
        $this->ctlTest->savedValue1 = 2;	// for test in QControlBaseTests
    }

    public function preTest2() {
        $this->longOperation(); // delay a bit
        $this->ctlTest->savedValue2 = $this->ctlTest->savedValue1;	// for test in QControlBaseTests
    }

    public function preTest3() {
        $this->ctlTest->savedValue3 = 1;	// This should NOT happen, since previous event should block it.
    }

    public function runTests() {
        $cliOptions = [ 'phpunit'];	// first entry is the command
        array_push($cliOptions, '-c', __DIR__ . '/phpunit-local.xml');	// the config file is here

        //require dirname(dirname(dirname(dirname(__FILE__)))) . '/autoload.php'; // Find PHPUnit_TextUI_Command

        $tester = new PHPUnit_TextUI_Command();

        $tester->run($cliOptions, false);

        $this->pnlOutput->refresh();
    }

    private function longOperation() {
        $a = [];
        for($i = 0; $i < 100000; $i++) {
            $a[] = rand(1,5000);
        }
        ksort($a);
    }

}

QTestForm::run('QTestForm', __DIR__ . "/unit/QTestForm.tpl.php");

