<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Css;

/**
 * Class BorderStyle
 *
 * @package QCubed\Css
 * @was QBorderStyle
 */
abstract class BorderStyleType
{
    /** No set border */
    const NOT_SET = 'NotSet';
    /** No border at all */
    const NONE = 'none';
    /** Border made of dots */
    const DOTTED = 'dotted';
    /** BOrder made ofdashes */
    const DASHED = 'dashed';
    /** Solid line border */
    const SOLID = 'solid';
    /** Double lined border */
    const DOUBLE = 'double';
    /** A 3D groove border */
    const GROOVE = 'groove';
    /** A 3D ridged border */
    const RIDGE = 'ridge';
    /** A 3D inset border */
    const INSET = 'inset';
    /** A 3D outset border */
    const OUTSET = 'outset';
}
