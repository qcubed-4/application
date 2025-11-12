<?php

    use QCubed\Control\Panel;
    use QCubed\Project\Control\ControlBase;
    use QCubed\Project\Control\FormBase;

    /**
     * Class NavPanel
     *
     * This is a basic starting navigation panel that appears at the top of every list form.
     * This particular panel just loads a template to navigate back to the main
     * form list. You can modify this, however, if you want to suit your application.
     * A list of links, button bar, or a menu bar are possibilities.
     */
    class NavPanel extends Panel
    {
        /**
         * @param ControlBase|FormBase $objParent
         * @param null|string $strControlId
         *
         * @throws \QCubed\Exception\Caller
         */
        public function __construct (ControlBase|FormBase $objParent, ?string $strControlId = null) {
            parent::__construct($objParent, $strControlId);
            $this->strTemplate = realpath(dirname(__FILE__)) . '/nav_panel.tpl.php';
        }
    }