<?php
require_once('../qcubed.inc.php');

use QCubed as Q;

class UrlForm extends \QCubed\Project\Control\FormBase {
	/** @var  \QCubed\Project\Control\Table */
	protected $dtg;
	protected $lblVars;

	protected function formCreate() {
		$this->dtg = new \QCubed\Project\Control\Table($this);
		$this->dtg->SetDataBinder('BindData');

		$col = $this->dtg->CreateCallableColumn('Link', [$this, 'dtg_LinkRender']);
		$col->HtmlEntities = false;
		$col = $this->dtg->CreateCallableColumn('Button', [$this, 'dtg_ButtonRender']);
		$col->HtmlEntities = false;

		$this->lblVars = new \QCubed\Control\Label ($this);
	}

	public function dtg_LinkRender ($item) {
		return (Q\Html::RenderTag('a', ['href'=>$item], urldecode($item)));
	}

	public function dtg_ButtonRender ($item) {
		$strControl = new \QCubed\Project\Control\Button ($this);
		$strControl->Text = 'Button';
		$strControl->ActionParameter = $item;
		$strControl->AddAction (new \QCubed\Event\Click(), new \QCubed\Action\Server('btn_click'));

		return $strControl->Render(false);
	}

	protected function btn_click($strFormId, $strControlId, $strParameter) {
		\QCubed\Project\Application::Redirect($strParameter);
	}

	public function BindData() {
		$a = [
			Q\Html::MakeUrl(\QCubed\Project\Application::instance()->context()->scriptName(), null, 'anchor'),
			Q\Html::MakeUrl(\QCubed\Project\Application::instance()->context()->scriptName(), ['a'=>1, 'b'=>'this & that', 'c'=>'/forward\back']),
			Q\Html::MakeUrl(\QCubed\Project\Application::instance()->context()->scriptName(), ['a'=>1, 'b'=>'this & that', 'c'=>'/forward\back'], null, \QCubed\Html::HTTP, $_SERVER['HTTP_HOST']),
			Q\Html::MailToUrl('test', 'qcu.be', ['subject'=>'Hey you.']),
			Q\Html::MailToUrl('test', 'qcu.be', ['subject'=>'What & About \ this /']),
			Q\Html::MailToUrl('"very.(),:;<>[]\".VERY.\"very@\\ \"very\".unusual"', 'strange.email.com', ['subject'=>'What & About \ this /']),

		];

		$this->dtg->DataSource = $a;

		$this->lblVars->Text = var_dump ($_GET);
	}

}

UrlForm::Run('UrlForm');
