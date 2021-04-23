<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Control;

use QCubed as Q;
use \QCubed\Exception\Caller;
use QCubed\Html;
use QCubed\Js\Closure;
use QCubed\QString;

/**
 * Class QControlProxy is used to 'proxy' the actions for another control
 * @was QControlProxy
 */
class Proxy extends Q\Project\Control\ControlBase
{
    /** @var bool Overriding parent class */
    protected $blnActionsMustTerminate = true;
    /** @var bool Overriding parent class */
    protected $blnScriptsOnly = true;
    /** @var null Overriding parent class to turn off rendering of this control when auto-rendering */
    protected $strPreferredRenderMethod = null;

    /**
     * Constructor Method
     *
     * @param ControlBase|FormBase $objParent Parent control
     * @param null|string $strControlId Control ID for this control
     *
     */
    public function __construct($objParent, $strControlId = null)
    {
        parent::__construct($objParent, $strControlId);
        $this->mixActionParameter = new Closure('return $j(this).data("qap")');
    }

    /**
     * @throws \QCubed\Exception\Caller
     */
    public function getControlHtml()
    {
        throw new Caller('QControlProxies cannot be rendered.  Use RenderAsEvents() within an HTML tag.');
    }

    /**
     * Render as an HTML link (anchor tag)
     *
     * @param string $strLabel Text to link to
     * @param string|null $strActionParameter Action parameter for this rendering of the control. Will be sent to the ActionParameter of the action.
     * @param null|array $attributes Array of attributes to add to the tag for the link.
     * @param string $strTag Tag to use. Defaults to 'a'.
     * @param bool $blnHtmlEntities True to render the label with html_entities.
     *
     * @return string
     */
    public function renderAsLink(
        $strLabel,
        $strActionParameter = null,
        $attributes = [],
        $strTag = 'a',
        $blnHtmlEntities = true
    ) {
        if (!$attributes) {
            $attributes = [];
        }
        if (!$strTag) {
            $strTag = 'a';
        }
        $defaults['href'] = 'javascript:;';
        $defaults['data-qpxy'] = $this->strControlId;
        if ($strActionParameter) {
            $defaults['data-qap'] = $strActionParameter;
        }
        $attributes = array_merge($defaults, $attributes); // will only apply defaults that are not in attributes

        if ($blnHtmlEntities) {
            $strLabel = QString::htmlEntities($strLabel);
        }

        return Html::renderTag($strTag, $attributes, $strLabel);
    }

    /**
     * Render as an HTML button.
     *
     * @param string $strLabel Text to link to
     * @param string|null $strActionParameter Action parameter for this rendering of the control. Will be sent to the ActionParameter of the action.
     * @param array $attributes Array of attributes to add to the tag for the link.
     * @param bool $blnHtmlEntities False to turn off html entities.
     *
     * @return string
     */
    public function renderAsButton($strLabel, $strActionParameter = null, $attributes = [], $blnHtmlEntities = true)
    {
        $defaults['onclick'] = 'return false';
        $defaults['type'] = 'button';
        $attributes = array_merge($defaults, $attributes); // will only apply defaults that are not in attributes
        return $this->renderAsLink($strLabel, $strActionParameter, $attributes, 'button', $blnHtmlEntities);
    }

    /**
     * Render just attributes that can be included in any html tag to attach the proxy to the tag.
     *
     * @param string|null $strActionParameter
     * @return string
     */
    public function renderAttributes($strActionParameter = null)
    {
        $attributes['data-qpxy'] = $this->ControlId;
        if ($strActionParameter) {
            $attributes['data-qap'] = $strActionParameter;
        }
        return Html::renderHtmlAttributes($attributes);
    }

    /**
     * Renders all the actions for a particular event as javascripts.
     *
     * @param string $strEventType
     * @return string
     */
    public function renderAsScript($strEventType = 'QClickEvent')
    {
        $objActions = $this->getAllActions($strEventType);
        $strToReturn = '';
        foreach ($objActions as $objAction) {
            $strToReturn .= $objAction->renderScript($this);
        }
        return $strToReturn;
    }

    /**
     * Parses postback data
     *
     * In this class, the method does nothing and is here because of the contraints (derived from an abstract class)
     */
    public function parsePostData()
    {
    }

    /**
     * Validates this control proxy
     *
     * @return bool Whether this control proxy is valid or not
     */
    public function validate()
    {
        return true;
    }

    // Note: TargetControlId was deprecated in 3.0 and is removed in 4.0
}