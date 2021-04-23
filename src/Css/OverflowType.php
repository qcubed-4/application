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
 * Class Overflow
 *
 * @package QCubed\Css
 * @was QOverflow
 */
abstract class OverflowType
{
    /** Not set */
    const NOT_SET = 'NotSet';
    /** Decided by browser */
    const AUTO = 'auto';
    /** Hide the content flowing outside boundary of the HTML element */
    const HIDDEN = 'hidden';
    /** The overflow is clipped, but a scroll-bar is added to see the rest of the content */
    const SCROLL = 'scroll';
    /** The overflow is not clipped. It renders outside the element's box. This is default */
    const VISIBLE = 'visible';
}
