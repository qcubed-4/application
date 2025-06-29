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
use QCubed\Exception\Caller;
use QCubed\Html;
use QCubed\Js\Closure;
use QCubed\QString;

/**
 * Class QControlProxy is used to 'proxy' the actions for another control
 */
class Proxy extends Q\Project\Control\ControlBase
{
    /** @var bool Overriding parent class */
    protected bool $blnActionsMustTerminate = true;
    /** @var bool Overriding parent class */
    protected bool $blnScriptsOnly = true;
    /** @var string Overriding parent class to turn off rendering of this control when auto-rendering */
    protected string $strPreferredRenderMethod = '';

    /**
     * Constructor Method
     *
     * @param ControlBase|FormBase $objParent Parent control
     * @param string|null $strControlId Control ID for this control
     *
     * @throws Caller
     */
    public function __construct(FormBase|ControlBase $objParent, ?string $strControlId = null)
    {
        parent::__construct($objParent, $strControlId);
        $this->mixActionParameter = new Closure('return $j(this).data("qap")');
    }

    /**
     * Retrieves the HTML representation of the control.
     *
     * @return string
     * @throws Caller
     */
    public function getControlHtml(): string
    {
        throw new Caller('QControlProxies cannot be rendered.  Use RenderAsEvents() within an HTML tag.');
    }

    /**
     * Render as an HTML link (anchor tag)
     *
     * @param string $strLabel Text to link to
     * @param string|null $strActionParameter Action parameter for this rendering of the control. Will be sent to the ActionParameter of the action.
     * @param array|null $attributes Array of attributes to add to the tag for the link.
     * @param string $strTag Tag to use. Defaults to 'a'.
     * @param bool $blnHtmlEntities True to render the label with HTML entities.
     *
     * @return string
     */
    public function renderAsLink(
        string  $strLabel,
        ?string $strActionParameter = null,
        array  $attributes = [],
        string  $strTag = 'a',
        bool $blnHtmlEntities = true
    ): string
    {
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
     * @param bool $blnHtmlEntities False to turn off HTML entities.
     *
     * @return string
     */
    public function renderAsButton(string $strLabel, ?string $strActionParameter = null, array $attributes = [], bool $blnHtmlEntities = true): string
    {
        $defaults['onclick'] = 'return false';
        $defaults['type'] = 'button';
        $attributes = array_merge($defaults, $attributes); // will only apply defaults that are not in attributes
        return $this->renderAsLink($strLabel, $strActionParameter, $attributes, 'button', $blnHtmlEntities);
    }

    /**
     * Render just attributes that can be included in any HTML tag to attach the proxy to the tag.
     *
     * @param string|null $strActionParameter
     * @return string
     */
    public function renderAttributes(?string $strActionParameter = null): string
    {
        $attributes['data-qpxy'] = $this->ControlId;
        if ($strActionParameter) {
            $attributes['data-qap'] = $strActionParameter;
        }
        return Html::renderHtmlAttributes($attributes);
    }

    /**
     * Renders all the actions for a particular event as JavaScripts.
     *
     * @param string $strEventType
     * @return string
     */
    public function renderAsScript(string $strEventType = 'Click'): string
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
     * In this class, the method does nothing and is here because of the constraints (derived from an abstract class)
     */
    public function parsePostData(): void
    {
    }

    /**
     * Validates this control proxy
     *
     * @return bool Whether this control proxy is valid or not
     */
    public function validate(): bool
    {
        return true;
    }

    // Note: TargetControlId was deprecated in 3.0 and is removed in 4.0
}