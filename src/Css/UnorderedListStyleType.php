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
 * Class UnorderedListStyleType
 *
 * For specifying what to dislay in an unordered html list. Goes in the list-style-type style.
 *
 * @package QCubed\Css
 * @was QUnorderedListStyle
 */
abstract class UnorderedListStyleType {
    const Disc = 'disc';
    const Circle = 'circle';
    const Square = 'square';
    const None = 'none';
}