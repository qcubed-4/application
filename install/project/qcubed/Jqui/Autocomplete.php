<?php
namespace QCubed\Project\Jqui;

use QCubed as Q;

/**
 * Class Autocomplete
 *
 * This is the Autocomplete class that was automatically generated
 * by scraping the JQuery UI documentation website. It overrides the AutocompleteBase
 *  class and provides you with a way of inserting custom functionality into the control. Feel free
 * to make changes to this file.
 *
 * @see AutocompleteBase
 */
class Autocomplete extends Q\Jqui\AutocompleteBase
{
    /**
     * Returns the generator corresponding to this control.
     *
     * @return Q\Codegen\Generator\Autocomplete
     */
    public static function getCodeGenerator(): Q\Codegen\Generator\Autocomplete
    {
        return new Q\Codegen\Generator\Autocomplete(__CLASS__);
    }

}
