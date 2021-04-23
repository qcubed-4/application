<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Jqui;

use QCubed\Exception\InvalidCast;
use QCubed\Html;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class AccordionBase
 *
 * The QAccordionBase class defined here provides an interface between the generated
 * QAccordianGen class, and QCubed. This file is part of the core and will be overwritten
 * when you update QCubed. To override, make your changes to the QAccordion.class.php file instead.
 *
 * An accordion is a series of panels, only one of which is shown at a time. Each panel has a trigger, and
 * when the user clicks on the trigger, its corresponding panel is shown, and the others hidden.
 *
 * The Accordion descends from QPanel. There are a number of ways to create an Accordion,
 * but the basics are that you put a series of block level items inside the Accordion (like divs, or h1, QPanels, etc.)
 * and it will automatically pick the first item as the header and the second item as the content that will be collapsed
 * or expanded, and will repeat that until the end of the Accordion block.
 *
 * If you want more control, you can assign a jQuery selector to the Header item and that selector
 * will be used to find the headers within the Accordion. In this case, the next block level sibling to
 * the header will be used as the content for that header. For example, to use all the items with class ItemHeader
 * inside the Accordion panel as the headers for the accordion, do this:
 *
 * <code>$accordion->Header = '.ItemHeader';</code>
 *
 * To get or set the index of the item that is currently open, use the inherited <code>->Active</code> value.
 *
 * The Accordion will generate a QChangeEvent when a new header is selected.
 *
 * See the jQuery UI documentation for additional events, methods and options that may be useful.
 *
 * @link http://jqueryui.com/accordion/
 * @was QAccordionBase
 * @package QCubed\Control
 */

class AccordionBase extends AccordionGen
{
    /** @var bool Should the children be rendered automatically? */
    protected $blnAutoRenderChildren = true;

    /**
     * Rendered the children of this control
     * @param bool $blnDisplayOutput Send the output to client?
     *
     * @return null|string
     */
    protected function renderChildren($blnDisplayOutput = true)
    {
        $strToReturn = "";

        foreach ($this->getChildControls() as $objControl) {
            if (!$objControl->Rendered) {
                $renderMethod = $objControl->strPreferredRenderMethod;
                $strToReturn .= Html::renderTag('div', null, $objControl->$renderMethod(false));
            }
        }

        if ($blnDisplayOutput) {
            print($strToReturn);
            return null;
        } else {
            return $strToReturn;
        }
    }

    /**
     * Returns the Javascript needed as the part of control's behavior
     */
    protected function makeJqWidget()
    {
        parent::makeJqWidget();

        Application::executeJsFunction('qcubed.accordion', $this->getJqControlId(), Application::PRIORITY_HIGH);
    }

    /**
     * Returns the state data to restore later.
     * @return mixed
     */
    protected function getState()
    {
        return ['active' => $this->Active];
    }

    /**
     * Restore the state of the control.
     * @param mixed $state
     */
    protected function putState($state)
    {
        if (isset($state['active'])) {
            $this->Active = $state['active'];
        }
    }


    /**
     * PHP __set magic method implementation
     *
     * @param string $strName Name of the property
     * @param string $mixValue Value of the property
     *
     * @return mixed|void
     * @throws InvalidCast
     */
    public function __set($strName, $mixValue)
    {
        switch ($strName) {
            case '_SelectedIndex': // Internal Only. Used by JS above. Do Not Call.
                try {
                    $this->mixActive = Type::cast($mixValue,
                        Type::INTEGER);    // will cause ->Active getter to always return index of content item that is currently active
                } catch (InvalidCast $objExc) {
                    try {
                        $this->mixActive = Type::cast($mixValue, Type::BOOLEAN);
                    } catch (InvalidCast $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                }
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }
}
