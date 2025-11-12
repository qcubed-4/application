<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed;

    use QCubed\Exception\Caller;
    use QCubed\Project\HtmlAttributeManager;

    /**
     * Class Tag
     *
     * Code that encapsulates the rendering of an HTML tag. This can be used to render simple tags without the overhead
     * of the QControl mechanism.
     *
     * This class outputs the HTML for one HTML tag, including the attributes inside the tag and the inner HTML between the
     * opening and closing tags. If it represents a void element, which is self-closing, no inner HTML or closing tag
     * will be printed, and the tag will be correctly terminated.
     *
     * It will normally print the opening and closing tags on their own lines, with the inner HTML indented once and in-between
     * the two tags. If you define the QCUBED_MINIMIZE constant or set Application::minimize(), it will all be printed on one line with no indents.
     *
     * This control can be used as a drawing aid for drawing complex controls.
     *
     * @was QTag
     * @package QCubed
     */
    class Tag extends HtmlAttributeManager
    {

        /** @var  string The tag */
        protected string $strTag;
        /** @var  bool True to render without a closing tag or inner HTML */
        protected bool $blnIsVoidElement = false;

        /**
         * @param null|string $strTag
         * @param bool $blnIsVoidElement
         *
         * @throws Caller
         */

        public function __construct(?string $strTag = null, bool $blnIsVoidElement = false)
        {
            if ($strTag) {
                $this->strTag = $strTag;
            } elseif (!isset($this->strTag)) {
                throw new Caller('Must set a tag either with a subclass or constructor');
            }
            $this->blnIsVoidElement = $blnIsVoidElement;
        }

        /**
         * Render the tag and everything between the opening and closing tags. Does this in two modes:
         * - Developer mode (default) will put the opening and closing tags on separate lines, with the
         *   innerHtml indented in between them.
         * - Minimize mode (set the QCUBED_MINIMIZE global constant) will put everything on one line and draw a little faster.
         *
         * @param bool $blnDisplayOutput
         * @param null|string $strInnerText
         * @param null|array $attributeOverrides
         * @param null|array $styleOverrides
         *
         * @return string
         */
        protected function render(
            bool    $blnDisplayOutput = true,
            ?string $strInnerText = null,
            ?array   $attributeOverrides = null,
            ?array $styleOverrides = null
        ): string
        {
            if (is_null($strInnerText)) {
                $strInnerText = $this->getInnerHtml();
            }
            $strOut = $this->renderTag($this->strTag,
                $attributeOverrides,
                $styleOverrides,
                $strInnerText,
                $this->blnIsVoidElement);

            if ($blnDisplayOutput) {
                print($strOut);
                return '';
            } else {
                return $strOut;
            }
        }

        /**
         * Returns the HTML that sits between the tags. Do NOT escape the HTML, that will be handled at render time.
         *
         * This implementation just returns nothing to allow for subclassing. Future implementations could implement
         * a callback or store text internally.
         *
         * @return string
         */
        protected function getInnerHtml(): string
        {
            return '';
        }
    }
