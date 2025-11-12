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
     * Class EscapeKey
     *
     * When the escape key is pressed while the control is in
     * focus
     *
     * @package QCubed\Event
     */
    class EscapeKey extends KeyDown
    {
        /** @var string|null Condition JS */
        protected ?string $strCondition = 'event.keyCode == 27';
    }
