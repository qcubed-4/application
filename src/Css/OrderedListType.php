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
 * Class OrderedListType
 *
 * The type attribute of an ordered list.
 *
 * @package QCubed\Css
 * @was QOrderedListType
 */
abstract class OrderedListType {
    const Numbers = '1';
    const UppercaseLetters = 'A';
    const LowercaseLetters = 'a';
    const UppercaseRoman = 'I';
    const LowercaseRoman = 'i';
}
