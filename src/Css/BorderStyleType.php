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
        const string NOT_SET = 'NotSet';
        /** No border at all */
        const string NONE = 'none';
        /** Border made of dots */
        const string DOTTED = 'dotted';
        /** BOrder made dashes */
        const string DASHED = 'dashed';
        /** Solid line border */
        const string SOLID = 'solid';
        /** Double lined border */
        const string DOUBLE = 'double';
        /** A 3D groove border */
        const string GROOVE = 'groove';
        /** A 3D ridged border */
        const string RIDGE = 'ridge';
        /** A 3D inset border */
        const string INSET = 'inset';
        /** A 3D outset border */
        const string OUTSET = 'outset';
    }
