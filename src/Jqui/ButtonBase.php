<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Jqui;

use QCubed\Exception\Caller;


/**
 * Class ButtonBase
 *
 * Implements a JQuery UI Button
 *
 * The QJqButtonBase class defined here provides an interface between the generated
 * QJqButtonGen class, and QCubed. This file is part of the core and will be overwritten
 * when you update QCubed. To override, make your changes to the QJqButton.class.php file instead.
 *
 * Create a button exactly as if you were creating a QButton.
 *
 * @property boolean $ShowText Causes text to be shown when icons are also defined.
 *
 * One of the JqButtonGen properties use the same names as standard QCubed properties.
 * The Text property is a boolean in the JqUi object that specifies whether
 * to show text or just icons (provided icons are defined), and the Label property overrides
 * the standard HTML of the button. Because of the name conflict, the JQ UI property is called
 * ->JqText. You can also use ShowText as an alias to this as well so that your code is more readable.
 *  Text = standard html text of button
 *  Label = override of standard HTML text, if you want a button to say something different when JS is on or off
 *  ShowText = whether or not to hide the text of the button when icons are set
 *
 * @link http://jqueryui.com/button/
 * @was QJqButtonBase
 * @package QCubed\Jqui
 */
class ButtonBase extends ButtonGen
{
    public function __get($strName)
    {
        switch ($strName) {
            case 'ShowText':
                return $this->ShowLabel;    // from Gen superclass
            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case 'ShowText':    // true if the text should be shown when icons are defined
                $this->ShowLabel = $mixValue;
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }

}