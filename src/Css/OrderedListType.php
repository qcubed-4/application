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
        const string Numbers = '1';
        const string UppercaseLetters = 'A';
        const string LowercaseLetters = 'a';
        const string UppercaseRoman = 'I';
        const string LowercaseRoman = 'i';
    }
