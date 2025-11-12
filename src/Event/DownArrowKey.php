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
     * Class DownArrowKey
     *
     * @package QCubed\Event
     */
    class DownArrowKey extends KeyDown
    {
        /** @var string|null Condition JS */
        protected ?string $strCondition = 'event.keyCode == 40';
    }
