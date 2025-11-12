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
     * Class Display
     *
     * @package QCubed\Css
     * @was QDisplayStyle
     */
    abstract class DisplayType
    {
        /** Hide the control */
        const string NONE = 'none';
        /** Treat as a block element */
        const string BLOCK = 'block';
        /** Treat as an inline element */
        const string INLINE = 'inline';
        /** Treat as an inline-block element */
        const string INLINE_BLOCK = 'inline-block';
        /** Display style not set. Browser will take care */
        const string NOT_SET = 'NotSet';
    }
