<?php
    namespace QCubed\Project\Control;

    use QCubed\Control\DialogInterface;
    use QCubed\Jqui\DialogBase;

    /**
     * Class Dialog
     *
     * Implements a Dialog.
     *
     * Dialogs are somewhat of a special control in the framework, in that the framework calls on dialogs and expects them
     * to do certain things. The dialog must implement the dialog interface, but the actual display of the dialog might
     * be dependent on what CSS or JavaScript framework.
     *
     * Here is where you choose what you want all the dialogs in your application to look like by extending this Dialog
     * class from the base class in your framework. The default uses JQuery UI's dialog, but there is a bootstrap plugin
     * available that implements dialogs through its Modal class. Other implementations may be developed in the future.
     *
     * There are a couple of ways to use the dialog. The simplest is as follows:
     *
     * From anywhere in your code:
     * <code>
     * $dlg = new Dialog($this, 'my-dialog');
     * $dlg->Text = 'Show this on the dialog.'
     * $dlg->AddButton ('OK', 'ok');
     * $dlg->AddAction (new DialogButton(), new Ajax('buttonClicked));
     * </code>
     *
     * @package QCubed\Project\Control
     */
    class Dialog extends DialogBase implements DialogInterface
    {
    }

    // If using the bootstrap library:
    //class Dialog extends \QCubed\Bootstrap\Modal implements \QCubed\Control\DialogInterface

