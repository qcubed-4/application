<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Event;

    use QCubed\Exception\Caller;

    /**
     * Class CheckboxColumnClick
     *
     * Registers a click on a table checkbox column.
     *
     * @package QCubed\Event
     * @was QHtmlTableCheckBoxColumn_ClickEvent
     */
    class CheckboxColumnClick extends Click
    {
        const string JS_RETURN_PARAM = '{"row": $j(this).closest("tr")[0].rowIndex, "col": $j(this).closest("th,td")[0].cellIndex, "checked":this.checked, "id":this.id}'; // returns the array of cell info, and the new state of the checkbox

        /**
         * Constructs a new instance of the class.
         *
         * @param int $intDelay The delay in milliseconds before the action is triggered. Defaults to 0.
         * @param string|null $strCondition The condition to filter or validate. Defaults to null.
         * @return void
         * @throws Caller
         */
        public function __construct(int $intDelay = 0, ?string $strCondition = null)
        {
            parent::__construct($intDelay, $strCondition, 'input[type="checkbox"]');
        }
    }
