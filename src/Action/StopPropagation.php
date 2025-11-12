<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Action;

    use QCubed\Control\ControlBase;

    /**
     * Class StopPropagation
     *
     * Prevents the event from bubbling up the DOM tree, preventing any parent
     * handlers from being notified of the event.
     *
     * @package QCubed\Action
     */
    class StopPropagation extends ActionBase
    {
        /**
         * Generates and returns a JavaScript snippet to stop the event's propagation.
         *
         * @param ControlBase $objControl The control instance triggering the script rendering.
         * @return string The JavaScript code to stop the event's propagation.
         */
        public function renderScript(ControlBase $objControl): string
        {
            return 'event.stopPropagation();';
        }
    }
