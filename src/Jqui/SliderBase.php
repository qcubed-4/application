<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Jqui;

use QCubed\Project\Application;
use QCubed\Exception\Caller;
use QCubed\Type;

/**
 * Class SliderBase
 *
 * The  SliderBase class defined here provides an interface between the generated
 * SliderGen class, and QCubed. This file is part of the core and will be overwritten
 * when you update QCubed. To override, make your changes to the Slider.php file in
 * the controls folder instead.
 *
 * A slider can have one or two handles to represent a range of things, similar to a scroll bar.
 *
 * Use the inherited properties to manipulate it. Call Value or Values to get the values.
 *
 * @link http://jqueryui.com/slider/
 * @was QSliderBase
 * @package QCubed\Jqui
 */
class SliderBase extends SliderGen
{

    /** Constants to use for setting Orientation */
    const VERTICAL = 'vertical';
    const HORIZONTAL = 'horizontal';

    protected function makeJqWidget()
    {
        parent::makeJqWidget();

        Application::executeJsFunction('qcubed.slider', $this->getJqControlId(), Application::PRIORITY_HIGH);
    }

    /**
     * Returns the state data to restore later.
     * @return mixed
     */
    protected function getState()
    {
        if ($this->mixRange === true) {
            return ['values' => $this->Values];
        } else {
            return ['value' => $this->Value];
        }
    }

    /**
     * Restore the state of the control.
     * @param mixed $state
     */
    protected function putState($state)
    {
        if (isset($state['values'])) {
            $this->Values = $state['values'];
        } elseif (isset($state['value'])) {
            $this->Value = $state['value'];
        }
    }


    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case '_Value':    // Internal Only. Used by JS above. Do Not Call.
                try {
                    $this->intValue = Type::cast($mixValue, Type::INTEGER);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case '_Values': // Internal Only. Used by JS above. Do Not Call.
                try {
                    $aValues = explode(',', $mixValue);
                    $aValues[0] = Type::cast($aValues[0],
                        Type::INTEGER); // important to make sure JS sends values as ints instead of strings
                    $aValues[1] = Type::cast($aValues[1],
                        Type::INTEGER); // important to make sure JS sends values as ints instead of strings
                    $this->arrValues = $aValues;
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
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
