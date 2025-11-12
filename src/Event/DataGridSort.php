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
     *
     * @package QCubed\Event
     */
    class DataGridSort extends EventBase {
        const string JS_RETURN_PARAM = 'ui'; // returns the col id
        const string EVENT_NAME = 'qdg2sort';
    }
