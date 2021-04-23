<?php
use QCubed\Control\TextBoxBase;
use QCubed\Event\Click;
use QCubed\Jqui\Action\Bounce;
use QCubed\Jqui\Action\Hide;
use QCubed\Jqui\Action\HideEffect;
use QCubed\Jqui\Action\Highlight;
use QCubed\Jqui\Action\Pulsate;
use QCubed\Jqui\Action\Shake;
use QCubed\Jqui\Action\Show;
use QCubed\Jqui\Action\ShowEffect;
use QCubed\Jqui\Action\Size;
use QCubed\Jqui\Action\ToggleEffect;
use QCubed\Jqui\Action\Transfer;
use QCubed\Project\Control\FormBase;
use QCubed\Project\Control\TextBox;
use QCubed\Project\Jqui\Button;

require_once('../qcubed.inc.php');

class ExampleForm extends FormBase
{
    protected $txtTextbox;

    protected $btnToggle;
    protected $btnHide;
    protected $btnShow;
    protected $btnBounce;
    protected $btnHighlight;
    protected $btnShake;
    protected $btnPulsate;
    protected $btnSize;
    protected $btnTransfer;

    protected function formCreate()
    {
        $this->txtTextbox = new TextBox($this);
        $this->txtTextbox->TextMode = TextBoxBase::MULTI_LINE;
        $this->txtTextbox->Text = 'Click a button to start an animation.';
        $this->txtTextbox->Height = 200;

        $this->btnToggle = new Button($this);
        $this->btnToggle->Text = "toggle";

        $this->btnShow = new Button($this);
        $this->btnShow->Text = "show";

        $this->btnHide = new Button($this);
        $this->btnHide->Text = "hide";

        $this->btnBounce = new Button($this);
        $this->btnBounce->Text = "bounce";

        $this->btnHighlight = new Button($this);
        $this->btnHighlight->Text = "highlight";

        $this->btnShake = new Button($this);
        $this->btnShake->Text = "shake";

        $this->btnPulsate = new Button($this);
        $this->btnPulsate->Text = "pulsate";

        $this->btnSize = new Button($this);
        $this->btnSize->Text = "resize";

        $this->btnTransfer = new Button($this);
        $this->btnTransfer->Text = "transfer and hide";

        $this->btnToggle->addAction(new Click(), new ToggleEffect($this->txtTextbox, "scale", ""));
        $this->btnHide->addAction(new Click(), new HideEffect($this->txtTextbox, "blind"));
        $this->btnShow->addAction(new Click(), new ShowEffect($this->txtTextbox, "slide", "direction: 'up'"));
        $this->btnBounce->addAction(new Click(), new Bounce($this->txtTextbox, "", 300));
        $this->btnHighlight->addAction(new Click(), new Highlight($this->txtTextbox, "", 2000));
        $this->btnShake->addAction(new Click(), new Shake($this->txtTextbox, "", 300));
        $this->btnPulsate->addAction(new Click(), new Pulsate($this->txtTextbox, "times:2", 700));
        $this->btnSize->addAction(new Click(),
            new Size($this->txtTextbox, "to: {width: 100, height: 100}, scale: 'box'"));

        // 3 events, one after the other, for the Shake action.
        $this->btnTransfer->addAction(new Click(), new Show($this->txtTextbox, "fast"));
        $this->btnTransfer->addAction(new Click(), new Transfer($this->txtTextbox, $this->btnTransfer));
        $this->btnTransfer->addAction(new Click(), new Hide($this->txtTextbox, "fast"));
    }
}

ExampleForm::run('ExampleForm');
