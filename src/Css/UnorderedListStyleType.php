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
     * For specifying what to display in an unordered HTML list. Goes in the list-style-type style.
     *
     * @package QCubed\Css
     * @was QUnorderedListStyle
     */
    abstract class UnorderedListStyleType {
        const string Disc = 'disc';
        const string Circle = 'circle';
        const string Square = 'square';
        const string None = 'none';
    }