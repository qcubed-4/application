<?php
namespace QCubed\Project\Jqui;

use QCubed as Q;

/**
 * Class Slider
 *
 * This is the Slider class that was automatically generated
 * by scraping the JQuery UI documentation website. It overrides the SliderBase
 *  class and provides you with a way of inserting custom functionality into the control. Feel free
 * to make changes to this file.
 *
 * @see SliderBase
 */
class Slider extends Q\Jqui\SliderBase
{
    /**
     * Returns the generator corresponding to this control.
     *
     * @return Q\Codegen\Generator\Slider
     */
    public static function getCodeGenerator(): Q\Codegen\Generator\Slider
    {
        return new Q\Codegen\Generator\Slider(__CLASS__);
    }

}
