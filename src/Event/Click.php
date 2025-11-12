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
     * Represents a Click event in the application.
     *
     * This class inherits from EventBase and provides the constant EVENT_NAME
     * specific to identifying a click event.
     */
    class Click extends EventBase
    {
        const string EVENT_NAME = 'click';
    }

