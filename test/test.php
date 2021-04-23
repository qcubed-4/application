#!/usr/bin/env php
<?php
/**
 * This file runs the travis unit tests.
 */

include (__DIR__ . "/travis-config.inc.php");

require_once(__DIR__ . '/unit/QTestControl.php');

use QCubed\Project\Control\FormBase as QForm;

class QTestForm extends QForm {
	public $ctlTest;

	protected function formCreate() {
		$this->ctlTest = new QTestControl($this);
		$this->runTests();
	}
	
	public function runTests() {
		$cliOptions = [ 'phpunit'];	// first entry is the command
		array_push($cliOptions, '-c', __DIR__ . '/phpunit.xml');	// the config file is here
//		array_push($cliOptions, '--bootstrap', __QCUBED_CORE__ . '/../vendor/autoload.php');

		$tester = new PHPUnit_TextUI_Command();

		$tester->run($cliOptions);
	}
}

QTestForm::run('QTestForm', __DIR__ . "/travis/QTestForm.tpl.php");
