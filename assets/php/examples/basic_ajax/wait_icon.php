<?php

    use QCubed\Action\ActionParams;
    use QCubed\Action\Ajax;
    use QCubed\Control\Label;
    use QCubed\Control\WaitIcon;
    use QCubed\Event\Click;
    use QCubed\Exception\Caller;
    use QCubed\Project\Control\Button;
    use QCubed\Project\Control\FormBase;

    require_once('../qcubed.inc.php');

    /**
     * Class ExamplesForm
     *
     * Represents a form with interactive components, including a label and buttons,
     * demonstrating basic event handling and AJAX functionality with optional wait icons.
     */
    class ExamplesForm extends FormBase
    {
        // Local declarations of our Controls
        protected Label $lblMessage;
        protected Button $btnButton;
        protected Button $btnButton2;

        /**
         * Initializes the form components, including labels, buttons, wait icons, and event handlers.
         *
         * @return void
         * @throws Caller
         */
        protected function formCreate(): void
        {
            // Define the Label
            $this->lblMessage = new Label($this);
            $this->lblMessage->Text = 'Click the button to change my message.';

            // Define two Buttons
            $this->btnButton = new Button($this);
            $this->btnButton->Text = 'Click Me!';
            $this->btnButton2 = new Button($this);
            $this->btnButton2->Text = '(No Spinner)';

            // Define these Wait Icons -- we need to remember to "RENDER" this wait icon, too!
            $this->objDefaultWaitIcon  = new WaitIcon($this);
            $this->objDefaultWaitIcon->SpinnerType = 'default';
            $this->objDefaultWaitIcon->Width = '2em';
            $this->objDefaultWaitIcon->Height = '2em';

//            $this->objDefaultWaitIcon = new WaitIcon($this);
//            $this->objDefaultWaitIcon->SpinnerType = 'classic';
//            $this->objDefaultWaitIcon->Width = '2em';
//            $this->objDefaultWaitIcon->Height = '2em';

//            $this->objDefaultWaitIcon = new WaitIcon($this);
//            $this->objDefaultWaitIcon->SpinnerType = 'ripple';

            // Add a Click event handler to the button -- the action to run is an AjaxAction.
            $this->btnButton->addAction(new Click(), new Ajax('btnButton_Click'));

            // Add a second click event handler which will use NO spinner
            $this->btnButton2->addAction(new Click(), new Ajax('btnButton_Click', null));
        }

        /**
         * Handles the click event of the button, toggling the message displayed in the label.
         * Introduces a simulated delay to demonstrate a wait spinner.
         *
         * @param ActionParams $params
         *
         * @return void
         */
        protected function btnButton_Click(ActionParams $params): void
        {
            $strMessage = 'Hello, world!';
            // Let's add artificial latency/wait to show the spinner
            sleep(1);
            if ($this->lblMessage->Text == $strMessage) {
                $this->lblMessage->Text = 'Click the button to change my message.';
            } else {
                $this->lblMessage->Text = $strMessage;
            }
        }
    }

    // Run the Form we have defined
    ExamplesForm::run('ExamplesForm');
