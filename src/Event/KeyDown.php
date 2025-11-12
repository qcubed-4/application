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
     * Class KeyDown
     *
     * When a keyboard key is pressed down (without having been released) while the control is in focus
     *
     * @package QCubed\Event
     */
    class KeyDown extends EventBase
    {
        /** Event Name */
        const string EVENT_NAME = 'keydown';
    }
