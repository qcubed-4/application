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
    const NONE = 'none';
    /** Treat as a block element */
    const BLOCK = 'block';
    /** Treat as an inline element */
    const INLINE = 'inline';
    /** Treat as an inline-block element */
    const INLINE_BLOCK = 'inline-block';
    /** Display style not set. Browser will take care */
    const NOT_SET = 'NotSet';
}
