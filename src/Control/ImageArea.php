<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Type;

/**
 * Class ImageArea
 *
 * An AREA tag that is to be used specfically as a child control of an Image control. Creates an image map for
 * the parent image to detect specific areas of an image. You can attach actions and events to this control like any
 * other QCubed Control.
 *
 * @property string $Shape a shape type. Use ImageArea::SHAPE_RECT, SHAPE_CIRCLE, or SHAPE_POLY
 * @property int[] $Coordinates is the url of the image to be used
 * @package QCubed\Control
 */
class ImageArea extends \QCubed\Project\Control\ControlBase
{
    const SHAPE_RECT = "rect";
    const SHAPE_CIRCLE = "circle";
    const SHAPE_POLY = "poly";

    /** @var  string */
    protected $strShape;
    /** @var  int[] */
    protected $coordinates;

    protected function getControlHtml()
    {
        $this->blnUseWrapper = false;   // make sure we do not use a wrapper to draw!
        if (!$this->strShape) {
            throw new \Exception("Shape is required for ImageArea controls.");
        }
        if (!$this->coordinates) {
            throw new \Exception("Coordinates are required for ImageArea controls.");
        }

        $attributes = ["shape" => $this->strShape, "coords" => implode(",", $this->coordinates)];
        $strToReturn = $this->renderTag('area', $attributes, null, null, true);
        return $strToReturn;
    }

    public function validate()
    {
        return true;
    }

    public function parsePostData()
    {
    }

    /**
     * @param string $strName
     * @return mixed|null
     * @throws Caller
     */
    public function __get($strName)
    {
        switch ($strName) {
            // APPEARANCE
            case "Shape":
                return $this->strShape;
            case "Coordinates":
                return $this->coordinates;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * @param $strName
     * @param $mixValue
     * @throws Caller
     * @throws InvalidCast
     * @return void
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            // APPEARANCE
            case "Shape":
                try {
                    $this->blnModified = true;
                    $this->strShape = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Coordinates":
                try {
                    $this->blnModified = true;
                    $this->coordinates = Type::cast($mixValue, Type::ARRAY_TYPE);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

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
