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
     * Class Position
     *
     * @package QCubed\Css
     * @was QPosition
     */
    abstract class PositionType
    {
        /** Relative to the normal position */
        const string RELATIVE = 'relative';
        /** Relative to the first parent element that has a position other than static */
        const string ABSOLUTE = 'absolute';
        /** Relative to the browser Window */
        const string FIXED = 'fixed';
        /** Will result in 'static' positioning. Is default */
        const string NOT_SET = 'NotSet';
    }
