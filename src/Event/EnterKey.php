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
     * Class EnterKey
     *
     * When an enter key is pressed while the control is in focus
     *
     * @package QCubed\Event
     */
    class EnterKey extends KeyDown
    {
        /** @var string|null Condition JS */
        protected ?string $strCondition = 'event.keyCode == 13';
    }
