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
     * Class BorderCollapse
     *
     * @package QCubed\Css
     * @was QBorderCollapse
     */
    abstract class BorderCollapseType
    {
        /** Not set */
        const string NOT_SET = 'NotSet';
        /** Borders are not collapsed */
        const string SEPARATE = 'Separate';
        /** Collapse the borders */
        const string COLLAPSE = 'Collapse';
    }
