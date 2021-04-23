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
    const NOT_SET = 'NotSet';
    /** Borders are not collapsed */
    const SEPARATE = 'Separate';
    /** Collapse the borders */
    const COLLAPSE = 'Collapse';
}
