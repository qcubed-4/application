<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed\Jqui\Event;

    /**
     * Class AccordionActivate
     *
     * The abstract AccordionActivate class defined here is
     * code-generated. The code to generate this file is
     * in the /tools/jquery_ui_gen/jq_control_gen.php file,
     * and you can regenerate the files if you need to.
     *
     * The comments in this file are taken from the api reference site, so they do
     * not always make sense with regard to QCubed. They are simply provided
     * as reference.
     * Triggered after a panel has been activated (after the animation
     * completes). If the accordion was previously collapsed, ui.oldHeader
     * and ui.oldPanel will be empty jQuery objects. If the accordion is
     * collapsing, ui.newHeader and ui.newPanel will be empty jQuery objects.
     * Note: Since the activated event is only fired on panel activation, it
     * is not fired for the initial panel when the accordion widget is
     * created. If you need a hook for widget creation, use the creation event.
     *
     * 	* event Type: Event
     *
     * 	* ui Type: Object
     *
     * 	* newHeader Type: jQuery The header that was just activated.
     * 	* oldHeader Type: jQuery The header that was just deactivated.
     * 	* newPanel Type: jQuery The panel that was just activated.
     * 	* oldPanel Type: jQuery The panel that was just deactivated.
     *
     *
     */
    class AccordionActivate extends EventBase
    {
        const string EVENT_NAME = 'accordionactivate';
    }