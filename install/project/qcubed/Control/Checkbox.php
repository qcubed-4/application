<?php

namespace QCubed\Project\Control;

use QCubed as Q;
use QCubed\Control\CheckboxBase;

/**
 * Button class - You may modify it to contain your own modifications to the
 * Button throughout the framework.
 */
class Checkbox extends CheckboxBase
{
    ///////////////////////////
    // Button Preferences
    ///////////////////////////

    protected string $strCssClass = 'checkbox';

    /**
     * Returns the generator corresponding to this control.
     *
     * @return Q\Codegen\Generator\Checkbox
     */
    public static function getCodeGenerator(): Q\Codegen\Generator\Checkbox
    {
        return new Q\Codegen\Generator\Checkbox(__CLASS__);
    }

}
