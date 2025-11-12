<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\ModelConnector;

    use QCubed as Q;

    /**
     * Class UnorderedListStyleType
     *
     * For specifying what to display in an unordered HTML list. Goes in the list-style-type style.
     *
     * @package QCubed\Css
     */
    abstract class ControlType
    {
        /** Large binary object or large text data */
        const string BLOB = Q\Database\FieldType::BLOB;
        /** Character sequence - variable length */
        const string TEXT = Q\Database\FieldType::VAR_CHAR;
        /** Character sequence - fixed length */
        const string CHAR = Q\Database\FieldType::CHAR;
        /** Integers */
        const string INTEGER = Q\Database\FieldType::INTEGER;
        /** Date and Time together */
        const string DATE_TIME = Q\Database\FieldType::DATE_TIME;
        /** Date only */
        const string DATE = Q\Database\FieldType::DATE;
        /** Time only */
        const string TIME = Q\Database\FieldType::TIME;
        /** Float, Double and real (postgresql) */
        const string FLOAT = Q\Database\FieldType::FLOAT;
        /** Boolean */
        const string BOOLEAN = Q\Database\FieldType::BIT;
        /** Select one item from a list of items. A foreign key or a unique reverse relationship. */
        const string SINGLE_SELECT = 'single';
        /** Select multiple items from a list of items. A non-unique reverse relationship or association table. */
        const string MULTI_SELECT = 'multi';
        /** Display a representation of an entire database table. Click actions would typically be done on this list. */
        const string TABLE = 'table';
    }
