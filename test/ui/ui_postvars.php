<?php
require_once('../qcubed.inc.php');
\QCubed\Project\Application::setEncodingType('ISO-8859-1');

/**
 * Class MyControl
 * A text box to test the setAdditionalPostVar function's abilities, including ability to pass a null value.
 */
class MyControl extends \QCubed\Project\Control\ControlBase
{
    public $txt;
    public $nullVal;

    public function getControlHtml()
    {
        return $this->renderTag('input');
    }
    public function parsePostData()
    {
        if (isset($_POST[$this->ControlId . '_extra'])) {
            $this->txt = $_POST[$this->ControlId . '_extra']['txt'];
            $this->nullVal = $_POST[$this->ControlId . '_extra']['nullVal'];
        }
    }

    public function validate()
    {
        return true;
    }

    public function getEndScript()
    {
        $strId = $this->ControlId;

        $strJs = parent::getEndScript();
        $strJs .= ';';
        $strJs .= "\$j('#{$strId}').change(function(event) {
			qcubed.setAdditionalPostVar('{$strId}_extra', {txt: \$j(this).val(), 'nullVal': null});
			qcubed.recordControlModification('{$strId}', 'Name', \$j(this).val());
			})";
        return $strJs;
    }
}

class ParamsForm extends \QCubed\Project\Control\FormBase
{
    protected $txtText;
    protected $txt2;
    protected $pnlTest;
    protected $lstCheckables;

    protected $btnSubmit;
    protected $btnAjax;

    protected function formCreate()
    {
        $this->txtText = new MyControl($this);
        $this->txtText->Name = "Special Vals";

        $this->txt2 = new \QCubed\Project\Control\TextBox($this);
        $this->txt2->Name = "Regular Val";

        $this->pnlTest = new \QCubed\Control\Panel($this);
        //$this->pnlTest->HtmlEntities = true;
        $this->pnlTest->Name = 'Result';

        $this->lstCheckables = new \QCubed\Control\CheckboxList($this);
        $this->lstCheckables->addItem('é - accented', 'é');
        $this->lstCheckables->addItem('ü - umlat', 'ü');
        $this->lstCheckables->addItem('î - circuflexed', 'î');
        $this->lstCheckables->addItem('ß - Eszett', 'ß');

        $strId = $this->txtText->ControlId;
        $strJs = "{txt: \$j('#{$strId}').val(), nullVal:null}";

        $this->btnSubmit = new \QCubed\Project\Control\Button($this);
        $this->btnSubmit->Text = "Server Submit";
        $this->btnSubmit->addAction(new \QCubed\Event\Click(), new \QCubed\Action\Server('submit_click', null, $strJs));

        $this->btnAjax = new \QCubed\Project\Control\Button($this);
        $this->btnAjax->Text = "Ajax Submit";
        $this->btnAjax->addAction(new \QCubed\Event\Click(), new \QCubed\Action\Ajax('submit_click', null, null, $strJs));
    }

    protected function submit_click($strFormId, $strControlId, $mixParam)
    {
        // test setAdditionalPostParam
        $strResult = $this->txtText->txt;
        $strResult .= ($this->txtText->nullVal === null ? ' and is null' : ' and is not null');

        // test parameters
        $strResult .= "\n" . var_export($mixParam, true);

        // test checkables
        $checkables = $this->lstCheckables->SelectedValues;
        $strResult .= "\n" . \QCubed\QString::htmlEntities(var_export($checkables, true));
        $checkables = $this->lstCheckables->SelectedNames;
        $strResult .= "\n" . \QCubed\QString::htmlEntities(var_export($checkables, true));
        $strResult .= "\n" . 'Ordinals: ' . ord($this->txtText->Name) . ',' . ord($strResult);
        $strResult .= "\n" . 'Regular: ' . $this->txt2->Text;
        
        $this->pnlTest->Text = $strResult;
    }
}
ParamsForm::run('ParamsForm');
