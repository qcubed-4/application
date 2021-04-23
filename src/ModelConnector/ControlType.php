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
 * For specifying what to dislay in an unordered html list. Goes in the list-style-type style.
 *
 * @package QCubed\Css
 * @was QControlCategoryType
 */
abstract class ControlType
{
    /** Large binary object or large text data */
    const BLOB = Q\Database\FieldType::BLOB;
    /** Character sequence - variable length */
    const TEXT = Q\Database\FieldType::VAR_CHAR;
    /** Character sequence - fixed length */
    const CHAR = Q\Database\FieldType::CHAR;
    /** Integers */
    const INTEGER = Q\Database\FieldType::INTEGER;
    /** Date and Time together */
    const DATE_TIME = Q\Database\FieldType::DATE_TIME;
    /** Date only */
    const DATE = Q\Database\FieldType::DATE;
    /** Time only */
    const TIME = Q\Database\FieldType::TIME;
    /** Float, Double and real (postgresql) */
    const FLOAT = Q\Database\FieldType::FLOAT;
    /** Boolean */
    const BOOLEAN = Q\Database\FieldType::BIT;
    /** Select one item from a list of items. A foreign key or a unique reverse relationship. */
    const SINGLE_SELECT = 'single';
    /** Select multiple items from a list of items. A non-unique reverse relationship or association table. */
    const MULTI_SELECT = 'multi';
    /** Display a representation of an entire database table. Click actions would typically be done on this list. */
    const TABLE = 'table';
}
