<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Event;

/**
 * Class DataGridSort
 * @package QCubed\Event
 * @was QDataGrid_SortEvent
 */
class DataGridSort extends EventBase {
    const JS_RETURN_PARAM = 'ui'; // returns the col id
    const EVENT_NAME = 'qdg2sort';
}
